<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Event>
 */
class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('eventExternalId', TextType::class, [
            'label'      => 'mautic.events.event_external_id',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.event_external_id.required']),
            ],
        ]);

        $builder->add('name', TextType::class, [
            'label'      => 'mautic.events.name',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'mautic.events.name.required']),
            ],
        ]);

        $builder->add('website', UrlType::class, [
            'label'      => 'mautic.events.website',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('currency', TextType::class, [
            'label'      => 'mautic.events.currency',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'placeholder' => 'ISO 4217 code (e.g., USD, EUR)'],
            'required'   => false,
        ]);

        $builder->add('country', TextType::class, [
            'label'      => 'mautic.events.country',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'placeholder' => 'Full country name or ISO code (e.g., United States, US)'],
            'required'   => false,
        ]);

        $builder->add('city', TextType::class, [
            'label'      => 'mautic.events.city',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('registrationUrl', UrlType::class, [
            'label'      => 'mautic.events.registration_url',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('suitecrmId', TextType::class, [
            'label'      => 'mautic.events.suitecrm_id',
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
            'data_class' => Event::class,
        ]);
    }
}
