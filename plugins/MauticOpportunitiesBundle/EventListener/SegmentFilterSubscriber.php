<?php

namespace MauticPlugin\MauticOpportunitiesBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\SegmentDictionaryGenerationEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Provider\TypeOperatorProviderInterface;
use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
use MauticPlugin\MauticOpportunitiesBundle\Segment\Query\Filter\OpportunityFieldFilterQueryBuilder;
use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SegmentFilterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TypeOperatorProviderInterface $typeOperatorProvider,
        private TranslatorInterface $translator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LeadEvents::LIST_FILTERS_CHOICES_ON_GENERATE => [
                ['onGenerateSegmentFiltersAddOpportunityFields', -10],
            ],
            LeadEvents::SEGMENT_DICTIONARY_ON_GENERATE => [
                ['onSegmentDictionaryGenerate', 0],
            ],
        ];
    }

    public function onGenerateSegmentFiltersAddOpportunityFields(LeadListFiltersChoicesEvent $event): void
    {
        if (!$event->isForSegmentation()) {
            return;
        }

        $choices = [
            'opportunity_name' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_name'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'opportunity_stage' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_stage'),
                'properties' => [
                    'type' => 'select',
                    'list' => Opportunity::getStageChoices(),
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
            'opportunity_amount' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_amount'),
                'properties' => ['type' => 'number'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('number'),
                'object'     => 'lead',
            ],
            'opportunity_external_id' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_external_id'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'opportunity_suitecrm_id' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_suitecrm_id'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'opportunity_abstract_review_result_url' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_abstract_review_result_url'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('url'),
                'object'     => 'lead',
            ],
            'opportunity_invoice_url' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_invoice_url'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('url'),
                'object'     => 'lead',
            ],
            'opportunity_invitation_url' => [
                'label'      => $this->translator->trans('mautic.opportunities.segment.opportunity_invitation_url'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('url'),
                'object'     => 'lead',
            ],
        ];

        foreach ($choices as $alias => $fieldOptions) {
            $event->addChoice('Opportunity', $alias, $fieldOptions);
        }
    }

    public function onSegmentDictionaryGenerate(SegmentDictionaryGenerationEvent $event): void
    {
        // Use custom OpportunityFieldFilterQueryBuilder for opportunity segment filters
        $opportunityFields = [
            'opportunity_name', 'opportunity_stage', 'opportunity_amount',
            'opportunity_external_id', 'opportunity_suitecrm_id', 
            'opportunity_abstract_review_result_url', 'opportunity_invoice_url',
            'opportunity_invitation_url'
        ];

        foreach ($opportunityFields as $fieldName) {
            $config = [
                'type' => OpportunityFieldFilterQueryBuilder::getServiceId(),
                'field' => $fieldName,
            ];

            // Add null_value for amount field to handle empty values properly
            if ($fieldName === 'opportunity_amount') {
                $config['null_value'] = 0;
            }

            $event->addTranslation($fieldName, $config);
        }
    }
}