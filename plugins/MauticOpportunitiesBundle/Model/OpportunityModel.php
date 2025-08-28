<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;
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
     * Returns the contact attached to the opportunity.
     *
     * @return array{total:int,contacts:array<int,array{id:int,name:string,email:?string,city:?string,state:?string,country:?string,stage:?string,points:int,lastActive:?string}>}
     */
    public function getAttachedContacts(Opportunity $opportunity, int $page = 1, int $limit = 20): array
    {
        $contact = $opportunity->getContact();
        $contacts = [];
        
        if ($contact) {
            $contacts[] = [
                'id'         => $contact->getId(),
                'name'       => trim(($contact->getFirstname() ?? '').' '.($contact->getLastname() ?? '')) ?: $contact->getEmail(),
                'email'      => $contact->getEmail(),
                'city'       => $contact->getCity(),
                'state'      => $contact->getState(),
                'country'    => $contact->getCountry(),
                'stage'      => $contact->getStage() ? $contact->getStage()->getName() : null,
                'points'     => $contact->getPoints() ?? 0,
                'lastActive' => $contact->getLastActive() ? $contact->getLastActive()->format('Y-m-d H:i:s') : null,
            ];
        }

        return ['total' => count($contacts), 'contacts' => $contacts];
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
     * Update opportunity contact (since opportunities now have a single contact_id field).
     *
     * @param int $contactId
     */
    public function attachContact(Opportunity $opportunity, int $contactId): void
    {
        $leadRepo = $this->em->getRepository(Lead::class);
        /** @var Lead|null $lead */
        $lead = $leadRepo->find($contactId);
        
        if (null !== $lead) {
            $opportunity->setContact($lead);
            $this->em->persist($opportunity);
            $this->em->flush();
        }
    }

    public function detachContact(Opportunity $opportunity): void
    {
        $opportunity->setContact(null);
        $this->em->persist($opportunity);
        $this->em->flush();
    }
}