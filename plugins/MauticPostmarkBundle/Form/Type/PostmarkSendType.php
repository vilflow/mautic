<?php

namespace MauticPlugin\MauticPostmarkBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\SortableListType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostmarkSendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Authentication & Configuration Section
        $builder
            ->add('server_token', PasswordType::class, [
                'label'       => 'mautic.postmark.form.server_token',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.postmark.form.server_token.required']),
                ],
                'attr'        => [
                    'class'           => 'form-control',
                    'autocomplete'    => 'new-password',
                    'data-toggle'     => 'tooltip',
                    'data-placement'  => 'top',
                    'title'           => 'mautic.postmark.form.server_token.tooltip',
                    // 'style'           => 'background-color: #f8f9fa; border: 2px solid #dee2e6;',
                ],
            ]);

        // Email Configuration Section  
        $builder
            ->add('from_email', TextType::class, [
                'label'       => 'mautic.postmark.form.from_email',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.postmark.form.from_email.required']),
                ],
                'attr'        => [
                    'class'           => 'form-control',
                    'placeholder'     => 'sender@yourdomain.com',
                    'data-toggle'     => 'tooltip',
                    'data-placement'  => 'top',
                    'title'           => 'mautic.postmark.form.from_email.tooltip',
                ],
            ])
            ->add('to_email', TextType::class, [
                'label'       => 'mautic.postmark.form.to_email',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.postmark.form.to_email.required']),
                ],
                'attr'        => [
                    'class'           => 'form-control',
                    'placeholder'     => '{contactfield=email} or user@example.com',
                    'data-toggle'     => 'tooltip',
                    'data-placement'  => 'top',
                    'title'           => 'mautic.postmark.form.to_email.tooltip',
                ],
            ]);

        // Postmark Template Section
        $builder
            ->add('template_alias', TextType::class, [
                'label'       => 'mautic.postmark.form.template_alias',
                'required'    => true,
                'constraints' => [
                    new NotBlank(['message' => 'mautic.postmark.form.template_alias.required']),
                ],
                'attr'        => [
                    'class'           => 'form-control',
                    'placeholder'     => 'welcome-email',
                    'data-toggle'     => 'tooltip',
                    'data-placement'  => 'top',
                    'title'           => 'mautic.postmark.form.template_alias.tooltip',
                ],
            ])
            ->add('template_model', SortableListType::class, [
                'label'           => 'mautic.postmark.form.template_model',
                'required'        => false,
                'option_required' => false,
                'with_labels'     => true,
                'attr'            => [
                    'class'           => 'form-control',
                    'data-toggle'     => 'tooltip',
                    'data-placement'  => 'top',
                    'title'           => 'mautic.postmark.form.template_model.tooltip',
                ],
                'label_attr'      => [
                    'data-toggle'     => 'tooltip',
                    'data-placement'  => 'top',
                    'title'           => 'mautic.postmark.form.template_model.help',
                ],
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

    public function getBlockPrefix(): string
    {
        return 'postmark_send';
    }
}
