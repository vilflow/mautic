<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for opportunity stage campaign condition.
 */
class OpportunityStageConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('stage', ChoiceType::class, [
            'label'   => 'mautic.opportunities.campaign.condition.stage',
            'choices' => Opportunity::getStageChoices(),
            'attr'    => [
                'class' => 'form-control',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.campaign.condition.stage.required']),
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