<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticEventsBundle\Entity\EventContactRepository;
use MauticPlugin\MauticEventsBundle\MauticEventsEvents;
use MauticPlugin\MauticEventsBundle\Form\Type\HasEventNameConditionType;
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
        $condition = [
            'label'       => 'mautic.events.campaign.condition.has_event_name',
            'description' => 'mautic.events.campaign.condition.has_event_name_descr',
            'formType'    => HasEventNameConditionType::class,
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];

        $event->addCondition('events.has_event_name', $condition);
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event): void
    {
        if (!$event->checkContext('events.has_event_name')) {
            return;
        }

        $lead = $event->getLead();
        if (!$lead || !$lead->getId()) {
            $event->setResult(false);
            return;
        }

        $config = $event->getConfig();
        $eventName = $config['event_name'] ?? '';

        if (empty($eventName)) {
            $event->setResult(false);
            return;
        }

        $hasEvent = $this->eventContactRepository->contactHasEventByName($lead->getId(), $eventName);
        
        $event->setResult($hasEvent);
    }
}