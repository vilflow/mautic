<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticEventsBundle\Entity\EventContactRepository;
use MauticPlugin\MauticEventsBundle\Entity\EventRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EventRepository $eventRepository,
        private EventContactRepository $eventContactRepository
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
            '{event.name}' => 'mautic.events.token.name',
            '{event.conference_name}' => 'mautic.events.token.conference_name',
            '{event.city}' => 'mautic.events.token.city',
            '{event.country}' => 'mautic.events.token.country',
            '{event.currency}' => 'mautic.events.token.currency',
            '{event.website}' => 'mautic.events.token.website',
            '{event.registration_url}' => 'mautic.events.token.registration_url',
            '{event.external_id}' => 'mautic.events.token.external_id',
            '{event.suitecrm_id}' => 'mautic.events.token.suitecrm_id',
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

            // Find all event tokens in the content
            if (preg_match_all('/\{event\.([\w_]+)\}/', $text, $matches)) {
                $eventContacts = $this->eventContactRepository->findBy(['contact' => $lead->getId()]);
                
                if (empty($eventContacts)) {
                    // Replace all event tokens with empty strings if no events found
                    foreach ($matches[0] as $token) {
                        $text = str_replace($token, '', $text);
                    }
                } else {
                    // Use the first event for token replacement
                    $eventContact = $eventContacts[0];
                    $event_entity = $eventContact->getEvent();
                    
                    if ($event_entity) {
                        $tokenValues = [
                            'event.name' => $event_entity->getName() ?? '',
                            'event.conference_name' => $event_entity->getName() ?? '', // Alias for name
                            'event.city' => $event_entity->getCity() ?? '',
                            'event.country' => $event_entity->getCountry() ?? '',
                            'event.currency' => $event_entity->getCurrency() ?? '',
                            'event.website' => $event_entity->getWebsite() ?? '',
                            'event.registration_url' => $event_entity->getWebsite() ?? '', // Alias for website
                            'event.external_id' => $event_entity->getEventExternalId() ?? '',
                            'event.suitecrm_id' => $event_entity->getSuitecrmId() ?? '',
                            'event.created_at' => $event_entity->getCreatedAt() ? $event_entity->getCreatedAt()->format('Y-m-d H:i:s') : '',
                            'event.updated_at' => $event_entity->getUpdatedAt() ? $event_entity->getUpdatedAt()->format('Y-m-d H:i:s') : '',
                        ];

                        foreach ($matches[0] as $index => $token) {
                            $fieldName = 'event.' . $matches[1][$index];
                            $replacement = $tokenValues[$fieldName] ?? '';
                            $text = str_replace($token, $replacement, $text);
                        }
                    } else {
                        // Replace all event tokens with empty strings if event not found
                        foreach ($matches[0] as $token) {
                            $text = str_replace($token, '', $text);
                        }
                    }
                }
            }
        }

        $event->setContent($content);
        $event->setPlainText($plainText);
    }
}