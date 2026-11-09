<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\AddressBookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressBookRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['address_book:read']],
    denormalizationContext: ['groups' => ['address_book:write']],
    security: "is_granted('ROLE_USER')",
    operations: [
        new GetCollection(),
        new Get(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Post(),
        new Patch(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Delete(security: "is_granted('ROLE_USER') and object.getUser() == user"),
    ]
)]
class AddressBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['address_book:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address_book:read', 'address_book:write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address_book:read', 'address_book:write'])]
    private ?string $lastName = null;

    #[Groups(['address_book:read', 'address_book:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address_book:read', 'address_book:write'])]
    private ?string $company = null;

    #[ORM\Column]
    #[Groups(['address_book:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['address_book:read', 'address_book:write'])]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['address_book:read', 'address_book:write'])]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
