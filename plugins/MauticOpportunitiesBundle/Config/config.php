<?php

return [
    'name'        => 'Opportunities',
    'description' => 'Manage opportunities.',
    'version'     => '1.0.0',
    'author'      => 'Mautic Community',

    'routes' => [
        'main' => [
            'mautic_opportunity_contacts' => [
                'path'       => '/opportunities/{objectId}/contacts',
                'controller' => 'MauticPlugin\\MauticOpportunitiesBundle\\Controller\\OpportunityController::contactsAction',
                'method'     => 'GET',
            ],
            'mautic_opportunity_index' => [
                'path'       => '/opportunities/{page}',
                'controller' => 'MauticPlugin\\MauticOpportunitiesBundle\\Controller\\OpportunityController::indexAction',
            ],
            'mautic_opportunity_action' => [
                'path'       => '/opportunities/{objectAction}/{objectId}',
                'controller' => 'MauticPlugin\\MauticOpportunitiesBundle\\Controller\\OpportunityController::executeAction',
            ],
            'mautic_opportunity_contacts_search' => [
                'path'       => '/opportunities/{objectId}/contacts/search',
                'controller' => 'MauticPlugin\\MauticOpportunitiesBundle\\Controller\\OpportunityController::searchContactsAction',
                'method'     => 'GET',
            ],
            'mautic_opportunity_contacts_attach' => [
                'path'       => '/opportunities/{objectId}/contacts/attach',
                'controller' => 'MauticPlugin\\MauticOpportunitiesBundle\\Controller\\OpportunityController::attachContactsAction',
                'method'     => 'POST',
            ],
            'mautic_opportunity_contacts_detach' => [
                'path'       => '/opportunities/{objectId}/contacts/{contactId}/detach',
                'controller' => 'MauticPlugin\\MauticOpportunitiesBundle\\Controller\\OpportunityController::detachContactAction',
                'method'     => 'DELETE',
            ],
        ],
        'api' => [
            'mautic_api_opportunitiesstandard' => [
                'standard_entity' => true,
                'name'            => 'opportunities',
                'path'            => '/opportunities',
                'controller'      => MauticPlugin\MauticOpportunitiesBundle\Controller\Api\OpportunityApiController::class,
            ],
        ],
    ],

    'menu' => [
        'main' => [
            'opportunities.menu.index' => [
                'id'        => 'mautic_opportunity_index',
                'route'     => 'mautic_opportunity_index',
                'access'    => 'opportunities:opportunities:view',
                'iconClass' => 'ri-briefcase-line',
                'priority'  => 10,
            ],
        ],
    ],
];
