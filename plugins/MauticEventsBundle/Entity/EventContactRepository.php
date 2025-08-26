<?php

namespace MauticPlugin\MauticEventsBundle\Entity;

use Doctrine\DBAL\ParameterType;
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
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->andWhere('e.name = :eventName')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('eventName', trim($eventName), ParameterType::STRING)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByCity(int $contactId, string $operator, string $city): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('city', trim($city), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.city = :city');
                break;
            case 'neq':
                $qb->andWhere('e.city != :city OR e.city IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.city LIKE :city');
                $qb->setParameter('city', '%' . trim($city) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByCountry(int $contactId, string $operator, string $country): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('country', trim($country), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.country = :country');
                break;
            case 'neq':
                $qb->andWhere('e.country != :country OR e.country IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.country LIKE :country');
                $qb->setParameter('country', '%' . trim($country) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByCurrency(int $contactId, string $operator, string $currency): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('currency', trim($currency), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.currency = :currency');
                break;
            case 'neq':
                $qb->andWhere('e.currency != :currency OR e.currency IS NULL');
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByExternalId(int $contactId, string $operator, string $externalId): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('externalId', trim($externalId), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.eventExternalId = :externalId');
                break;
            case 'neq':
                $qb->andWhere('e.eventExternalId != :externalId OR e.eventExternalId IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.eventExternalId LIKE :externalId');
                $qb->setParameter('externalId', '%' . trim($externalId) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByWebsite(int $contactId, string $operator, string $website): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('website', trim($website), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.website = :website');
                break;
            case 'neq':
                $qb->andWhere('e.website != :website OR e.website IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.website LIKE :website');
                $qb->setParameter('website', '%' . trim($website) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}
