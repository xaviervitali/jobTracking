<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups as AttributeGroups;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['note:read']],
    denormalizationContext: ['groups' => ['note:write']],
    security: "is_granted('ROLE_USER')",
    operations: [
        new GetCollection(),
        new Get(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Post(),
        new Patch(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Delete(security: "is_granted('ROLE_USER') and object.getUser() == user"),
    ]
)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[AttributeGroups(["note:read"])]
    private ?int $id = null;

    #[ORM\Column]
    #[AttributeGroups(["note:read"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[AttributeGroups(["note:read", "note:write"])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[AttributeGroups(["note:read", "note:write"])]
    private ?Job $job = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?User $user = null;

    #[ORM\Column(length: 8, nullable: true)]
    #[AttributeGroups(["note:read", "note:write"])]
    private ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
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


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): static
    {
        $this->job = $job;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
