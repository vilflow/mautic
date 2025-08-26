<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for event website campaign condition.
 */
class EventWebsiteConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('website', TextType::class, [
            'label' => 'mautic.events.campaign.condition.website',
            'attr'  => [
                'class'       => 'form-control',
                'placeholder' => 'mautic.events.campaign.condition.website.placeholder',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.campaign.condition.website.required']),
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