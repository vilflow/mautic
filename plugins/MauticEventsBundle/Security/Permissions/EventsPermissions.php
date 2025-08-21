<?php

namespace MauticPlugin\MauticEventsBundle\Security\Permissions;

use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

class EventsPermissions extends AbstractPermissions
{
    public function __construct($params)
    {
        parent::__construct($params);
        $this->addExtendedPermissions('events');
    }

    public function getName(): string
    {
        return 'events';
    }

    public function buildForm(FormBuilderInterface &$builder, array $options, array $data): void
    {
        $this->addExtendedFormFields('events', 'events', $builder, $data);
    }
}
