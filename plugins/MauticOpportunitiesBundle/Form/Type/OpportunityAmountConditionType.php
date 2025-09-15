<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Mautic\LeadBundle\Provider\TypeOperatorProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for opportunity amount campaign condition.
 */
class OpportunityAmountConditionType extends AbstractType
{
    public function __construct(
        private TypeOperatorProviderInterface $typeOperatorProvider
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('operator', ChoiceType::class, [
            'label'   => 'mautic.opportunities.campaign.condition.operator',
            'choices' => $this->typeOperatorProvider->getOperatorsForFieldType('number'),
            'attr'    => [
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.campaign.condition.operator.required']),
            ],
        ]);

        $builder->add('amount', NumberType::class, [
            'label' => 'mautic.opportunities.campaign.condition.amount',
            'attr'  => [
                'class' => 'form-control',
                'step'  => '0.01',
            ],
            'scale' => 2,
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.campaign.condition.amount.required']),
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