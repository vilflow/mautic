<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Opportunity>
 */
class OpportunityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('opportunityExternalId', TextType::class, [
            'label'      => 'mautic.opportunities.opportunity_external_id',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.opportunity_external_id.required']),
            ],
        ]);

        $builder->add('name', TextType::class, [
            'label'      => 'mautic.opportunities.name',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('contact', EntityType::class, [
            'label'       => 'mautic.opportunities.contact',
            'label_attr'  => ['class' => 'control-label'],
            'attr'        => ['class' => 'form-control'],
            'class'       => Lead::class,
            'choice_label' => function (Lead $contact) {
                return $contact->getPrimaryIdentifier();
            },
            'placeholder' => 'mautic.opportunities.contact.select',
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.contact.required']),
            ],
        ]);

        $builder->add('event', EntityType::class, [
            'label'       => 'mautic.opportunities.event',
            'label_attr'  => ['class' => 'control-label'],
            'attr'        => ['class' => 'form-control'],
            'class'       => Event::class,
            'choice_label' => 'name',
            'placeholder' => 'mautic.opportunities.event.select',
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.event.required']),
            ],
        ]);

        $builder->add('stage', ChoiceType::class, [
            'label'      => 'mautic.opportunities.stage',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getStageChoices(),
            'required'   => false,
        ]);

        $builder->add('amount', NumberType::class, [
            'label'      => 'mautic.opportunities.amount',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'step' => '0.01'],
            'scale'      => 2,
            'required'   => false,
        ]);

        $builder->add('abstractReviewResultUrl', UrlType::class, [
            'label'      => 'mautic.opportunities.abstract_review_result_url',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('invoiceUrl', UrlType::class, [
            'label'      => 'mautic.opportunities.invoice_url',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('invitationUrl', UrlType::class, [
            'label'      => 'mautic.opportunities.invitation_url',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('suitecrmId', TextType::class, [
            'label'      => 'mautic.opportunities.suitecrm_id',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('buttons', FormButtonsType::class);

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Opportunity::class,
        ]);
    }
}