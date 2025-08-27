<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<Opportunity>
 */
class OpportunityRepository extends CommonRepository
{
    protected function getDefaultOrder(): array
    {
        return [
            ['o.name', 'ASC'],
        ];
    }

    public function getTableAlias(): string
    {
        return 'o';
    }

    public function findByExternalId(string $externalId): ?Opportunity
    {
        return $this->findOneBy(['opportunityExternalId' => $externalId]);
    }

    public function findByContactId(int $contactId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByEventId(int $eventId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.event = :eventId')
            ->setParameter('eventId', $eventId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStage(string $stage): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.stage = :stage')
            ->setParameter('stage', $stage)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySuitecrmId(?string $suitecrmId): ?Opportunity
    {
        if (null === $suitecrmId) {
            return null;
        }
        
        return $this->findOneBy(['suitecrmId' => $suitecrmId]);
    }
}
