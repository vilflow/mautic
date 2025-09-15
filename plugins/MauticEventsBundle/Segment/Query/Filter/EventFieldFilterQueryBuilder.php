<?php

namespace MauticPlugin\MauticEventsBundle\Segment\Query\Filter;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;

class EventFieldFilterQueryBuilder extends BaseFilterQueryBuilder
{
    public static function getServiceId(): string
    {
        return 'mautic.events.segment.query.builder.event_field';
    }

    public function applyQuery(QueryBuilder $queryBuilder, ContactSegmentFilter $filter): QueryBuilder
    {
        $leadsTableAlias = $queryBuilder->getTableAlias(MAUTIC_TABLE_PREFIX.'leads');
        $filterOperator = $filter->getOperator();
        $filterParameters = $filter->getParameterValue();

        if (is_array($filterParameters)) {
            $parameters = [];
            foreach ($filterParameters as $filterParameter) {
                $parameters[] = $this->generateRandomParameterName();
            }
        } else {
            $parameters = $this->generateRandomParameterName();
        }

        $filterParametersHolder = $filter->getParameterHolder($parameters);
        $tableAlias = $this->generateRandomParameterName();

        // Create subquery to find contacts with matching event criteria
        $subQueryBuilder = $queryBuilder->createQueryBuilder();
        $subQueryBuilder->select($tableAlias.'_ec.contact_id')
                       ->from(MAUTIC_TABLE_PREFIX.'event_contacts', $tableAlias.'_ec')
                       ->innerJoin($tableAlias.'_ec', MAUTIC_TABLE_PREFIX.'events', $tableAlias.'_e', $tableAlias.'_ec.event_id = '.$tableAlias.'_e.id');

        // Map filter field names to actual column names
        $fieldColumn = $this->mapFilterFieldToColumn($filter->getField());

        switch ($filterOperator) {
            case 'empty':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->or(
                    $subQueryBuilder->expr()->isNull($tableAlias.'_e.'.$fieldColumn),
                    $subQueryBuilder->expr()->eq($tableAlias.'_e.'.$fieldColumn, $subQueryBuilder->expr()->literal(''))
                ));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notEmpty':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->and(
                    $subQueryBuilder->expr()->isNotNull($tableAlias.'_e.'.$fieldColumn),
                    $subQueryBuilder->expr()->neq($tableAlias.'_e.'.$fieldColumn, $subQueryBuilder->expr()->literal(''))
                ));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'neq':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->neq($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notIn':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->in($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notLike':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->like($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'eq':
            case 'like':
            case 'startsWith':
            case 'endsWith':
            case 'contains':
            case 'in':
            case 'gt':
            case 'gte':
            case 'lt':
            case 'lte':
            case 'regexp':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->$filterOperator($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            default:
                throw new \Exception('Unknown operator "'.$filterOperator.'" for event field filter');
        }

        $queryBuilder->setParametersPairs($parameters, $filterParameters);

        return $queryBuilder;
    }

    private function mapFilterFieldToColumn(string $field): string
    {
        // Map segment filter field names to actual database column names
        $fieldMap = [
            'event_name' => 'name',
            'event_city' => 'city',
            'event_country' => 'country',
            'event_currency' => 'currency',
            'event_website' => 'website',
            'event_external_id' => 'event_external_id',
            'event_suitecrm_id' => 'suitecrm_id',
            'event_registration_url' => 'registration_url',
        ];

        return $fieldMap[$field] ?? $field;
    }
}