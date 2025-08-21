<?php

namespace MauticPlugin\MauticEventsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\CommonEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MauticPlugin\MauticEventsBundle\Entity\EventContact;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 */
class Event extends CommonEntity
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
     * @var Collection<int, EventContact>
     */
    private $eventContacts;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('events')
            ->setCustomRepositoryClass(EventRepository::class);

        $builder->addId();
        $builder->addField('name', Types::STRING);

        $builder->createOneToMany('eventContacts', EventContact::class)
            ->mappedBy('event')
            // Removing an event should delete its associations but not cascade other operations
            ->cascadeRemove()
            ->fetchExtraLazy()
            ->build();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank(['message' => 'mautic.core.name.required']));
    }

    public function __construct()
    {
        $this->eventContacts = new ArrayCollection();
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

        return $this;
    }

    /**
     * @return Collection<int, EventContact>
     */
    public function getEventContacts(): Collection
    {
        return $this->eventContacts;
    }

    public function addEventContact(EventContact $eventContact): self
    {
        if (!$this->eventContacts->contains($eventContact)) {
            $this->eventContacts->add($eventContact);
            $eventContact->setEvent($this);
        }

        return $this;
    }

    public function removeEventContact(EventContact $eventContact): self
    {
        if ($this->eventContacts->removeElement($eventContact)) {
            $eventContact->setEvent($this);
        }

        return $this;
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
