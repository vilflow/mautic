<?php

namespace MauticPlugin\MauticPostmarkBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\SortableListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostmarkSendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Fixed parameters
        $builder
            ->add('from_email', TextType::class, [
                'label'      => 'mautic.postmark.form.from_email',
                'required'   => true,
            ])
            ->add('to_email', TextType::class, [
                'label'      => 'mautic.postmark.form.to_email',
                'required'   => true,
                'attr'       => [
                    'placeholder' => 'you@example.com or a token like {contactfield=email}',
                ],
            ])
            ->add('template_alias', TextType::class, [
                'label'      => 'mautic.postmark.form.template_alias',
                'required'   => true,
            ])
            ->add('server_token', TextType::class, [
                'label'      => 'mautic.postmark.form.server_token',
                'required'   => true,
                'attr'       => [
                    'type'         => 'password',
                    'autocomplete' => 'new-password',
                ],
            ])
            ->add('template_model', SortableListType::class, [
                'label'           => 'mautic.postmark.form.template_model',
                'required'        => false,
                'option_required' => false,
                'with_labels'     => true,
                // Set to true to persist as a flat key=>value array instead of [list => [...]]
                // 'key_value_pairs' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }
}
