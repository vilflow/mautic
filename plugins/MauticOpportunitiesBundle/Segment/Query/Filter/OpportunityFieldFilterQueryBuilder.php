<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Segment\Query\Filter;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;

class OpportunityFieldFilterQueryBuilder extends BaseFilterQueryBuilder
{
    public static function getServiceId(): string
    {
        return 'mautic.opportunities.segment.query.builder.opportunity_field';
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

        // Create subquery to find contacts with matching opportunity criteria (using direct contact_id relationship)
        $subQueryBuilder = $queryBuilder->createQueryBuilder();
        $subQueryBuilder->select($tableAlias.'_o.contact_id')
                       ->from(MAUTIC_TABLE_PREFIX.'opportunities', $tableAlias.'_o')
                       ->where($tableAlias.'_o.contact_id IS NOT NULL');

        // Map filter field names to actual column names
        $fieldColumn = $this->mapFilterFieldToColumn($filter->getField());

        switch ($filterOperator) {
            case 'empty':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->or(
                    $subQueryBuilder->expr()->isNull($tableAlias.'_o.'.$fieldColumn),
                    $subQueryBuilder->expr()->eq($tableAlias.'_o.'.$fieldColumn, $subQueryBuilder->expr()->literal(''))
                ));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notEmpty':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->and(
                    $subQueryBuilder->expr()->isNotNull($tableAlias.'_o.'.$fieldColumn),
                    $subQueryBuilder->expr()->neq($tableAlias.'_o.'.$fieldColumn, $subQueryBuilder->expr()->literal(''))
                ));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'neq':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->neq($tableAlias.'_o.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notIn':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->in($tableAlias.'_o.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notLike':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->like($tableAlias.'_o.'.$fieldColumn, $filterParametersHolder));
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
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->$filterOperator($tableAlias.'_o.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            default:
                throw new \Exception('Unknown operator "'.$filterOperator.'" for opportunity field filter');
        }

        $queryBuilder->setParametersPairs($parameters, $filterParameters);

        return $queryBuilder;
    }

    private function mapFilterFieldToColumn(string $field): string
    {
        // Map segment filter field names to actual database column names
        $fieldMap = [
            'opportunity_name' => 'name',
            'opportunity_stage' => 'stage',
            'opportunity_amount' => 'amount',
            'opportunity_external_id' => 'opportunity_external_id',
            'opportunity_suitecrm_id' => 'suitecrm_id',
            'opportunity_abstract_review_result_url' => 'abstract_review_result_url',
            'opportunity_invoice_url' => 'invoice_url',
            'opportunity_invitation_url' => 'invitation_url',
        ];

        return $fieldMap[$field] ?? $field;
    }
}