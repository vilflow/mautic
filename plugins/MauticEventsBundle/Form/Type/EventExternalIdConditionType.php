<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for event external ID campaign condition.
 */
class EventExternalIdConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('external_id', TextType::class, [
            'label' => 'mautic.events.campaign.condition.external_id',
            'attr'  => [
                'class'       => 'form-control',
                'placeholder' => 'mautic.events.campaign.condition.external_id.placeholder',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.campaign.condition.external_id.required']),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }
}