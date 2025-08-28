<?php

namespace MauticPlugin\MauticOpportunitiesBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticOpportunitiesBundle\Entity\OpportunityRepository;
use MauticPlugin\MauticOpportunitiesBundle\MauticOpportunitiesEvents;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityStageConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityAmountConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityExternalIdConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunitySuitecrmIdConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityNameConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityEventConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityAbstractReviewResultUrlConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityInvoiceUrlConditionType;
use MauticPlugin\MauticOpportunitiesBundle\Form\Type\OpportunityInvitationUrlConditionType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private OpportunityRepository $opportunityRepository
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

        // Opportunity Name Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_name',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_name_descr',
            'formType'    => OpportunityNameConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_opportunity_name', $condition);

        // Opportunity Event Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_event',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_event_descr',
            'formType'    => OpportunityEventConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_opportunity_event', $condition);

        // Opportunity Abstract Review Result URL Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_abstract_review_result_url',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_abstract_review_result_url_descr',
            'formType'    => OpportunityAbstractReviewResultUrlConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_abstract_url', $condition);

        // Opportunity Invoice URL Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_invoice_url',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_invoice_url_descr',
            'formType'    => OpportunityInvoiceUrlConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_invoice_url', $condition);

        // Opportunity Invitation URL Condition
        $condition = [
            'label'       => 'mautic.opportunities.campaign.condition.has_opportunity_invitation_url',
            'description' => 'mautic.opportunities.campaign.condition.has_opportunity_invitation_url_descr',
            'formType'    => OpportunityInvitationUrlConditionType::class,
            'eventName'   => MauticOpportunitiesEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('opportunities.has_invitation_url', $condition);
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
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByStage(
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
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByAmount(
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
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByExternalId(
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
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityBySuitecrmId(
                $lead->getId(), 
                $operator, 
                $suitecrmId
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity Name Condition
        if ($event->checkContext('opportunities.has_opportunity_name')) {
            $name = $config['name'] ?? '';
            if (empty($name)) {
                $event->setResult(false);
                return;
            }
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByName(
                $lead->getId(), 
                'like', 
                $name
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity Event Condition
        if ($event->checkContext('opportunities.has_opportunity_event')) {
            $eventId = $config['event'] ?? '';
            if (empty($eventId)) {
                $event->setResult(false);
                return;
            }
            
            // Convert string to integer if needed
            $eventId = is_numeric($eventId) ? (int) $eventId : $eventId;
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByEvent(
                $lead->getId(), 
                'eq', 
                $eventId
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity Abstract Review Result URL Condition
        if ($event->checkContext('opportunities.has_abstract_url')) {
            $url = $config['abstract_review_result_url'] ?? '';
            $operator = empty($url) ? 'not_empty' : 'like';
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByAbstractReviewResultUrl(
                $lead->getId(), 
                $operator, 
                $url
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity Invoice URL Condition
        if ($event->checkContext('opportunities.has_invoice_url')) {
            $url = $config['invoice_url'] ?? '';
            $operator = empty($url) ? 'not_empty' : 'like';
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByInvoiceUrl(
                $lead->getId(), 
                $operator, 
                $url
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Opportunity Invitation URL Condition
        if ($event->checkContext('opportunities.has_invitation_url')) {
            $url = $config['invitation_url'] ?? '';
            $operator = empty($url) ? 'not_empty' : 'like';
            
            $hasOpportunity = $this->opportunityRepository->contactHasOpportunityByInvitationUrl(
                $lead->getId(), 
                $operator, 
                $url
            );
            $event->setResult($hasOpportunity);
            return;
        }

        // Default: condition not recognized
        $event->setResult(false);
    }
}