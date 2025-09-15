<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for opportunity abstract review result URL campaign condition.
 */
class OpportunityAbstractReviewResultUrlConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('abstract_review_result_url', TextType::class, [
            'label'   => 'mautic.opportunities.campaign.condition.abstract_review_result_url',
            'attr'    => [
                'class' => 'form-control',
                'placeholder' => 'mautic.opportunities.campaign.condition.abstract_review_result_url.placeholder',
            ],
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }
}