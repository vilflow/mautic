<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for event currency campaign condition.
 */
class EventCurrencyConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('operator', ChoiceType::class, [
            'label'   => 'mautic.events.campaign.condition.operator',
            'choices' => [
                'mautic.core.operator.equals'    => 'eq',
                'mautic.core.operator.notequals' => 'neq',
            ],
            'attr' => [
                'class' => 'form-control',
            ],
        ]);

        $builder->add('currency', ChoiceType::class, [
            'label' => 'mautic.events.campaign.condition.currency',
            'choices' => [
                'USD' => 'USD',
                'EUR' => 'EUR',
                'GBP' => 'GBP',
                'CAD' => 'CAD',
                'AUD' => 'AUD',
                'JPY' => 'JPY',
                'CHF' => 'CHF',
                'CNY' => 'CNY',
                'INR' => 'INR',
                'BRL' => 'BRL',
                'MXN' => 'MXN',
                'ZAR' => 'ZAR',
            ],
            'attr' => [
                'class' => 'form-control',
            ],
            'placeholder' => 'mautic.core.form.chooseone',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }
}