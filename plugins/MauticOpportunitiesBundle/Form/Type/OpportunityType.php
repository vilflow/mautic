<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        // Required fields
        $builder->add('opportunityExternalId', TextType::class, [
            'label'      => 'mautic.opportunities.opportunity_external_id',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.opportunity_external_id.required']),
            ],
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

        // Basic opportunity information
        $builder->add('name', TextType::class, [
            'label'      => 'mautic.opportunities.name',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('opportunityType', ChoiceType::class, [
            'label'      => 'mautic.opportunities.opportunity_type',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getOpportunityTypeChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        $builder->add('leadSource', ChoiceType::class, [
            'label'      => 'mautic.opportunities.lead_source',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getLeadSourceChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        // Financial information
        $builder->add('amount', NumberType::class, [
            'label'      => 'mautic.opportunities.amount',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'step' => '0.000001'],
            'scale'      => 6,
            'required'   => false,
        ]);

        $builder->add('amountUsdollar', NumberType::class, [
            'label'      => 'mautic.opportunities.amount_usdollar',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'step' => '0.000001'],
            'scale'      => 6,
            'required'   => false,
        ]);

        // Sales information
        $builder->add('salesStage', ChoiceType::class, [
            'label'      => 'mautic.opportunities.sales_stage',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getStageChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);


        $builder->add('probability', IntegerType::class, [
            'label'      => 'mautic.opportunities.probability',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'min' => '0', 'max' => '100'],
            'required'   => false,
        ]);

        $builder->add('dateClosed', DateType::class, [
            'label'      => 'mautic.opportunities.date_closed',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'widget'     => 'single_text',
            'required'   => false,
        ]);

        $builder->add('nextStep', TextType::class, [
            'label'      => 'mautic.opportunities.next_step',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Academic/Conference specific fields
        $builder->add('institutionC', TextType::class, [
            'label'      => 'mautic.opportunities.institution_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('paperTitleC', TextType::class, [
            'label'      => 'mautic.opportunities.paper_title_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('abstractC', TextareaType::class, [
            'label'      => 'mautic.opportunities.abstract_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'rows' => '6'],
            'required'   => false,
        ]);

        $builder->add('keywordsC', TextareaType::class, [
            'label'      => 'mautic.opportunities.keywords_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'rows' => '3'],
            'required'   => false,
        ]);

        $builder->add('coAuthorsNamesC', TextType::class, [
            'label'      => 'mautic.opportunities.co_authors_names_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('presentationTypeC', ChoiceType::class, [
            'label'      => 'mautic.opportunities.presentation_type_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getPresentationTypeChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        // Registration and Payment
        $builder->add('registrationTypeC', ChoiceType::class, [
            'label'      => 'mautic.opportunities.registration_type_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getRegistrationTypeChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        $builder->add('paymentStatusC', ChoiceType::class, [
            'label'      => 'mautic.opportunities.payment_status_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getPaymentStatusChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        $builder->add('paymentChannelC', ChoiceType::class, [
            'label'      => 'mautic.opportunities.payment_channel_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getPaymentChannelChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        $builder->add('couponCodeC', TextType::class, [
            'label'      => 'mautic.opportunities.coupon_code_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('transactionIdC', TextType::class, [
            'label'      => 'mautic.opportunities.transaction_id_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Review and Status
        $builder->add('reviewResultC', ChoiceType::class, [
            'label'      => 'mautic.opportunities.review_result_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'choices'    => Opportunity::getReviewResultChoices(),
            'placeholder' => 'mautic.opportunities.choose_one',
            'required'   => false,
        ]);

        // URLs and Attachments
        $builder->add('abstractReviewResultUrlC', UrlType::class, [
            'label'      => 'mautic.opportunities.abstract_review_result_url_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('invoiceUrlC', UrlType::class, [
            'label'      => 'mautic.opportunities.invoice_url_c',
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

        $builder->add('invitationLetterUrlC', UrlType::class, [
            'label'      => 'mautic.opportunities.invitation_letter_url_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('salesReceiptUrlC', UrlType::class, [
            'label'      => 'mautic.opportunities.sales_receipt_url_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('abstractAttachmentC', UrlType::class, [
            'label'      => 'mautic.opportunities.abstract_attachment_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('acceptanceLetterUrlC', UrlType::class, [
            'label'      => 'mautic.opportunities.acceptance_letter_url_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('wireTransferAttachmentC', UrlType::class, [
            'label'      => 'mautic.opportunities.wire_transfer_attachment_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Dates
        $builder->add('abstractBookSendDateC', DateType::class, [
            'label'      => 'mautic.opportunities.abstract_book_send_date_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'widget'     => 'single_text',
            'required'   => false,
        ]);

        $builder->add('abstractResultSendDateC', DateType::class, [
            'label'      => 'mautic.opportunities.abstract_result_send_date_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'widget'     => 'single_text',
            'required'   => false,
        ]);

        $builder->add('abstractResultReadyDateC', DateType::class, [
            'label'      => 'mautic.opportunities.abstract_result_ready_date_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'widget'     => 'single_text',
            'required'   => false,
        ]);

        // Boolean fields
        $builder->add('abstractBookDpublicationC', CheckboxType::class, [
            'label'      => 'mautic.opportunities.abstract_book_dpublication_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-check-input'],
            'required'   => false,
        ]);

        $builder->add('smsPermissionC', CheckboxType::class, [
            'label'      => 'mautic.opportunities.sms_permission_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-check-input'],
            'required'   => false,
        ]);

        $builder->add('withdrawC', CheckboxType::class, [
            'label'      => 'mautic.opportunities.withdraw_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-check-input'],
            'required'   => false,
        ]);

        // Additional text fields
        $builder->add('extraPaperC', TextType::class, [
            'label'      => 'mautic.opportunities.extra_paper_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('abstractBookInformationC', TextType::class, [
            'label'      => 'mautic.opportunities.abstract_book_information_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Geographic fields
        $builder->add('jjwgMapsAddressC', TextType::class, [
            'label'      => 'mautic.opportunities.jjwg_maps_address_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('jjwgMapsLatC', NumberType::class, [
            'label'      => 'mautic.opportunities.jjwg_maps_lat_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'step' => 'any'],
            'required'   => false,
        ]);

        $builder->add('jjwgMapsLngC', NumberType::class, [
            'label'      => 'mautic.opportunities.jjwg_maps_lng_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'step' => 'any'],
            'required'   => false,
        ]);

        $builder->add('jjwgMapsGeocodeStatusC', TextType::class, [
            'label'      => 'mautic.opportunities.jjwg_maps_geocode_status_c',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        // Legacy/System fields
        $builder->add('suitecrmId', TextType::class, [
            'label'      => 'mautic.opportunities.suitecrm_id',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control'],
            'required'   => false,
        ]);

        $builder->add('deleted', CheckboxType::class, [
            'label'      => 'mautic.opportunities.deleted',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-check-input'],
            'required'   => false,
        ]);

        $builder->add('description', TextareaType::class, [
            'label'      => 'mautic.opportunities.description',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => ['class' => 'form-control', 'rows' => '4'],
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