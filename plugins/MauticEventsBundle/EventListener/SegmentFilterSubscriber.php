<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\SegmentDictionaryGenerationEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Provider\TypeOperatorProviderInterface;
use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
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
        // Event Name
        $event->addTranslation('event_name', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'name',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event City
        $event->addTranslation('event_city', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'city',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event Country
        $event->addTranslation('event_country', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'country',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event Currency
        $event->addTranslation('event_currency', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'currency',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event Website
        $event->addTranslation('event_website', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'website',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event External ID
        $event->addTranslation('event_external_id', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'event_external_id',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event SuiteCRM ID
        $event->addTranslation('event_suitecrm_id', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'suitecrm_id',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);

        // Event Registration URL
        $event->addTranslation('event_registration_url', [
            'type'                => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table'       => 'event_contacts',
            'foreign_table_field' => 'contact_id',
            'table'               => 'leads',
            'table_field'         => 'id',
            'field'               => 'registration_url',
            'join_table'          => 'events',
            'join_table_column'   => 'id',
            'foreign_join_column' => 'event_id',
        ]);
    }
}