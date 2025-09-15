<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Security\Permissions;

use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

class OpportunitiesPermissions extends AbstractPermissions
{
    public function __construct($params)
    {
        parent::__construct($params);
        $this->addExtendedPermissions('opportunities');
    }

    public function getName(): string
    {
        return 'opportunities';
    }

    public function buildForm(FormBuilderInterface &$builder, array $options, array $data): void
    {
        $this->addExtendedFormFields('opportunities', 'opportunities', $builder, $data);
    }
}
