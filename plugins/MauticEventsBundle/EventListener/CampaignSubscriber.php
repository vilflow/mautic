<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticEventsBundle\Entity\EventContactRepository;
use MauticPlugin\MauticEventsBundle\MauticEventsEvents;
use MauticPlugin\MauticEventsBundle\Form\Type\HasEventNameConditionType;
use MauticPlugin\MauticEventsBundle\Form\Type\EventCityConditionType;
use MauticPlugin\MauticEventsBundle\Form\Type\EventCountryConditionType;
use MauticPlugin\MauticEventsBundle\Form\Type\EventCurrencyConditionType;
use MauticPlugin\MauticEventsBundle\Form\Type\EventExternalIdConditionType;
use MauticPlugin\MauticEventsBundle\Form\Type\EventWebsiteConditionType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EventContactRepository $eventContactRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD         => ['onCampaignBuild', 0],
            MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event): void
    {
        // Event Name Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_name',
            'description' => 'mautic.events.campaign.condition.has_event_name_descr',
            'formType'    => HasEventNameConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.has_event_name', $condition);

        // Event City Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_city',
            'description' => 'mautic.events.campaign.condition.has_event_city_descr',
            'formType'    => EventCityConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.has_event_city', $condition);

        // Event Country Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_country',
            'description' => 'mautic.events.campaign.condition.has_event_country_descr',
            'formType'    => EventCountryConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.has_event_country', $condition);

        // Event Currency Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_currency',
            'description' => 'mautic.events.campaign.condition.has_event_currency_descr',
            'formType'    => EventCurrencyConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.has_event_currency', $condition);

        // Event External ID Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_external_id',
            'description' => 'mautic.events.campaign.condition.has_event_external_id_descr',
            'formType'    => EventExternalIdConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.has_event_external_id', $condition);

        // Event Website Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_website',
            'description' => 'mautic.events.campaign.condition.has_event_website_descr',
            'formType'    => EventWebsiteConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.has_event_website', $condition);
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event): void
    {
        $lead = $event->getLead();
        if (!$lead || !$lead->getId()) {
            $event->setResult(false);
            return;
        }

        $config = $event->getConfig();

        // Event Name Condition
        if ($event->checkContext('events.has_event_name')) {
            $eventName = $config['event_name'] ?? '';
            if (empty($eventName)) {
                $event->setResult(false);
                return;
            }
            $hasEvent = $this->eventContactRepository->contactHasEventByName($lead->getId(), $eventName);
            $event->setResult($hasEvent);
            return;
        }

        // Event City Condition
        if ($event->checkContext('events.has_event_city')) {
            $city = $config['city'] ?? '';
            if (empty($city)) {
                $event->setResult(false);
                return;
            }
            $hasEvent = $this->eventContactRepository->contactHasEventByCity($lead->getId(), 'eq', $city);
            $event->setResult($hasEvent);
            return;
        }

        // Event Country Condition
        if ($event->checkContext('events.has_event_country')) {
            $operator = $config['operator'] ?? 'eq';
            $country = $config['country'] ?? '';
            if (empty($country)) {
                $event->setResult(false);
                return;
            }
            $hasEvent = $this->eventContactRepository->contactHasEventByCountry($lead->getId(), $operator, $country);
            $event->setResult($hasEvent);
            return;
        }

        // Event Currency Condition
        if ($event->checkContext('events.has_event_currency')) {
            $currency = $config['currency'] ?? '';
            if (empty($currency)) {
                $event->setResult(false);
                return;
            }
            $hasEvent = $this->eventContactRepository->contactHasEventByCurrency($lead->getId(), 'eq', $currency);
            $event->setResult($hasEvent);
            return;
        }

        // Event External ID Condition
        if ($event->checkContext('events.has_event_external_id')) {
            $externalId = $config['external_id'] ?? '';
            if (empty($externalId)) {
                $event->setResult(false);
                return;
            }
            $hasEvent = $this->eventContactRepository->contactHasEventByExternalId($lead->getId(), 'eq', $externalId);
            $event->setResult($hasEvent);
            return;
        }

        // Event Website Condition
        if ($event->checkContext('events.has_event_website')) {
            $website = $config['website'] ?? '';
            if (empty($website)) {
                $event->setResult(false);
                return;
            }
            $hasEvent = $this->eventContactRepository->contactHasEventByWebsite($lead->getId(), 'eq', $website);
            $event->setResult($hasEvent);
            return;
        }

        // No matching condition found
        $event->setResult(false);
    }
}