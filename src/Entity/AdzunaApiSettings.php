<?php

namespace App\Entity;

use App\Repository\AdzunaApiSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AdzunaApiSettingsRepository::class)]
class AdzunaApiSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: 'adzunaApiSettings')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: 'adzunaApiSettings')]
    private ?string $what = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: 'adzunaApiSettings')]

    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(groups: 'adzunaApiSettings')]

    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    #[Groups(groups: 'adzunaApiSettings')]

    private ?int $distance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: 'adzunaApiSettings')]

    private ?string $whatExclude = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups(groups: 'adzunaApiSettings')]

    private ?array $whatOr = null;

    #[ORM\OneToOne(inversedBy: 'adzunaApiSettings', cascade: ['persist', 'remove'])]
    private ?User $user = null;

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

    public function getWhatOr(): ?array
    {
        return $this->whatOr;
    }

    public function setWhatOr(?array $whatOr): static
    {
        $this->whatOr = $whatOr;

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
