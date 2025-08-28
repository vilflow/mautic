<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\CommonEntity;
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
    private $stage;

    /**
     * @var float|null
     */
    private $amount;

    /**
     * @var string|null
     */
    private $abstractReviewResultUrl;

    /**
     * @var string|null
     */
    private $invoiceUrl;

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
            
        $builder->addField('stage', Types::STRING, ['nullable' => true]);
        $builder->addField('amount', Types::DECIMAL, ['nullable' => true, 'precision' => 10, 'scale' => 2]);
        $builder->addField('abstractReviewResultUrl', Types::TEXT, ['columnName' => 'abstract_review_result_url', 'nullable' => true]);
        $builder->addField('invoiceUrl', Types::TEXT, ['columnName' => 'invoice_url', 'nullable' => true]);
        $builder->addField('invitationUrl', Types::TEXT, ['columnName' => 'invitation_url', 'nullable' => true]);
        $builder->addField('suitecrmId', Types::STRING, ['columnName' => 'suitecrm_id', 'nullable' => true]);
        $builder->addField('createdAt', Types::DATETIME_MUTABLE, ['columnName' => 'created_at', 'nullable' => true]);
        $builder->addField('updatedAt', Types::DATETIME_MUTABLE, ['columnName' => 'updated_at', 'nullable' => true]);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('opportunityExternalId', new NotBlank(['message' => 'mautic.opportunities.opportunity_external_id.required']));
        $metadata->addPropertyConstraint('contact', new NotBlank(['message' => 'mautic.opportunities.contact.required']));
        $metadata->addPropertyConstraint('event', new NotBlank(['message' => 'mautic.opportunities.event.required']));
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->stage = 'Submitted';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime();
        return $this;
    }


    public function getOpportunityExternalId(): ?string
    {
        return $this->opportunityExternalId;
    }

    public function setOpportunityExternalId(string $opportunityExternalId): self
    {
        $this->opportunityExternalId = $opportunityExternalId;
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
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(?string $stage): self
    {
        $this->stage = $stage;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getAbstractReviewResultUrl(): ?string
    {
        return $this->abstractReviewResultUrl;
    }

    public function setAbstractReviewResultUrl(?string $abstractReviewResultUrl): self
    {
        $this->abstractReviewResultUrl = $abstractReviewResultUrl;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getInvoiceUrl(): ?string
    {
        return $this->invoiceUrl;
    }

    public function setInvoiceUrl(?string $invoiceUrl): self
    {
        $this->invoiceUrl = $invoiceUrl;
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
            'Submitted' => 'Submitted',
            'Under Review' => 'Under Review',
            'Accepted' => 'Accepted',
            'Rejected' => 'Rejected',
            'Invoiced' => 'Invoiced',
            'Paid' => 'Paid',
            'Cancelled' => 'Cancelled',
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