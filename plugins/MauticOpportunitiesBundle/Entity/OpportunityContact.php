<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\CommonEntity;
use Mautic\LeadBundle\Entity\Lead;

/**
 * Link between Opportunity and Contact.
 */
class OpportunityContact extends CommonEntity
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var Opportunity
     */
    private $opportunity;

    /**
     * @var Lead
     */
    private $contact;

    /**
     * @var \DateTimeInterface|null
     */
    private $dateAdded;

    /**
     * @var \DateTimeInterface|null
     */
    private $dateModified;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('opportunity_contacts')
            ->setCustomRepositoryClass(OpportunityContactRepository::class);

        $builder->addId();
        $builder->addManyToOne('opportunity', Opportunity::class, 'opportunityContacts', [
            'onDelete' => 'CASCADE',
        ]);
        $builder->addManyToOne('contact', Lead::class, 'opportunityContacts', [
            'onDelete' => 'NO ACTION',
        ]);

        // Indexes for faster lookup by opportunity or contact
        $builder->addIndex(['opportunity_id'], 'opportunity_contacts_opportunity_idx');
        $builder->addIndex(['contact_id'], 'opportunity_contacts_contact_idx');
        $builder->addUniqueConstraint(['opportunity_id', 'contact_id'], 'opportunity_contacts_opportunity_contact_uniq');

        $builder->addNullableField('dateAdded', Types::DATETIME_MUTABLE);
        $builder->addNullableField('dateModified', Types::DATETIME_MUTABLE);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOpportunity(): Opportunity
    {
        return $this->opportunity;
    }

    public function setOpportunity(Opportunity $opportunity): self
    {
        $this->opportunity = $opportunity;

        return $this;
    }

    public function getContact(): Lead
    {
        return $this->contact;
    }

    public function setContact(Lead $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }
}