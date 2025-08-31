<?php

return [
    'name'        => 'Postmark Campaign Action',
    'description' => 'Adds a campaign action to send emails via Postmark API with templates.',
    'version'     => '1.0.0',
    'author'      => 'Your Team',
    'routes'      => [
        'public' => [
            'mautic_postmark_webhook' => [
                'path'       => '/postmark/webhook',
                'controller' => 'MauticPlugin\\MauticPostmarkBundle\\Controller\\WebhookController::handleAction',
                'method'     => 'POST',
            ],
        ],
    ],
];
