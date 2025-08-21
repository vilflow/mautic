<?php

namespace MauticPlugin\MauticEventsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\CommonEntity;
use Mautic\LeadBundle\Entity\Lead;

/**
 * Link between Event and Contact.
 */
class EventContact extends CommonEntity
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var Event
     */
    private $event;

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
        $builder->setTable('event_contacts')
            ->setCustomRepositoryClass(EventContactRepository::class);

        $builder->addId();
        $builder->addManyToOne('event', Event::class, 'eventContacts', [
            'onDelete' => 'CASCADE',
        ]);
        $builder->addManyToOne('contact', Lead::class, 'eventContacts', [
            'onDelete' => 'NO ACTION',
        ]);

        // Indexes for faster lookup by event or contact
        $builder->addIndex(['event_id'], 'event_contacts_event_idx');
        $builder->addIndex(['contact_id'], 'event_contacts_contact_idx');
        $builder->addUniqueConstraint(['event_id', 'contact_id'], 'event_contacts_event_contact_uniq');

        $builder->addNullableField('dateAdded', Types::DATETIME_MUTABLE);
        $builder->addNullableField('dateModified', Types::DATETIME_MUTABLE);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

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
