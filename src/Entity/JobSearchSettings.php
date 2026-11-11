<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Repository\JobSearchSettingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: JobSearchSettingsRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['settings:read']]),
        new Get(normalizationContext: ['groups' => ['settings:read']]),
        new Post(
            normalizationContext: ['groups' => ['settings:read']],
            denormalizationContext: ['groups' => ['settings:write']]
        ),
        new Patch(
            normalizationContext: ['groups' => ['settings:read']],
            denormalizationContext: ['groups' => ['settings:write']]
        ),
    ]
)]
class JobSearchSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['settings:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apiSettingsGroup', 'settings:read', 'settings:write'])]
    private ?string $what = null;



    #[ORM\Column(length: 255)]
    #[Groups(['settings:read', 'settings:write'])]
    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['apiSettingsGroup', 'settings:read', 'settings:write'])]

    private ?int $distance = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['apiSettingsGroup', 'settings:read', 'settings:write'])]
    private ?string $whatExclude = null;


    #[ORM\OneToOne(inversedBy: 'apiSettings', cascade: ['persist', 'remove'])]
    
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $whatOr = null;

    #[ORM\ManyToOne(inversedBy: 'jobSearchSettings')]
    private ?City $city = null;

    #[ORM\Column(nullable: true)]
    #[ORM\ManyToOne(inversedBy: 'jobSearchSettings')]
    #[Groups(['settings:read', 'settings:write'])]
    private ?int $maxDaysOld = null;

    /**
     * @var Collection<int, JobApiServices>
     */
    #[ORM\ManyToMany(targetEntity: JobApiServices::class, mappedBy: 'jobSearchSettings')]
    #[Groups(['apiSettingsGroup'])]
    private Collection $jobApiServices;

    public function __construct()
    {
        $this->jobApiServices = new ArrayCollection();
    }




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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getMaxDaysOld(): ?int
    {
        return $this->maxDaysOld;
    }

    public function setMaxDaysOld(?int $maxDaysOld): static
    {
        $this->maxDaysOld = $maxDaysOld;

        return $this;
    }

    /**
     * @return Collection<int, JobApiServices>
     */
    public function getJobApiServices(): Collection
    {
        return $this->jobApiServices;
    }

    public function addJobApiService(JobApiServices $jobApiService): static
    {
        if (!$this->jobApiServices->contains($jobApiService)) {
            $this->jobApiServices->add($jobApiService);
            $jobApiService->addJobSearchSetting($this);
        }

        return $this;
    }

    public function removeJobApiService(JobApiServices $jobApiService): static
    {
        
     $this->jobApiServices->removeElement($jobApiService);
            $jobApiService->removeJobSearchSetting($this);

        return $this;
    }


}
