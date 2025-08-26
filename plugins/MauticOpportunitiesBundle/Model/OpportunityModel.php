<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticOpportunitiesBundle\Entity\OpportunityContact;
use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;
use MauticPlugin\MauticOpportunitiesBundle\Entity\OpportunityContactRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityType;
use Symfony\Component\Form\FormInterface;

/**
 * @extends FormModel<Opportunity>
 */
class OpportunityModel extends FormModel
{
    public function __construct(
        EntityManagerInterface $em,
        CorePermissions $security,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        Translator $translator,
        UserHelper $userHelper,
        LoggerInterface $mauticLogger,
        CoreParametersHelper $coreParametersHelper
    ) {
        parent::__construct($em, $security, $dispatcher, $router, $translator, $userHelper, $mauticLogger, $coreParametersHelper);
    }

    public function getActionRouteBase(): string
    {
        return 'opportunity';
    }

    public function getPermissionBase(): string
    {
        return 'opportunities:opportunities';
    }

    /**
     * @param mixed       $entity
     * @param string|null $action
     * @param array       $options
     */
    public function createForm($entity, FormFactoryInterface $formFactory, $action = null, $options = []): FormInterface
    {
        if (!$entity instanceof Opportunity) {
            throw new MethodNotAllowedHttpException(['Opportunity']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(OpportunityType::class, $entity, $options);
    }

    /**
     * @return \MauticPlugin\MauticOpportunitiesBundle\Entity\OpportunityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Opportunity::class);
    }

    /**
     * @param int|null $id
     */
    public function getEntity($id = null): ?Opportunity
    {
        if (null === $id) {
            return new Opportunity();
        }

        return parent::getEntity($id);
    }

    /**
     * Returns contacts attached to opportunity.
     *
     * @return array{total:int,contacts:array<int,array{id:int,name:string,email:?string,city:?string,state:?string,country:?string,stage:?string,points:int,lastActive:?string}}>
     */
    public function getAttachedContacts(Opportunity $opportunity, int $page = 1, int $limit = 20): array
    {
        /** @var OpportunityContactRepository $repo */
        $repo   = $this->em->getRepository(OpportunityContact::class);
        $offset = ($page - 1) * $limit;
        $rows   = $repo->getAttachedContacts($opportunity, $limit, $offset);
        $total  = $repo->countAttachedContacts($opportunity);

        $contacts = [];
        foreach ($rows as $row) {
            $lead       = $row->getContact();
            $contacts[] = [
                'id'         => $lead->getId(),
                'name'       => trim(($lead->getFirstname() ?? '').' '.($lead->getLastname() ?? '')) ?: $lead->getEmail(),
                'email'      => $lead->getEmail(),
                'city'       => $lead->getCity(),
                'state'      => $lead->getState(),
                'country'    => $lead->getCountry(),
                'stage'      => $lead->getStage() ? $lead->getStage()->getName() : null,
                'points'     => $lead->getPoints() ?? 0,
                'lastActive' => $lead->getLastActive() ? $lead->getLastActive()->format('Y-m-d H:i:s') : null,
            ];
        }

        return ['total' => $total, 'contacts' => $contacts];
    }

    /**
     * Search contacts globally.
     *
     * @param int[] $excludeIds
     *
     * @return array<int,array{id:int,name:string,email:?string}>
     */
    public function searchContacts(string $term, array $excludeIds = [], int $page = 1, int $limit = 10): array
    {
        $qb = $this->em->getRepository(Lead::class)->createQueryBuilder('l');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like('l.email', ':term'),
                $qb->expr()->like('l.phone', ':term'),
                $qb->expr()->like('l.mobile', ':term'),
                $qb->expr()->like("CONCAT(l.firstname, ' ', l.lastname)", ':term')
            )
        );

        if (!empty($excludeIds)) {
            $qb->andWhere($qb->expr()->notIn('l.id', ':exclude'))
               ->setParameter('exclude', $excludeIds);
        }

        $qb->setParameter('term', '%'.$term.'%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('l.firstname', 'ASC');

        $leads = $qb->getQuery()->getResult();

        $results = [];
        foreach ($leads as $lead) {
            if (!$lead instanceof Lead) {
                continue;
            }

            $results[] = [
                'id'    => $lead->getId(),
                'name'  => trim(($lead->getFirstname() ?? '').' '.($lead->getLastname() ?? '')) ?: $lead->getEmail(),
                'email' => $lead->getEmail(),
            ];
        }

        return $results;
    }

    /**
     * @param int[] $contactIds
     */
    public function attachContacts(Opportunity $opportunity, array $contactIds): void
    {
        $repo = $this->em->getRepository(OpportunityContact::class);

        $existing = $repo->getAttachedContactIds($opportunity);
        $toAttach = array_diff($contactIds, $existing);

        if (empty($toAttach)) {
            return;
        }

        $leadRepo = $this->em->getRepository(Lead::class);
        foreach ($toAttach as $id) {
            /** @var Lead|null $lead */
            $lead = $leadRepo->find($id);
            if (null === $lead) {
                continue;
            }

            $link = new OpportunityContact();
            $link->setOpportunity($opportunity);
            $link->setContact($lead);
            $link->setDateAdded(new \DateTimeImmutable());
            $this->em->persist($link);
        }

        $this->em->flush();
    }

    public function detachContact(Opportunity $opportunity, int $contactId): void
    {
        $repo = $this->em->getRepository(OpportunityContact::class);
        $qb   = $repo->createQueryBuilder('oc');
        $qb->where('oc.opportunity = :opportunity')
            ->andWhere('oc.contact = :contact')
            ->setParameters([
                'opportunity' => $opportunity,
                'contact'     => $contactId,
            ]);

        $link = $qb->getQuery()->getOneOrNullResult();
        if (null !== $link) {
            $this->em->remove($link);
            $this->em->flush();
        }
    }
}