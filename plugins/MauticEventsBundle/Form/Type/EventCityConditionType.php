<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for event city campaign condition.
 */
class EventCityConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('operator', ChoiceType::class, [
            'label'   => 'mautic.events.campaign.condition.operator',
            'choices' => [
                'mautic.core.operator.equals'     => 'eq',
                'mautic.core.operator.notequals'  => 'neq',
                'mautic.core.operator.contains'   => 'like',
                'mautic.core.operator.notcontains' => 'notlike',
            ],
            'attr' => [
                'class' => 'form-control',
            ],
        ]);

        $builder->add('city', TextType::class, [
            'label' => 'mautic.events.campaign.condition.city',
            'attr'  => [
                'class'       => 'form-control',
                'placeholder' => 'mautic.events.campaign.condition.city.placeholder',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.campaign.condition.city.required']),
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