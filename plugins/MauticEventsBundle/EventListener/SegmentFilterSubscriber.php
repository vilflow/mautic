<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\SegmentDictionaryGenerationEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Provider\TypeOperatorProviderInterface;
use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
use MauticPlugin\MauticEventsBundle\Segment\Query\Filter\EventFieldFilterQueryBuilder;
use MauticPlugin\MauticEventsBundle\Entity\Event;
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
                ['onGenerateSegmentFiltersAddEventFields', -10],
            ],
            LeadEvents::SEGMENT_DICTIONARY_ON_GENERATE => [
                ['onSegmentDictionaryGenerate', 0],
            ],
        ];
    }

    public function onGenerateSegmentFiltersAddEventFields(LeadListFiltersChoicesEvent $event): void
    {
        if (!$event->isForSegmentation()) {
            return;
        }

        $choices = [
            'event_name' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_name'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_city' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_city'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_country' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_country'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_currency' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_currency'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_website' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_website'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_external_id' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_external_id'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_suitecrm_id' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_suitecrm_id'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_registration_url' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_registration_url'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
        ];

        foreach ($choices as $alias => $fieldOptions) {
            $event->addChoice('Event', $alias, $fieldOptions);
        }
    }

    public function onSegmentDictionaryGenerate(SegmentDictionaryGenerationEvent $event): void
    {
        // Use custom EventFieldFilterQueryBuilder for event segment filters
        $eventFields = [
            'event_name', 'event_city', 'event_country', 'event_currency',
            'event_website', 'event_external_id', 'event_suitecrm_id', 'event_registration_url'
        ];

        foreach ($eventFields as $fieldName) {
            $event->addTranslation($fieldName, [
                'type' => EventFieldFilterQueryBuilder::getServiceId(),
                'field' => $fieldName,
            ]);
        }
    }
}