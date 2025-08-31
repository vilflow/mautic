<?php

namespace MauticPlugin\MauticPostmarkBundle\EventListener;

use Doctrine\DBAL\Connection;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\PendingEvent;
use MauticPlugin\MauticPostmarkBundle\Event\PostmarkEvents;
use MauticPlugin\MauticPostmarkBundle\Form\Type\PostmarkSendType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    public function __construct(private Connection $connection)
    {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD        => ['onCampaignBuild', 0],
            PostmarkEvents::ON_CAMPAIGN_BATCH_ACTION => ['onCampaignTriggerPostmark', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event): void
    {
        $event->addAction(
            'postmark.send',
            [
                'label'            => 'mautic.postmark.campaign.event.send',
                'description'      => 'mautic.postmark.campaign.event.send_descr',
                'batchEventName'   => PostmarkEvents::ON_CAMPAIGN_BATCH_ACTION,
                'formType'         => PostmarkSendType::class,
                // Optional: declare channel so logs group under a channel name
                'channel'          => 'postmark',
            ]
        );

        // Add Postmark conditions
        $event->addCondition(
            'postmark.delivered',
            [
                'label'       => 'mautic.postmark.campaign.condition.delivered',
                'description' => 'mautic.postmark.campaign.condition.delivered_descr',
                'callback'    => [$this, 'onCampaignTriggerCondition'],
            ]
        );

        $event->addCondition(
            'postmark.opened',
            [
                'label'       => 'mautic.postmark.campaign.condition.opened',
                'description' => 'mautic.postmark.campaign.condition.opened_descr',
                'callback'    => [$this, 'onCampaignTriggerCondition'],
            ]
        );

        $event->addCondition(
            'postmark.clicked',
            [
                'label'       => 'mautic.postmark.campaign.condition.clicked',
                'description' => 'mautic.postmark.campaign.condition.clicked_descr',
                'callback'    => [$this, 'onCampaignTriggerCondition'],
            ]
        );

        $event->addCondition(
            'postmark.bounced',
            [
                'label'       => 'mautic.postmark.campaign.condition.bounced',
                'description' => 'mautic.postmark.campaign.condition.bounced_descr',
                'callback'    => [$this, 'onCampaignTriggerCondition'],
            ]
        );

        $event->addCondition(
            'postmark.spam_complaint',
            [
                'label'       => 'mautic.postmark.campaign.condition.spam_complaint',
                'description' => 'mautic.postmark.campaign.condition.spam_complaint_descr',
                'callback'    => [$this, 'onCampaignTriggerCondition'],
            ]
        );

        $event->addCondition(
            'postmark.delivery_status',
            [
                'label'       => 'mautic.postmark.campaign.condition.delivery_status',
                'description' => 'mautic.postmark.campaign.condition.delivery_status_descr',
                'callback'    => [$this, 'onCampaignTriggerCondition'],
                'formType'    => \Mautic\CoreBundle\Form\Type\TextType::class,
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

    public function onCampaignTriggerPostmark(PendingEvent $event): void
    {
        if (!$event->checkContext('postmark.send')) {
            return;
        }

        $config = $event->getEvent()->getProperties();

        $serverToken   = trim((string) ($config['server_token'] ?? ''));
        $fromEmail     = trim((string) ($config['from_email'] ?? ''));
        $toEmail       = trim((string) ($config['to_email'] ?? ''));
        $templateAlias = trim((string) ($config['template_alias'] ?? ''));

        // Build TemplateModel from SortableListType (like Webhook's additional_data)
        $templateModel = [];
        if (!empty($config['template_model']['list'])) {
            // parseList returns [value => label]; we want [label => value]
            $pairs         = \Mautic\CoreBundle\Helper\AbstractFormFieldHelper::parseList($config['template_model']['list']);
            $templateModel = array_flip($pairs);
        } elseif (!empty($config['template_model']) && is_array($config['template_model'])) {
            // Fallback if stored as flat key=>value pairs
            $templateModel = $config['template_model'];
        }
        // Also merge from generic 'data' bag if present (other UIs/patterns)
        if (!empty($config['data']) && is_array($config['data'])) {
            foreach ($config['data'] as $key => $value) {
                if (is_string($key)) {
                    $templateModel[$key] = $value;
                }
            }
        }

        // Channel for logs
        $event->setChannel('postmark');

        // Iterate all pending logs/contacts
        $pending  = $event->getPending();
        $contacts = $event->getContacts();

        foreach ($contacts as $logId => $contact) {
            $log = $pending->get($logId);

            // Resolve tokens in strings (basic token replacement for common contact fields)
            [$from, $to, $model] = $this->resolveTokens($fromEmail, $toEmail, $templateModel, $contact->getProfileFields());

            // Validate required and report specifics
            $missing = [];
            if (!$serverToken) {
                $missing[] = 'server_token';
            }
            if (!$from) {
                $missing[] = 'from_email';
            }
            if (!$to) {
                $missing[] = 'to_email';
            }
            if (!$templateAlias) {
                $missing[] = 'template_alias';
            }
            if ($missing) {
                $event->fail($log, 'Missing Postmark fields: '.implode(', ', $missing));
                continue;
            }

            $payload = [
                'From'          => $from,
                'To'            => $to,
                'TemplateAlias' => $templateAlias,
                'TemplateModel' => $model,
            ];

            [$ok, $statusCode, $respBody, $err] = $this->sendPostmark($serverToken, $payload);

            if (!$ok) {
                $event->fail($log, sprintf('Postmark error (%s): %s', (string) $statusCode, $err ?: $respBody));
                continue;
            }

            // Append response metadata for timeline visibility
            $log->appendToMetadata([
                'postmark' => [
                    'status'     => 'success',
                    'http_code'  => $statusCode,
                    'to'         => $to,
                    'from'       => $from,
                    'template'   => $templateAlias,
                    'response'   => $respBody,
                ],
            ]);

            // Also persist MessageID to campaign_lead_event_log for webhook correlation
            $messageId = null;
            $decoded   = json_decode((string) $respBody, true);
            if (is_array($decoded) && !empty($decoded['MessageID'])) {
                $messageId = (string) $decoded['MessageID'];
            }
            try {
                $update = [
                    'postmark_delivery_status' => 'sent',
                ];
                if ($messageId) {
                    $update['postmark_message_id'] = $messageId;
                }
                $this->connection->update(
                    MAUTIC_TABLE_PREFIX.'campaign_lead_event_log',
                    $update,
                    ['id' => $log->getId()]
                );
            } catch (\Throwable) {
                // Ignore if columns not present yet
            }

            $event->pass($log);
        }
    }

    /**
     * Handle Postmark campaign conditions
     */
    public function onCampaignTriggerCondition(\Mautic\CampaignBundle\Event\ConditionEvent $event): void
    {
        $log = $event->getLog();
        $lead = $log->getLead();
        $eventConfig = $event->getEventConfig();
        $config = $eventConfig->getConfig();
        $eventType = $eventConfig->getType();
        
        $leadId = $lead->getId();
        $currentCampaignId = $log->getCampaign()->getId();
        $currentEventId = $log->getEvent()->getId();

        // Find the previous Postmark action in the same campaign flow
        $previousPostmarkEvent = $this->findPreviousPostmarkEventInFlow($currentCampaignId, $currentEventId, $leadId);

        if (!$previousPostmarkEvent) {
            $event->fail();
            return;
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

        if ($conditionMet) {
            $event->pass();
        } else {
            $event->fail();
        }
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

    /**
     * Very basic token replacement supporting patterns like {contactfield=email}, {contactfield=firstname}, etc.
     *
     * @param string $from
     * @param string $to
     * @param array  $templateModel
     * @param array  $profileFields
     *
     * @return array [from, to, templateModel]
     */
    private function resolveTokens(string $from, string $to, array $templateModel, array $profileFields): array
    {
        $replace = function (string $value) use ($profileFields): string {
            return preg_replace_callback('/\{contactfield=([^}]+)\}/i', function ($m) use ($profileFields) {
                $key = $m[1] ?? '';
                return (string) ($profileFields[$key] ?? '');
            }, $value) ?? $value;
        };

        $from = $replace($from);
        $to   = $replace($to);

        $resolvedModel = [];
        foreach ($templateModel as $k => $v) {
            $resolvedModel[$k] = is_string($v) ? $replace($v) : $v;
        }

        return [$from, $to, $resolvedModel];
    }

    /**
     * Sends a POST to Postmark with the given payload.
     *
     * @param string $serverToken
     * @param array  $payload
     *
     * @return array [ok(bool), statusCode(int), responseBody(string), error(string|null)]
     */
    private function sendPostmark(string $serverToken, array $payload): array
    {
        $url  = 'https://api.postmarkapp.com/email/withTemplate';
        $ch   = curl_init($url);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Postmark-Server-Token: '.$serverToken,
        ];

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response   = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno      = curl_errno($ch);
        $error      = $errno ? curl_error($ch) : null;
        curl_close($ch);

        $ok = ($errno === 0) && $statusCode >= 200 && $statusCode < 300;
        return [$ok, $statusCode, (string) $response, $error];
    }
}
