<?php

namespace MauticPlugin\MauticEventsBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticEventsBundle\Entity\EventContact;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use MauticPlugin\MauticEventsBundle\Entity\EventContactRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use MauticPlugin\MauticEventsBundle\Form\Type\EventType;
use Symfony\Component\Form\FormInterface;

/**
 * @extends FormModel<Event>
 */
class EventModel extends FormModel
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
        return 'event';
    }

    public function getPermissionBase(): string
    {
        return 'events:events';
    }

    /**
     * @param mixed       $entity
     * @param string|null $action
     * @param array       $options
     */
    public function createForm($entity, FormFactoryInterface $formFactory, $action = null, $options = []): FormInterface
    {
        if (!$entity instanceof Event) {
            throw new MethodNotAllowedHttpException(['Event']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create(EventType::class, $entity, $options);
    }

    /**
     * @return \MauticPlugin\MauticEventsBundle\Entity\EventRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository(Event::class);
    }

    /**
     * @param int|null $id
     */
    public function getEntity($id = null): ?Event
    {
        if (null === $id) {
            return new Event();
        }

        return parent::getEntity($id);
    }

    /**
     * Returns contacts attached to event.
     *
     * @return array{total:int,contacts:array<int,array{id:int,name:string,email:?string}}>
     */
    public function getAttachedContacts(Event $event, int $page = 1, int $limit = 20): array
    {
        /** @var EventContactRepository $repo */
        $repo   = $this->em->getRepository(EventContact::class);
        $offset = ($page - 1) * $limit;
        $rows   = $repo->getAttachedContacts($event, $limit, $offset);
        $total  = $repo->countAttachedContacts($event);

        $contacts = [];
        foreach ($rows as $row) {
            $lead       = $row->getContact();
            $contacts[] = [
                'id'    => $lead->getId(),
                'name'  => trim(($lead->getFirstname() ?? '').' '.($lead->getLastname() ?? '')) ?: $lead->getEmail(),
                'email' => $lead->getEmail(),
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
    public function attachContacts(Event $event, array $contactIds): void
    {
        $repo = $this->em->getRepository(EventContact::class);

        $existing = $repo->getAttachedContactIds($event);
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

            $link = new EventContact();
            $link->setEvent($event);
            $link->setContact($lead);
            $link->setDateAdded(new \DateTimeImmutable());
            $this->em->persist($link);
        }

        $this->em->flush();
    }

    public function detachContact(Event $event, int $contactId): void
    {
        $repo = $this->em->getRepository(EventContact::class);
        $qb   = $repo->createQueryBuilder('ec');
        $qb->where('ec.event = :event')
            ->andWhere('ec.contact = :contact')
            ->setParameters([
                'event'   => $event,
                'contact' => $contactId,
            ]);

        $link = $qb->getQuery()->getOneOrNullResult();
        if (null !== $link) {
            $this->em->remove($link);
            $this->em->flush();
        }
    }
}
