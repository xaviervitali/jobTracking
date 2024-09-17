<?php

namespace App\Entity;

use App\Repository\JobTrackingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobTrackingRepository::class)]
class JobTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;




    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\ManyToOne(inversedBy: 'jobTrackings')]
    private ?Action $action = null;

    #[ORM\ManyToOne(inversedBy: 'jobTracking')]
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
