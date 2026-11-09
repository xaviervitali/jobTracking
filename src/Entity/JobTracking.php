<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\JobTrackingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: JobTrackingRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['job_tracking:read']],
    denormalizationContext: ['groups' => ['job_tracking:write']],
    security: "is_granted('ROLE_USER')",
    operations: [
        new GetCollection(),
        new Get(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Post(),
        new Delete(security: "is_granted('ROLE_USER') and object.getUser() == user"),
    ]
)]
class JobTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["job_tracking:read"])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["job_tracking:read"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'jobTrackings')]
    #[Groups(["job_tracking:read", "job_tracking:write"])]
    private ?Action $action = null;

    #[ORM\ManyToOne(inversedBy: 'jobTracking')]
    #[Groups(["job_tracking:read", "job_tracking:write"])]
    private ?Job $job = null;

    #[ORM\ManyToOne(inversedBy: 'jobTrackings')]
    private ?User $user = null;

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


    public function getAction(): ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action): static
    {
        $this->action = $action;

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


}
