<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for opportunity SuiteCRM ID campaign condition.
 */
class OpportunitySuitecrmIdConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('suitecrm_id', TextType::class, [
            'label' => 'mautic.opportunities.campaign.condition.suitecrm_id',
            'attr'  => [
                'class'       => 'form-control',
                'placeholder' => 'mautic.opportunities.campaign.condition.suitecrm_id.placeholder',
            ],
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.campaign.condition.suitecrm_id.required']),
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