<?php

return [
    'name'        => 'Events',
    'description' => 'Manage events.',
    'version'     => '1.0.0',
    'author'      => 'Mautic Community',

    'routes' => [
        'main' => [
            'mautic_event_contacts' => [
                'path'       => '/events/{objectId}/contacts',
                'controller' => 'MauticPlugin\\MauticEventsBundle\\Controller\\EventController::contactsAction',
                'method'     => 'GET',
            ],
            'mautic_event_index' => [
                'path'       => '/events/{page}',
                'controller' => 'MauticPlugin\\MauticEventsBundle\\Controller\\EventController::indexAction',
            ],
            'mautic_event_action' => [
                'path'       => '/events/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\\MauticEventsBundle\\Controller\\EventController::executeAction',
            ],
            'mautic_event_contacts_search' => [
                'path'       => '/events/{objectId}/contacts/search',
                'controller' => 'MauticPlugin\\MauticEventsBundle\\Controller\\EventController::searchContactsAction',
                'method'     => 'GET',
            ],
            'mautic_event_contacts_attach' => [
                'path'       => '/events/{objectId}/contacts/attach',
                'controller' => 'MauticPlugin\\MauticEventsBundle\\Controller\\EventController::attachContactsAction',
                'method'     => 'POST',
            ],
            'mautic_event_contacts_detach' => [
                'path'       => '/events/{objectId}/contacts/{contactId}/detach',
                'controller' => 'MauticPlugin\\MauticEventsBundle\\Controller\\EventController::detachContactAction',
                'method'     => 'DELETE',
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'events.menu.index' => [
                'id'        => 'mautic_event_index',
                'route'     => 'mautic_event_index',
                'access'    => 'events:events:view',
                'iconClass' => 'ri-calendar-line',
                'priority'  => 10,
            ],
        ],
    ],
];
