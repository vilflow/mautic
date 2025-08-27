<?php

namespace MauticPlugin\MauticOpportunitiesBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticOpportunitiesBundle\Entity\OpportunityContactRepository;
use MauticPlugin\MauticOpportunitiesBundle\MauticOpportunitiesEvents;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityStageConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityAmountConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityExternalIdConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunitySuitecrmIdConditionType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private OpportunityContactRepository $opportunityContactRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event): void
    {
        // Opportunity Stage Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_stage',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_stage_descr',
            'formType'    => OpportunityStageConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_opportunity_stage', $condition);

        // Opportunity Amount Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_amount',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_amount_descr',
            'formType'    => OpportunityAmountConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_opportunity_amount', $condition);

        // Opportunity External ID Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_external_id',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_external_id_descr',
            'formType'    => OpportunityExternalIdConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_opportunity_external_id', $condition);

        // Opportunity SuiteCRM ID Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_suitecrm_id',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_suitecrm_id_descr',
            'formType'    => OpportunitySuitecrmIdConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_opportunity_suitecrm_id', $condition);
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event): void
    {
        $lead = $event->getLead();
        if (!$lead || !$lead->getId()) {
            $event->setResult(false);
            return;
        }

        $config = $event->getConfig();

        // Opportunity Stage Condition
        if ($event->checkContext('opportunities.has_opportunity_stage')) {
            $stage = $config['stage'] ?? '';
            $operator = $config['operator'] ?? 'eq';
            if (empty($stage)) {
                $event->setResult(false);
                return;
            }
            
            $hasOpportunity = $this->opportunityContactRepository->contactHasOpportunityByStage(
                $lead->getId(), 
                $operator, 
                $stage
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity Amount Condition
        if ($event->checkContext('opportunities.has_opportunity_amount')) {
            $amount = $config['amount'] ?? 0;
            $operator = $config['operator'] ?? 'eq';
            if (!is_numeric($amount)) {
                $event->setResult(false);
                return;
            }
            
            $hasOpportunity = $this->opportunityContactRepository->contactHasOpportunityByAmount(
                $lead->getId(), 
                $operator, 
                (float) $amount
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity External ID Condition
        if ($event->checkContext('opportunities.has_opportunity_external_id')) {
            $externalId = $config['external_id'] ?? '';
            $operator = $config['operator'] ?? 'eq';
            if (empty($externalId)) {
                $event->setResult(false);
                return;
            }
            
            $hasOpportunity = $this->opportunityContactRepository->contactHasOpportunityByExternalId(
                $lead->getId(), 
                $operator, 
                $externalId
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity SuiteCRM ID Condition
        if ($event->checkContext('opportunities.has_opportunity_suitecrm_id')) {
            $suitecrmId = $config['suitecrm_id'] ?? '';
            $operator = $config['operator'] ?? 'eq';
            if (empty($suitecrmId)) {
                $event->setResult(false);
                return;
            }
            
            $hasOpportunity = $this->opportunityContactRepository->contactHasOpportunityBySuitecrmId(
                $lead->getId(), 
                $operator, 
                $suitecrmId
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Default: condition not recognized
        $event->setResult(false);
    }
}