<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups as AttributeGroups;


#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[AttributeGroups(["note"])]
    private ?int $id = null;

    #[ORM\Column]
    #[AttributeGroups(["note"])]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\Column(type: Types::TEXT)]   
    #[AttributeGroups(["note"])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[AttributeGroups(["note"])]
    private ?Job $job = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?User $user = null;

    #[ORM\Column(length: 8, nullable: true)]
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
