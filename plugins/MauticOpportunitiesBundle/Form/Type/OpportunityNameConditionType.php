<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for opportunity name campaign condition.
 */
class OpportunityNameConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label'   => 'mautic.opportunities.campaign.condition.name',
            'attr'    => [
                'class' => 'form-control',
                'placeholder' => 'mautic.opportunities.campaign.condition.name.placeholder',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.campaign.condition.name.required']),
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