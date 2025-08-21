<?php

namespace MauticPlugin\MauticEventsBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<EventContact>
 */
class EventContactRepository extends CommonRepository
{
    public function getTableAlias(): string
    {
        return 'ec';
    }

    /**
     * @return EventContact[]
     */
    public function getAttachedContacts(Event $event, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('ec')
            ->join('ec.contact', 'c')
            ->where('ec.event = :event')
            ->setParameter('event', $event)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('ec.dateAdded', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function countAttachedContacts(Event $event): int
    {
        return (int) $this->createQueryBuilder('ec')
            ->select('COUNT(ec.id)')
            ->where('ec.event = :event')
            ->setParameter('event', $event)
            ->getQuery()->getSingleScalarResult();
    }

    public function getAttachedContactIds(Event $event): array
    {
        $results = $this->createQueryBuilder('ec')
            ->select('c.id')
            ->join('ec.contact', 'c')
            ->where('ec.event = :event')
            ->setParameter('event', $event)
            ->getQuery()->getScalarResult();

        return array_map('intval', array_column($results, 'id'));
    }

    public function contactHasEventByName(int $contactId, string $eventName): bool
    {
        $result = $this->createQueryBuilder('ec')
            ->select('COUNT(ec.id)')
            ->join('ec.event', 'e')
            ->where('ec.contact = :contactId')
            ->andWhere('LOWER(e.name) = LOWER(:eventName)')
            ->setParameter('contactId', $contactId)
            ->setParameter('eventName', $eventName)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }
}
