<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    #[Groups(['apiSettingsGroup'])]

    private ?string $inseeCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['apiSettingsGroup'])]
    private ?string $cityCode = null;

    #[ORM\Column]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 255)]
    private ?string $departmentNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $regionName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regionGeojsonName = null;

    #[ORM\Column(length: 255)]
    private ?string $departmentName = null;

    /**
     * @var Collection<int, JobSearchSettings>
     */
    #[ORM\OneToMany(targetEntity: JobSearchSettings::class, mappedBy: 'city')]
    private Collection $jobSearchSettings;

    public function __construct()
    {
        $this->jobSearchSettings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInseeCode(): ?string
    {
        return $this->inseeCode;
    }

    public function setInseeCode(string $inseeCode): static
    {
        $this->inseeCode = $inseeCode;

        return $this;
    }

    public function getCityCode(): ?string
    {
        return $this->cityCode;
    }

    public function setCityCode(string $cityCode): static
    {
        $this->cityCode = $cityCode;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLatitude(): ?int
    {
        return $this->latitude;
    }

    public function setLatitude(int $lattitude): static
    {
        $this->latitude = $lattitude;

        return $this;
    }

    public function getLongitude(): ?int
    {
        return $this->longitude;
    }

    public function setLongitude(int $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDepartmentNumber(): ?string
    {
        return $this->departmentNumber;
    }

    public function setDepartmentNumber(string $departmentNumber): static
    {
        $this->departmentNumber = $departmentNumber;

        return $this;
    }

    public function getRegionName(): ?string
    {
        return $this->regionName;
    }

    public function setRegionName(string $regionName): static
    {
        $this->regionName = $regionName;

        return $this;
    }

    public function getRegionGeojsonName(): ?string
    {
        return $this->regionGeojsonName;
    }

    public function setRegionGeojsonName(string $regionGeojsonName): static
    {
        $this->regionGeojsonName = $regionGeojsonName;

        return $this;
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function setDepartmentName(string $departmentName): static
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * @return Collection<int, JobSearchSettings>
     */
    public function getJobSearchSettings(): Collection
    {
        return $this->jobSearchSettings;
    }

    public function addJobSearchSetting(JobSearchSettings $jobSearchSetting): static
    {
        if (!$this->jobSearchSettings->contains($jobSearchSetting)) {
            $this->jobSearchSettings->add($jobSearchSetting);
            $jobSearchSetting->setCity($this);
        }

        return $this;
    }

    public function removeJobSearchSetting(JobSearchSettings $jobSearchSetting): static
    {
        if ($this->jobSearchSettings->removeElement($jobSearchSetting)) {
            // set the owning side to null (unless already changed)
            if ($jobSearchSetting->getCity() === $this) {
                $jobSearchSetting->setCity(null);
            }
        }

        return $this;
    }
}
