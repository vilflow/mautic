<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventCountryConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'country',
            TextType::class,
            [
                'label'      => 'mautic.events.campaign.condition.event_country_field',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'tooltip'     => 'mautic.events.campaign.condition.event_country_field_tooltip',
                    'placeholder' => 'mautic.events.campaign.condition.event_country_placeholder',
                    'maxlength'   => 50,
                ],
                'required'    => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'mautic.events.campaign.condition.event_country_required',
                    ]),
                ],
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'mautic_events_event_country_condition';
    }
}