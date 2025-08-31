<?php

namespace MauticPlugin\MauticPostmarkBundle\EventListener;

use Doctrine\DBAL\Connection;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticPostmarkBundle\Event\PostmarkEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostmarkConditionSubscriber implements EventSubscriberInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            PostmarkEvents::ON_CAMPAIGN_CONDITION => ['onCampaignCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event): void
    {
        // Add Postmark conditions
        $event->addCondition(
            'postmark.delivered',
            [
                'label'         => 'mautic.postmark.campaign.condition.delivered',
                'description'   => 'mautic.postmark.campaign.condition.delivered_descr',
                'eventName'     => PostmarkEvents::ON_CAMPAIGN_CONDITION,
            ]
        );

        $event->addCondition(
            'postmark.opened',
            [
                'label'         => 'mautic.postmark.campaign.condition.opened',
                'description'   => 'mautic.postmark.campaign.condition.opened_descr',
                'eventName'     => PostmarkEvents::ON_CAMPAIGN_CONDITION,
            ]
        );

        $event->addCondition(
            'postmark.clicked',
            [
                'label'         => 'mautic.postmark.campaign.condition.clicked',
                'description'   => 'mautic.postmark.campaign.condition.clicked_descr',
                'eventName'     => PostmarkEvents::ON_CAMPAIGN_CONDITION,
            ]
        );

        $event->addCondition(
            'postmark.bounced',
            [
                'label'         => 'mautic.postmark.campaign.condition.bounced',
                'description'   => 'mautic.postmark.campaign.condition.bounced_descr',
                'eventName'     => PostmarkEvents::ON_CAMPAIGN_CONDITION,
            ]
        );

        $event->addCondition(
            'postmark.spam_complaint',
            [
                'label'         => 'mautic.postmark.campaign.condition.spam_complaint',
                'description'   => 'mautic.postmark.campaign.condition.spam_complaint_descr',
                'eventName'     => PostmarkEvents::ON_CAMPAIGN_CONDITION,
            ]
        );

        $event->addCondition(
            'postmark.delivery_status',
            [
                'label'           => 'mautic.postmark.campaign.condition.delivery_status',
                'description'     => 'mautic.postmark.campaign.condition.delivery_status_descr',
                'eventName'       => PostmarkEvents::ON_CAMPAIGN_CONDITION,
                'formType'        => \Mautic\CoreBundle\Form\Type\TextType::class,
                'formTypeOptions' => [
                    'label' => 'mautic.postmark.campaign.condition.delivery_status.value',
                    'attr'  => [
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.postmark.campaign.condition.delivery_status.tooltip',
                    ],
                ],
            ]
        );
    }

    public function onCampaignCondition(CampaignExecutionEvent $event)
    {
        $eventDetails = $event->getEventDetails();
        $eventType = $eventDetails->getType();
        $config = $event->getConfig();
        $lead = $event->getLead();
        
        $leadId = $lead->getId();
        $currentCampaignId = $eventDetails->getCampaign()->getId();
        $currentEventId = $eventDetails->getId();

        // Find the previous Postmark action in the same campaign flow
        $previousPostmarkEvent = $this->findPreviousPostmarkEventInFlow($currentCampaignId, $currentEventId, $leadId);

        if (!$previousPostmarkEvent) {
            return $event->setResult(false);
        }

        $conditionMet = false;

        switch ($eventType) {
            case 'postmark.delivered':
                $conditionMet = (bool) $previousPostmarkEvent['postmark_delivered'];
                break;

            case 'postmark.opened':
                $conditionMet = (bool) $previousPostmarkEvent['postmark_opened'];
                break;

            case 'postmark.clicked':
                $conditionMet = (bool) $previousPostmarkEvent['postmark_clicked'];
                break;

            case 'postmark.bounced':
                $conditionMet = (bool) $previousPostmarkEvent['postmark_bounced'];
                break;

            case 'postmark.spam_complaint':
                $conditionMet = (bool) $previousPostmarkEvent['postmark_spam_complaint'];
                break;

            case 'postmark.delivery_status':
                $expectedStatus = $config['delivery_status'] ?? '';
                $actualStatus = $previousPostmarkEvent['postmark_delivery_status'] ?? '';
                $conditionMet = ($actualStatus === $expectedStatus);
                break;
        }

        return $event->setResult($conditionMet);
    }

    /**
     * Find the previous Postmark action event in the same campaign flow for this contact
     */
    private function findPreviousPostmarkEventInFlow(int $campaignId, int $currentEventId, int $leadId): ?array
    {
        // First, get the campaign structure to understand the flow order
        $campaignEventsQb = $this->connection->createQueryBuilder();
        $campaignEventsQb->select('id', 'type', 'event_order')
                        ->from(MAUTIC_TABLE_PREFIX.'campaign_events', 'ce')
                        ->where('ce.campaign_id = :campaignId')
                        ->andWhere('ce.channel = :channel')
                        ->andWhere('ce.type = :actionType')
                        ->andWhere('ce.event_order < (SELECT event_order FROM '.MAUTIC_TABLE_PREFIX.'campaign_events WHERE id = :currentEventId)')
                        ->setParameter('campaignId', $campaignId)
                        ->setParameter('channel', 'postmark')
                        ->setParameter('actionType', 'action')
                        ->setParameter('currentEventId', $currentEventId)
                        ->orderBy('ce.event_order', 'DESC')
                        ->setMaxResults(1);

        $previousEvent = $campaignEventsQb->executeQuery()->fetchAssociative();

        if (!$previousEvent) {
            return null;
        }

        // Now find the log entry for this contact and this previous event
        $logQb = $this->connection->createQueryBuilder();
        $logQb->select('*')
             ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'log')
             ->where('log.lead_id = :leadId')
             ->andWhere('log.campaign_id = :campaignId')
             ->andWhere('log.event_id = :eventId')
             ->andWhere('log.channel = :channel')
             ->setParameter('leadId', $leadId)
             ->setParameter('campaignId', $campaignId)
             ->setParameter('eventId', $previousEvent['id'])
             ->setParameter('channel', 'postmark')
             ->orderBy('log.date_triggered', 'DESC')
             ->setMaxResults(1);

        $result = $logQb->executeQuery()->fetchAssociative();
        
        return $result ?: null;
    }
}