<?php

declare(strict_types=1);

namespace App\Entity;

use App\EntityListener\FarmListener;
use App\Repository\FarmRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FarmRepository::class)]
#[ORM\EntityListeners([FarmListener::class])]
#[UniqueEntity(
    fields: ['siret'],
    message: 'Ce numéro siret est déjà enregistré chez nous.',
    entityClass: Farm::class,
    errorPath: 'siret'
)]
class Farm
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['json_read'])]
    private ?Uuid $id;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['json_read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 14, unique: true)]
    private ?string $siret = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['json_read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(['json_read'])]
    private ?string $slug = null;

    #[ORM\OneToOne(mappedBy: 'farm', targetEntity: Producer::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Producer $producer = null;

    #[ORM\Embedded(class: Address::class)]
    #[Groups(['json_read'])]
    private ?Address $address = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): Farm
    {
        $this->address = $address;
        return $this;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Farm
    {
        $this->name = $name;
        return $this;
    }

    public function getProducer(): ?Producer
    {
        return $this->producer;
    }

    public function setProducer(?Producer $producer): Farm
    {
        $this->producer = $producer;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Farm
    {
        $this->description = $description;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): Farm
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): Farm
    {
        $this->siret = $siret;
        return $this;
    }
}
