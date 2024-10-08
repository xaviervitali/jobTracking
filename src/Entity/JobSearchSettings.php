<?php

namespace App\Entity;

use App\Repository\JobSearchSettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: JobSearchSettingsRepository::class)]
class JobSearchSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apiSettingsGroup'])]
    private ?string $what = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apiSettingsGroup'])]

    private ?string $city = null;

    #[ORM\Column(length: 255)]

    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['apiSettingsGroup'])]

    private ?int $distance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['apiSettingsGroup'])]
    private ?string $whatExclude = null;


    #[ORM\OneToOne(inversedBy: 'apiSettings', cascade: ['persist', 'remove'])]
    
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $whatOr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWhat(): ?string
    {
        return $this->what;
    }

    public function setWhat(string $what): static
    {
        $this->what = $what;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getWhatExclude(): ?string
    {
        return $this->whatExclude;
    }

    public function setWhatExclude(?string $whatExclude): static
    {
        $this->whatExclude = $whatExclude;

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

    public function getWhatOr(): ?string
    {
        return $this->whatOr;
    }

    public function setWhatOr(?string $whatOr): static
    {
        $this->whatOr = $whatOr;

        return $this;
    }
}
