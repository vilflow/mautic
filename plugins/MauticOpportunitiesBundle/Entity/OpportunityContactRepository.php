<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Entity;

use Doctrine\DBAL\ParameterType;
use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * @extends CommonRepository<OpportunityContact>
 */
class OpportunityContactRepository extends CommonRepository
{
    public function getTableAlias(): string
    {
        return 'oc';
    }

    /**
     * @return OpportunityContact[]
     */
    public function getAttachedContacts(Opportunity $opportunity, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('oc')
            ->join('oc.contact', 'c')
            ->where('oc.opportunity = :opportunity')
            ->setParameter('opportunity', $opportunity)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('oc.dateAdded', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function countAttachedContacts(Opportunity $opportunity): int
    {
        return (int) $this->createQueryBuilder('oc')
            ->select('COUNT(oc.id)')
            ->where('oc.opportunity = :opportunity')
            ->setParameter('opportunity', $opportunity)
            ->getQuery()->getSingleScalarResult();
    }

    public function getAttachedContactIds(Opportunity $opportunity): array
    {
        $results = $this->createQueryBuilder('oc')
            ->select('c.id')
            ->join('oc.contact', 'c')
            ->where('oc.opportunity = :opportunity')
            ->setParameter('opportunity', $opportunity)
            ->getQuery()->getScalarResult();

        return array_map('intval', array_column($results, 'id'));
    }

    public function contactHasOpportunityByExternalId(int $contactId, string $operator, string $externalId): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('externalId', trim($externalId), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.opportunityExternalId = :externalId');
                break;
            case 'neq':
                $qb->andWhere('o.opportunityExternalId != :externalId OR o.opportunityExternalId IS NULL');
                break;
            case 'like':
                $qb->andWhere('o.opportunityExternalId LIKE :externalId');
                $qb->setParameter('externalId', '%' . trim($externalId) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityByStage(int $contactId, string $operator, string $stage): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('stage', trim($stage), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.stage = :stage');
                break;
            case 'neq':
                $qb->andWhere('o.stage != :stage OR o.stage IS NULL');
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityByAmount(int $contactId, string $operator, float $amount): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('amount', $amount)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.amount = :amount');
                break;
            case 'neq':
                $qb->andWhere('o.amount != :amount OR o.amount IS NULL');
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

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityBySuitecrmId(int $contactId, string $operator, string $suitecrmId): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('suitecrmId', trim($suitecrmId), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.suitecrmId = :suitecrmId');
                break;
            case 'neq':
                $qb->andWhere('o.suitecrmId != :suitecrmId OR o.suitecrmId IS NULL');
                break;
            case 'like':
                $qb->andWhere('o.suitecrmId LIKE :suitecrmId');
                $qb->setParameter('suitecrmId', '%' . trim($suitecrmId) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}