<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\CommonEntity;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 */
class Opportunity extends CommonEntity
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var \DateTime|null
     */
    private $dateEntered;

    /**
     * @var \DateTime|null
     */
    private $dateModified;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * @var string|null
     */
    private $opportunityType;

    /**
     * @var string|null
     */
    private $leadSource;

    /**
     * @var float|null
     */
    private $amount;

    /**
     * @var float|null
     */
    private $amountUsdollar;

    /**
     * @var \DateTime|null
     */
    private $dateClosed;

    /**
     * @var string|null
     */
    private $nextStep;

    /**
     * @var string|null
     */
    private $salesStage;

    /**
     * @var int|null
     */
    private $probability;

    /**
     * @var string|null
     */
    private $institutionC;

    /**
     * @var string|null
     */
    private $reviewResultC;

    /**
     * @var \DateTime|null
     */
    private $abstractBookSendDateC;

    /**
     * @var string|null
     */
    private $abstractReviewResultUrlC;

    /**
     * @var bool
     */
    private $abstractBookDpublicationC = false;

    /**
     * @var string|null
     */
    private $extraPaperC;

    /**
     * @var string|null
     */
    private $salesReceiptUrlC;

    /**
     * @var \DateTime|null
     */
    private $abstractResultSendDateC;

    /**
     * @var string|null
     */
    private $registrationTypeC;

    /**
     * @var string|null
     */
    private $abstractC;

    /**
     * @var string|null
     */
    private $abstractBookInformationC;

    /**
     * @var string|null
     */
    private $paymentStatusC;

    /**
     * @var string|null
     */
    private $couponCodeC;

    /**
     * @var \DateTime|null
     */
    private $abstractResultReadyDateC;

    /**
     * @var string|null
     */
    private $paperTitleC;

    /**
     * @var bool
     */
    private $smsPermissionC = false;

    /**
     * @var string|null
     */
    private $jjwgMapsGeocodeStatusC;

    /**
     * @var string|null
     */
    private $invoiceUrlC;

    /**
     * @var string|null
     */
    private $presentationTypeC;

    /**
     * @var string|null
     */
    private $invitationLetterUrlC;

    /**
     * @var bool
     */
    private $withdrawC = false;

    /**
     * @var string|null
     */
    private $keywordsC;

    /**
     * @var float|null
     */
    private $jjwgMapsLngC;

    /**
     * @var float|null
     */
    private $jjwgMapsLatC;

    /**
     * @var string|null
     */
    private $transactionIdC;

    /**
     * @var string|null
     */
    private $coAuthorsNamesC;

    /**
     * @var string|null
     */
    private $abstractAttachmentC;

    /**
     * @var string|null
     */
    private $acceptanceLetterUrlC;

    /**
     * @var string|null
     */
    private $paymentChannelC;

    /**
     * @var string|null
     */
    private $wireTransferAttachmentC;

    /**
     * @var string|null
     */
    private $jjwgMapsAddressC;

    /**
     * @var string|null
     */
    private $opportunityExternalId;

    /**
     * @var Lead|null
     */
    private $contact;

    /**
     * @var Event|null
     */
    private $event;


    /**
     * @var string|null
     */
    private $invitationUrl;

    /**
     * @var string|null
     */
    private $suitecrmId;

    /**
     * @var \DateTime|null
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     */
    private $updatedAt;


    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('opportunities')
            ->setCustomRepositoryClass(OpportunityRepository::class);

        $builder->addId();
        $builder->addField('opportunityExternalId', Types::STRING, ['columnName' => 'opportunity_external_id', 'unique' => true]);
        $builder->addField('name', Types::STRING, ['columnName' => 'name']);

        $builder->createManyToOne('contact', Lead::class)
            ->addJoinColumn('contact_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->createManyToOne('event', Event::class)
            ->addJoinColumn('event_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->addField('dateEntered', Types::DATETIME_MUTABLE, ['columnName' => 'date_entered', 'nullable' => true]);
        $builder->addField('dateModified', Types::DATETIME_MUTABLE, ['columnName' => 'date_modified', 'nullable' => true]);
        $builder->addField('deleted', Types::BOOLEAN, ['default' => false]);
        $builder->addField('opportunityType', Types::STRING, ['columnName' => 'opportunity_type', 'nullable' => true]);
        $builder->addField('leadSource', Types::STRING, ['columnName' => 'lead_source', 'nullable' => true]);
        $builder->addField('amount', Types::DECIMAL, ['nullable' => true, 'precision' => 19, 'scale' => 6]);
        $builder->addField('amountUsdollar', Types::DECIMAL, ['columnName' => 'amount_usdollar', 'nullable' => true, 'precision' => 26, 'scale' => 6]);
        $builder->addField('dateClosed', Types::DATE_MUTABLE, ['columnName' => 'date_closed', 'nullable' => true]);
        $builder->addField('nextStep', Types::STRING, ['columnName' => 'next_step', 'nullable' => true]);
        $builder->addField('salesStage', Types::STRING, ['columnName' => 'sales_stage', 'nullable' => true]);
        $builder->addField('probability', Types::INTEGER, ['nullable' => true]);
        $builder->addField('institutionC', Types::STRING, ['columnName' => 'institution_c', 'nullable' => true]);
        $builder->addField('reviewResultC', Types::STRING, ['columnName' => 'review_result_c', 'nullable' => true]);
        $builder->addField('abstractBookSendDateC', Types::DATE_MUTABLE, ['columnName' => 'abstract_book_send_date_c', 'nullable' => true]);
        $builder->addField('abstractReviewResultUrlC', Types::TEXT, ['columnName' => 'abstract_review_result_url_c', 'nullable' => true]);
        $builder->addField('abstractBookDpublicationC', Types::BOOLEAN, ['columnName' => 'abstract_book_dpublication_c', 'default' => false]);
        $builder->addField('extraPaperC', Types::STRING, ['columnName' => 'extra_paper_c', 'nullable' => true]);
        $builder->addField('salesReceiptUrlC', Types::TEXT, ['columnName' => 'sales_receipt_url_c', 'nullable' => true]);
        $builder->addField('abstractResultSendDateC', Types::DATE_MUTABLE, ['columnName' => 'abstract_result_send_date_c', 'nullable' => true]);
        $builder->addField('registrationTypeC', Types::STRING, ['columnName' => 'registration_type_c', 'nullable' => true]);
        $builder->addField('abstractC', Types::TEXT, ['columnName' => 'abstract_c', 'nullable' => true]);
        $builder->addField('abstractBookInformationC', Types::STRING, ['columnName' => 'abstract_book_information_c', 'nullable' => true]);
        $builder->addField('paymentStatusC', Types::STRING, ['columnName' => 'payment_status_c', 'nullable' => true]);
        $builder->addField('couponCodeC', Types::STRING, ['columnName' => 'coupon_code_c', 'nullable' => true]);
        $builder->addField('abstractResultReadyDateC', Types::DATE_MUTABLE, ['columnName' => 'abstract_result_ready_date_c', 'nullable' => true]);
        $builder->addField('paperTitleC', Types::STRING, ['columnName' => 'paper_title_c', 'nullable' => true]);
        $builder->addField('smsPermissionC', Types::BOOLEAN, ['columnName' => 'sms_permission_c', 'default' => false]);
        $builder->addField('jjwgMapsGeocodeStatusC', Types::STRING, ['columnName' => 'jjwg_maps_geocode_status_c', 'nullable' => true]);
        $builder->addField('invoiceUrlC', Types::TEXT, ['columnName' => 'invoice_url_c', 'nullable' => true]);
        $builder->addField('presentationTypeC', Types::STRING, ['columnName' => 'presentation_type_c', 'nullable' => true]);
        $builder->addField('invitationLetterUrlC', Types::TEXT, ['columnName' => 'invitation_letter_url_c', 'nullable' => true]);
        $builder->addField('withdrawC', Types::BOOLEAN, ['columnName' => 'withdraw_c', 'default' => false]);
        $builder->addField('keywordsC', Types::TEXT, ['columnName' => 'keywords_c', 'nullable' => true]);
        $builder->addField('jjwgMapsLngC', Types::FLOAT, ['columnName' => 'jjwg_maps_lng_c', 'nullable' => true]);
        $builder->addField('jjwgMapsLatC', Types::FLOAT, ['columnName' => 'jjwg_maps_lat_c', 'nullable' => true]);
        $builder->addField('transactionIdC', Types::STRING, ['columnName' => 'transaction_id_c', 'nullable' => true]);
        $builder->addField('coAuthorsNamesC', Types::STRING, ['columnName' => 'co_authors_names_c', 'nullable' => true]);
        $builder->addField('abstractAttachmentC', Types::TEXT, ['columnName' => 'abstract_attachment_c', 'nullable' => true]);
        $builder->addField('acceptanceLetterUrlC', Types::TEXT, ['columnName' => 'acceptance_letter_url_c', 'nullable' => true]);
        $builder->addField('paymentChannelC', Types::STRING, ['columnName' => 'payment_channel_c', 'nullable' => true]);
        $builder->addField('wireTransferAttachmentC', Types::TEXT, ['columnName' => 'wire_transfer_attachment_c', 'nullable' => true]);
        $builder->addField('jjwgMapsAddressC', Types::STRING, ['columnName' => 'jjwg_maps_address_c', 'nullable' => true]);
        $builder->addField('suitecrmId', Types::STRING, ['columnName' => 'suitecrm_id', 'nullable' => true]);
        $builder->addField('description', Types::TEXT, ['nullable' => true]);

        // Keep original fields for backwards compatibility
        $builder->addField('invitationUrl', Types::TEXT, ['columnName' => 'invitation_url', 'nullable' => true]);
        $builder->addField('createdAt', Types::DATETIME_MUTABLE, ['columnName' => 'created_at', 'nullable' => true]);
        $builder->addField('updatedAt', Types::DATETIME_MUTABLE, ['columnName' => 'updated_at', 'nullable' => true]);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('opportunityExternalId', new NotBlank(['message' => 'mautic.opportunities.opportunity_external_id.required']));
        $metadata->addPropertyConstraint('contact', new NotBlank(['message' => 'mautic.opportunities.contact.required']));
        $metadata->addPropertyConstraint('event', new NotBlank(['message' => 'mautic.opportunities.event.required']));
    }

    /**
     * Prepares the metadata for API usage.
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata): void
    {
        $metadata->setGroupPrefix('opportunity')
            ->addListProperties([
                'id',
                'opportunityExternalId',
                'name',
                'dateEntered',
                'dateModified',
                'opportunityType',
                'leadSource',
                'amount',
                'amountUsdollar',
                'dateClosed',
                'salesStage',
                'probability',
                'suitecrmId',
            ])
            ->addProperties([
                'description',
                'deleted',
                'nextStep',
                'institutionC',
                'reviewResultC',
                'abstractBookSendDateC',
                'abstractReviewResultUrlC',
                'abstractBookDpublicationC',
                'extraPaperC',
                'salesReceiptUrlC',
                'abstractResultSendDateC',
                'registrationTypeC',
                'abstractC',
                'abstractBookInformationC',
                'paymentStatusC',
                'couponCodeC',
                'abstractResultReadyDateC',
                'paperTitleC',
                'smsPermissionC',
                'jjwgMapsGeocodeStatusC',
                'invoiceUrlC',
                'presentationTypeC',
                'invitationLetterUrlC',
                'withdrawC',
                'keywordsC',
                'jjwgMapsLngC',
                'jjwgMapsLatC',
                'transactionIdC',
                'coAuthorsNamesC',
                'abstractAttachmentC',
                'acceptanceLetterUrlC',
                'paymentChannelC',
                'wireTransferAttachmentC',
                'jjwgMapsAddressC',
                'invitationUrl',
                'createdAt',
                'updatedAt',
            ])
            ->build();
    }

    public function __construct()
    {
        $this->dateEntered = new \DateTime();
        $this->dateModified = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->salesStage = 'Submitted';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getDateEntered(): ?\DateTime
    {
        return $this->dateEntered;
    }

    public function setDateEntered(?\DateTime $dateEntered): self
    {
        $this->dateEntered = $dateEntered;
        return $this;
    }

    public function getDateModified(): ?\DateTime
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTime $dateModified): self
    {
        $this->dateModified = $dateModified;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getOpportunityType(): ?string
    {
        return $this->opportunityType;
    }

    public function setOpportunityType(?string $opportunityType): self
    {
        $this->opportunityType = $opportunityType;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getLeadSource(): ?string
    {
        return $this->leadSource;
    }

    public function setLeadSource(?string $leadSource): self
    {
        $this->leadSource = $leadSource;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getAmountUsdollar(): ?float
    {
        return $this->amountUsdollar;
    }

    public function setAmountUsdollar(?float $amountUsdollar): self
    {
        $this->amountUsdollar = $amountUsdollar;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getDateClosed(): ?\DateTime
    {
        return $this->dateClosed;
    }

    public function setDateClosed(?\DateTime $dateClosed): self
    {
        $this->dateClosed = $dateClosed;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getNextStep(): ?string
    {
        return $this->nextStep;
    }

    public function setNextStep(?string $nextStep): self
    {
        $this->nextStep = $nextStep;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getSalesStage(): ?string
    {
        return $this->salesStage;
    }

    public function setSalesStage(?string $salesStage): self
    {
        $this->salesStage = $salesStage;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getProbability(): ?int
    {
        return $this->probability;
    }

    public function setProbability(?int $probability): self
    {
        $this->probability = $probability;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getInstitutionC(): ?string
    {
        return $this->institutionC;
    }

    public function setInstitutionC(?string $institutionC): self
    {
        $this->institutionC = $institutionC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getReviewResultC(): ?string
    {
        return $this->reviewResultC;
    }

    public function setReviewResultC(?string $reviewResultC): self
    {
        $this->reviewResultC = $reviewResultC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractBookSendDateC(): ?\DateTime
    {
        return $this->abstractBookSendDateC;
    }

    public function setAbstractBookSendDateC(?\DateTime $abstractBookSendDateC): self
    {
        $this->abstractBookSendDateC = $abstractBookSendDateC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractReviewResultUrlC(): ?string
    {
        return $this->abstractReviewResultUrlC;
    }

    public function setAbstractReviewResultUrlC(?string $abstractReviewResultUrlC): self
    {
        $this->abstractReviewResultUrlC = $abstractReviewResultUrlC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function isAbstractBookDpublicationC(): bool
    {
        return $this->abstractBookDpublicationC;
    }

    public function setAbstractBookDpublicationC(bool $abstractBookDpublicationC): self
    {
        $this->abstractBookDpublicationC = $abstractBookDpublicationC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getExtraPaperC(): ?string
    {
        return $this->extraPaperC;
    }

    public function setExtraPaperC(?string $extraPaperC): self
    {
        $this->extraPaperC = $extraPaperC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getSalesReceiptUrlC(): ?string
    {
        return $this->salesReceiptUrlC;
    }

    public function setSalesReceiptUrlC(?string $salesReceiptUrlC): self
    {
        $this->salesReceiptUrlC = $salesReceiptUrlC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractResultSendDateC(): ?\DateTime
    {
        return $this->abstractResultSendDateC;
    }

    public function setAbstractResultSendDateC(?\DateTime $abstractResultSendDateC): self
    {
        $this->abstractResultSendDateC = $abstractResultSendDateC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getRegistrationTypeC(): ?string
    {
        return $this->registrationTypeC;
    }

    public function setRegistrationTypeC(?string $registrationTypeC): self
    {
        $this->registrationTypeC = $registrationTypeC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractC(): ?string
    {
        return $this->abstractC;
    }

    public function setAbstractC(?string $abstractC): self
    {
        $this->abstractC = $abstractC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractBookInformationC(): ?string
    {
        return $this->abstractBookInformationC;
    }

    public function setAbstractBookInformationC(?string $abstractBookInformationC): self
    {
        $this->abstractBookInformationC = $abstractBookInformationC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getPaymentStatusC(): ?string
    {
        return $this->paymentStatusC;
    }

    public function setPaymentStatusC(?string $paymentStatusC): self
    {
        $this->paymentStatusC = $paymentStatusC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getCouponCodeC(): ?string
    {
        return $this->couponCodeC;
    }

    public function setCouponCodeC(?string $couponCodeC): self
    {
        $this->couponCodeC = $couponCodeC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractResultReadyDateC(): ?\DateTime
    {
        return $this->abstractResultReadyDateC;
    }

    public function setAbstractResultReadyDateC(?\DateTime $abstractResultReadyDateC): self
    {
        $this->abstractResultReadyDateC = $abstractResultReadyDateC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getPaperTitleC(): ?string
    {
        return $this->paperTitleC;
    }

    public function setPaperTitleC(?string $paperTitleC): self
    {
        $this->paperTitleC = $paperTitleC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function isSmsPermissionC(): bool
    {
        return $this->smsPermissionC;
    }

    public function setSmsPermissionC(bool $smsPermissionC): self
    {
        $this->smsPermissionC = $smsPermissionC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getJjwgMapsGeocodeStatusC(): ?string
    {
        return $this->jjwgMapsGeocodeStatusC;
    }

    public function setJjwgMapsGeocodeStatusC(?string $jjwgMapsGeocodeStatusC): self
    {
        $this->jjwgMapsGeocodeStatusC = $jjwgMapsGeocodeStatusC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getInvoiceUrlC(): ?string
    {
        return $this->invoiceUrlC;
    }

    public function setInvoiceUrlC(?string $invoiceUrlC): self
    {
        $this->invoiceUrlC = $invoiceUrlC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getPresentationTypeC(): ?string
    {
        return $this->presentationTypeC;
    }

    public function setPresentationTypeC(?string $presentationTypeC): self
    {
        $this->presentationTypeC = $presentationTypeC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getInvitationLetterUrlC(): ?string
    {
        return $this->invitationLetterUrlC;
    }

    public function setInvitationLetterUrlC(?string $invitationLetterUrlC): self
    {
        $this->invitationLetterUrlC = $invitationLetterUrlC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function isWithdrawC(): bool
    {
        return $this->withdrawC;
    }

    public function setWithdrawC(bool $withdrawC): self
    {
        $this->withdrawC = $withdrawC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getKeywordsC(): ?string
    {
        return $this->keywordsC;
    }

    public function setKeywordsC(?string $keywordsC): self
    {
        $this->keywordsC = $keywordsC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getJjwgMapsLngC(): ?float
    {
        return $this->jjwgMapsLngC;
    }

    public function setJjwgMapsLngC(?float $jjwgMapsLngC): self
    {
        $this->jjwgMapsLngC = $jjwgMapsLngC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getJjwgMapsLatC(): ?float
    {
        return $this->jjwgMapsLatC;
    }

    public function setJjwgMapsLatC(?float $jjwgMapsLatC): self
    {
        $this->jjwgMapsLatC = $jjwgMapsLatC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getTransactionIdC(): ?string
    {
        return $this->transactionIdC;
    }

    public function setTransactionIdC(?string $transactionIdC): self
    {
        $this->transactionIdC = $transactionIdC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getCoAuthorsNamesC(): ?string
    {
        return $this->coAuthorsNamesC;
    }

    public function setCoAuthorsNamesC(?string $coAuthorsNamesC): self
    {
        $this->coAuthorsNamesC = $coAuthorsNamesC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAbstractAttachmentC(): ?string
    {
        return $this->abstractAttachmentC;
    }

    public function setAbstractAttachmentC(?string $abstractAttachmentC): self
    {
        $this->abstractAttachmentC = $abstractAttachmentC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getAcceptanceLetterUrlC(): ?string
    {
        return $this->acceptanceLetterUrlC;
    }

    public function setAcceptanceLetterUrlC(?string $acceptanceLetterUrlC): self
    {
        $this->acceptanceLetterUrlC = $acceptanceLetterUrlC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getPaymentChannelC(): ?string
    {
        return $this->paymentChannelC;
    }

    public function setPaymentChannelC(?string $paymentChannelC): self
    {
        $this->paymentChannelC = $paymentChannelC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getWireTransferAttachmentC(): ?string
    {
        return $this->wireTransferAttachmentC;
    }

    public function setWireTransferAttachmentC(?string $wireTransferAttachmentC): self
    {
        $this->wireTransferAttachmentC = $wireTransferAttachmentC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getJjwgMapsAddressC(): ?string
    {
        return $this->jjwgMapsAddressC;
    }

    public function setJjwgMapsAddressC(?string $jjwgMapsAddressC): self
    {
        $this->jjwgMapsAddressC = $jjwgMapsAddressC;
        $this->dateModified = new \DateTime();
        return $this;
    }

    public function getOpportunityExternalId(): ?string
    {
        return $this->opportunityExternalId;
    }

    public function setOpportunityExternalId(string $opportunityExternalId): self
    {
        $this->opportunityExternalId = $opportunityExternalId;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getContact(): ?Lead
    {
        return $this->contact;
    }

    public function setContact(?Lead $contact): self
    {
        $this->contact = $contact;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }


    public function getInvitationUrl(): ?string
    {
        return $this->invitationUrl;
    }

    public function setInvitationUrl(?string $invitationUrl): self
    {
        $this->invitationUrl = $invitationUrl;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getSuitecrmId(): ?string
    {
        return $this->suitecrmId;
    }

    public function setSuitecrmId(?string $suitecrmId): self
    {
        $this->suitecrmId = $suitecrmId;
        $this->dateModified = new \DateTime();
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get available stages for opportunities
     */
    public static function getStageChoices(): array
    {
        return [
            'Prospecting' => 'Prospecting',
            'Qualification' => 'Qualification',
            'Needs Analysis' => 'Needs Analysis',
            'Value Proposition' => 'Value Proposition',
            'Id. Decision Makers' => 'Id. Decision Makers',
            'Perception Analysis' => 'Perception Analysis',
            'Proposal/Price Quote' => 'Proposal/Price Quote',
            'Negotiation/Review' => 'Negotiation/Review',
            'Closed Won' => 'Closed Won',
            'Closed Lost' => 'Closed Lost',
        ];
    }

    /**
     * Get available opportunity types
     */
    public static function getOpportunityTypeChoices(): array
    {
        return [
            'Existing Business' => 'Existing Business',
            'New Business' => 'New Business',
        ];
    }

    /**
     * Get available lead sources
     */
    public static function getLeadSourceChoices(): array
    {
        return [
            'Cold Call' => 'Cold Call',
            'Existing Customer' => 'Existing Customer',
            'Self Generated' => 'Self Generated',
            'Employee' => 'Employee',
            'Partner' => 'Partner',
            'Public Relations' => 'Public Relations',
            'Direct Mail' => 'Direct Mail',
            'Conference' => 'Conference',
            'Trade Show' => 'Trade Show',
            'Web Site' => 'Web Site',
            'Word of mouth' => 'Word of mouth',
            'Email' => 'Email',
            'Campaign' => 'Campaign',
            'Other' => 'Other',
        ];
    }

    /**
     * Get available presentation types
     */
    public static function getPresentationTypeChoices(): array
    {
        return [
            'Oral Presentation' => 'Oral Presentation',
            'Poster Presentation' => 'Poster Presentation',
            'Keynote Speech' => 'Keynote Speech',
            'Workshop' => 'Workshop',
            'Panel Discussion' => 'Panel Discussion',
        ];
    }

    /**
     * Get available registration types
     */
    public static function getRegistrationTypeChoices(): array
    {
        return [
            'Early Bird' => 'Early Bird',
            'Regular' => 'Regular',
            'Student' => 'Student',
            'Faculty' => 'Faculty',
            'Industry' => 'Industry',
            'International' => 'International',
        ];
    }

    /**
     * Get available payment statuses
     */
    public static function getPaymentStatusChoices(): array
    {
        return [
            'Pending' => 'Pending',
            'Paid' => 'Paid',
            'Failed' => 'Failed',
            'Refunded' => 'Refunded',
            'Cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get available payment channels
     */
    public static function getPaymentChannelChoices(): array
    {
        return [
            'Credit Card' => 'Credit Card',
            'Bank Transfer' => 'Bank Transfer',
            'PayPal' => 'PayPal',
            'Wire Transfer' => 'Wire Transfer',
            'Check' => 'Check',
            'Cash' => 'Cash',
        ];
    }

    /**
     * Get available review results
     */
    public static function getReviewResultChoices(): array
    {
        return [
            'Accepted' => 'Accepted',
            'Rejected' => 'Rejected',
            'Revision Required' => 'Revision Required',
            'Under Review' => 'Under Review',
        ];
    }

    public function __call($name, $arguments)
    {
        $defaults = [
            'getCreatedBy'      => null,
            'getDateAdded'      => null,
            'getDateModified'   => null,
            'getCreatedByUser'  => null,
            'getModifiedBy'     => null,
            'getModifiedByUser' => null,
            'getCheckedOut'     => null,
            'getCheckedOutBy'   => null,
            'getCheckedOutByUser' => null,
            'isPublished'       => true,
        ];

        if (array_key_exists($name, $defaults)) {
            return $defaults[$name];
        }

        return parent::__call($name, $arguments);
    }
}
