<?php

namespace MauticPlugin\MauticOpportunitiesBundle\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticOpportunitiesBundle\Entity\OpportunityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private OpportunityRepository $opportunityRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvents::EMAIL_ON_BUILD   => ['onEmailBuild', 0],
            EmailEvents::EMAIL_ON_SEND    => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailDisplay', 0],
        ];
    }

    public function onEmailBuild(EmailBuilderEvent $event): void
    {
        $event->addTokens([
            '{opportunity.stage}' => 'mautic.opportunities.token.stage',
            '{opportunity.amount}' => 'mautic.opportunities.token.amount',
            '{opportunity.external_id}' => 'mautic.opportunities.token.external_id',
            '{opportunity.abstract_review_result_url}' => 'mautic.opportunities.token.abstract_review_result_url',
            '{opportunity.invoice_url}' => 'mautic.opportunities.token.invoice_url',
            '{opportunity.invitation_url}' => 'mautic.opportunities.token.invitation_url',
            '{opportunity.suitecrm_id}' => 'mautic.opportunities.token.suitecrm_id',
        ]);
    }

    public function onEmailGenerate(EmailSendEvent $event): void
    {
        $this->replaceTokens($event);
    }

    public function onEmailDisplay(EmailSendEvent $event): void
    {
        $this->replaceTokens($event);
    }

    private function replaceTokens(EmailSendEvent $event): void
    {
        $lead = $event->getLead();
        if (!$lead || !$lead->getId()) {
            return;
        }

        $content = $event->getContent();
        $plainText = $event->getPlainText();

        // Process both HTML and plain text content
        foreach ([$content, $plainText] as &$text) {
            if (empty($text)) {
                continue;
            }

            // Find all opportunity tokens in the content
            if (preg_match_all('/\{opportunity\.([\w_]+)\}/', $text, $matches)) {
                $opportunities = $this->opportunityRepository->findBy(['contact' => $lead->getId()]);
                
                if (empty($opportunities)) {
                    // Replace all opportunity tokens with empty strings if no opportunities found
                    foreach ($matches[0] as $token) {
                        $text = str_replace($token, '', $text);
                    }
                } else {
                    // Use the first opportunity for token replacement
                    $opportunity = $opportunities[0];
                    
                    $tokenValues = [
                        'opportunity.stage' => $opportunity->getStage() ?? '',
                        'opportunity.amount' => $opportunity->getAmount() ? number_format($opportunity->getAmount(), 2) : '',
                        'opportunity.external_id' => $opportunity->getOpportunityExternalId() ?? '',
                        'opportunity.abstract_review_result_url' => $opportunity->getAbstractReviewResultUrl() ?? '',
                        'opportunity.invoice_url' => $opportunity->getInvoiceUrl() ?? '',
                        'opportunity.invitation_url' => $opportunity->getInvitationUrl() ?? '',
                        'opportunity.suitecrm_id' => $opportunity->getSuitecrmId() ?? '',
                        'opportunity.created_at' => $opportunity->getCreatedAt() ? $opportunity->getCreatedAt()->format('Y-m-d H:i:s') : '',
                        'opportunity.updated_at' => $opportunity->getUpdatedAt() ? $opportunity->getUpdatedAt()->format('Y-m-d H:i:s') : '',
                    ];

                    foreach ($matches[0] as $index => $token) {
                        $fieldName = 'opportunity.' . $matches[1][$index];
                        $replacement = $tokenValues[$fieldName] ?? '';
                        $text = str_replace($token, $replacement, $text);
                    }
                }
            }
        }

        $event->setContent($content);
        $event->setPlainText($plainText);
    }
}