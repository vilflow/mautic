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
     * @var string|null
     */
    private $eventExternalId;

    /**
     * @var string|null
     */
    private $website;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string|null
     */
    private $country;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $registrationUrl;

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
        $builder->addField('eventExternalId', Types::STRING, ['columnName' => 'event_external_id', 'unique' => true]);
        $builder->addField('name', Types::STRING, ['columnName' => 'name']);
        $builder->addField('website', Types::STRING, ['nullable' => true]);
        $builder->addField('currency', Types::STRING, ['nullable' => true, 'length' => 3]);
        $builder->addField('country', Types::STRING, ['nullable' => true]);
        $builder->addField('city', Types::STRING, ['nullable' => true]);
        $builder->addField('registrationUrl', Types::STRING, ['columnName' => 'registration_url', 'nullable' => true]);
        $builder->addField('suitecrmId', Types::STRING, ['columnName' => 'suitecrm_id', 'nullable' => true]);
        $builder->addField('createdAt', Types::DATETIME_MUTABLE, ['columnName' => 'created_at', 'nullable' => true]);
        $builder->addField('updatedAt', Types::DATETIME_MUTABLE, ['columnName' => 'updated_at', 'nullable' => true]);

        $builder->createOneToMany('eventContacts', EventContact::class)
            ->mappedBy('event')
            // Removing an event should delete its associations but not cascade other operations
            ->cascadeRemove()
            ->fetchExtraLazy()
            ->build();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('eventExternalId', new NotBlank(['message' => 'mautic.events.event_external_id.required']));
        $metadata->addPropertyConstraint('name', new NotBlank(['message' => 'mautic.events.name.required']));
    }

    public function __construct()
    {
        $this->eventContacts = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function getEventExternalId(): ?string
    {
        return $this->eventExternalId;
    }

    public function setEventExternalId(string $eventExternalId): self
    {
        $this->eventExternalId = $eventExternalId;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getRegistrationUrl(): ?string
    {
        return $this->registrationUrl;
    }

    public function setRegistrationUrl(?string $registrationUrl): self
    {
        $this->registrationUrl = $registrationUrl;
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
