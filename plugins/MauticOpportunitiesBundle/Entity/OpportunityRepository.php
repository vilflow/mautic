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

    /**
     * Check if contact has opportunities matching stage criteria
     */
    public function contactHasOpportunityByStage(int $contactId, string $operator, string $stage): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.stage = :stage');
                break;
            case 'neq':
                $qb->andWhere('o.stage != :stage');
                break;
            case 'like':
                $qb->andWhere('o.stage LIKE :stage');
                $stage = '%' . $stage . '%';
                break;
            case 'not_like':
                $qb->andWhere('o.stage NOT LIKE :stage');
                $stage = '%' . $stage . '%';
                break;
        }
        
        $qb->setParameter('stage', $stage);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching amount criteria
     */
    public function contactHasOpportunityByAmount(int $contactId, string $operator, float $amount): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->andWhere('o.amount IS NOT NULL')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.amount = :amount');
                break;
            case 'neq':
                $qb->andWhere('o.amount != :amount');
                break;
            case 'gt':
                $qb->andWhere('o.amount > :amount');
                break;
            case 'gte':
                $qb->andWhere('o.amount >= :amount');
                break;
            case 'lt':
                $qb->andWhere('o.amount < :amount');
                break;
            case 'lte':
                $qb->andWhere('o.amount <= :amount');
                break;
        }
        
        $qb->setParameter('amount', $amount);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching external ID criteria
     */
    public function contactHasOpportunityByExternalId(int $contactId, string $operator, string $externalId): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.opportunityExternalId = :externalId');
                break;
            case 'neq':
                $qb->andWhere('o.opportunityExternalId != :externalId');
                break;
            case 'like':
                $qb->andWhere('o.opportunityExternalId LIKE :externalId');
                $externalId = '%' . $externalId . '%';
                break;
        }
        
        $qb->setParameter('externalId', $externalId);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching SuiteCRM ID criteria
     */
    public function contactHasOpportunityBySuitecrmId(int $contactId, string $operator, string $suitecrmId): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.suitecrmId = :suitecrmId');
                break;
            case 'neq':
                $qb->andWhere('o.suitecrmId != :suitecrmId');
                break;
            case 'like':
                $qb->andWhere('o.suitecrmId LIKE :suitecrmId');
                $suitecrmId = '%' . $suitecrmId . '%';
                break;
        }
        
        $qb->setParameter('suitecrmId', $suitecrmId);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching name criteria
     */
    public function contactHasOpportunityByName(int $contactId, string $operator, string $name): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.name = :name');
                break;
            case 'neq':
                $qb->andWhere('o.name != :name');
                break;
            case 'like':
                $qb->andWhere('o.name LIKE :name');
                $name = '%' . $name . '%';
                break;
        }
        
        $qb->setParameter('name', $name);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching event criteria
     */
    public function contactHasOpportunityByEvent(int $contactId, string $operator, int $eventId): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.event = :eventId');
                break;
            case 'neq':
                $qb->andWhere('o.event != :eventId');
                break;
        }
        
        $qb->setParameter('eventId', $eventId);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching abstract review result URL criteria
     */
    public function contactHasOpportunityByAbstractReviewResultUrl(int $contactId, string $operator, string $url): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'not_empty':
                $qb->andWhere('o.abstractReviewResultUrl IS NOT NULL')
                   ->andWhere('o.abstractReviewResultUrl != \'\'');
                break;
            case 'like':
                $qb->andWhere('o.abstractReviewResultUrl LIKE :url');
                $url = '%' . $url . '%';
                $qb->setParameter('url', $url);
                break;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching invoice URL criteria
     */
    public function contactHasOpportunityByInvoiceUrl(int $contactId, string $operator, string $url): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'not_empty':
                $qb->andWhere('o.invoiceUrl IS NOT NULL')
                   ->andWhere('o.invoiceUrl != \'\'');
                break;
            case 'like':
                $qb->andWhere('o.invoiceUrl LIKE :url');
                $url = '%' . $url . '%';
                $qb->setParameter('url', $url);
                break;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Check if contact has opportunities matching invitation URL criteria
     */
    public function contactHasOpportunityByInvitationUrl(int $contactId, string $operator, string $url): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.contact = :contactId')
            ->setParameter('contactId', $contactId);

        switch ($operator) {
            case 'not_empty':
                $qb->andWhere('o.invitationUrl IS NOT NULL')
                   ->andWhere('o.invitationUrl != \'\'');
                break;
            case 'like':
                $qb->andWhere('o.invitationUrl LIKE :url');
                $url = '%' . $url . '%';
                $qb->setParameter('url', $url);
                break;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
