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

    public function contactHasOpportunityByName(int $contactId, string $operator, string $name): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('name', trim($name), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('o.name = :name');
                break;
            case 'neq':
                $qb->andWhere('o.name != :name OR o.name IS NULL');
                break;
            case 'like':
                $qb->andWhere('o.name LIKE :name');
                $qb->setParameter('name', '%' . trim($name) . '%', ParameterType::STRING);
                break;
            case 'notlike':
                $qb->andWhere('o.name NOT LIKE :name OR o.name IS NULL');
                $qb->setParameter('name', '%' . trim($name) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityByEvent(int $contactId, string $operator, $eventId): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('IDENTITY(o.event) = :eventId')
                   ->setParameter('eventId', $eventId, ParameterType::INTEGER);
                break;
            case 'neq':
                $qb->andWhere('IDENTITY(o.event) != :eventId OR o.event IS NULL')
                   ->setParameter('eventId', $eventId, ParameterType::INTEGER);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityByAbstractReviewResultUrl(int $contactId, string $operator, string $url = ''): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setMaxResults(1);

        switch ($operator) {
            case 'empty':
                $qb->andWhere('o.abstractReviewResultUrl IS NULL OR o.abstractReviewResultUrl = \'\'');
                break;
            case 'not_empty':
                $qb->andWhere('o.abstractReviewResultUrl IS NOT NULL AND o.abstractReviewResultUrl != \'\'');
                break;
            case 'like':
                $qb->andWhere('o.abstractReviewResultUrl LIKE :url')
                   ->setParameter('url', '%' . trim($url) . '%', ParameterType::STRING);
                break;
            case 'notlike':
                $qb->andWhere('o.abstractReviewResultUrl NOT LIKE :url OR o.abstractReviewResultUrl IS NULL')
                   ->setParameter('url', '%' . trim($url) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityByInvoiceUrl(int $contactId, string $operator, string $url = ''): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setMaxResults(1);

        switch ($operator) {
            case 'empty':
                $qb->andWhere('o.invoiceUrl IS NULL OR o.invoiceUrl = \'\'');
                break;
            case 'not_empty':
                $qb->andWhere('o.invoiceUrl IS NOT NULL AND o.invoiceUrl != \'\'');
                break;
            case 'like':
                $qb->andWhere('o.invoiceUrl LIKE :url')
                   ->setParameter('url', '%' . trim($url) . '%', ParameterType::STRING);
                break;
            case 'notlike':
                $qb->andWhere('o.invoiceUrl NOT LIKE :url OR o.invoiceUrl IS NULL')
                   ->setParameter('url', '%' . trim($url) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasOpportunityByInvitationUrl(int $contactId, string $operator, string $url = ''): bool
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('1')
            ->innerJoin('oc.opportunity', 'o')
            ->andWhere('IDENTITY(oc.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setMaxResults(1);

        switch ($operator) {
            case 'empty':
                $qb->andWhere('o.invitationUrl IS NULL OR o.invitationUrl = \'\'');
                break;
            case 'not_empty':
                $qb->andWhere('o.invitationUrl IS NOT NULL AND o.invitationUrl != \'\'');
                break;
            case 'like':
                $qb->andWhere('o.invitationUrl LIKE :url')
                   ->setParameter('url', '%' . trim($url) . '%', ParameterType::STRING);
                break;
            case 'notlike':
                $qb->andWhere('o.invitationUrl NOT LIKE :url OR o.invitationUrl IS NULL')
                   ->setParameter('url', '%' . trim($url) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}