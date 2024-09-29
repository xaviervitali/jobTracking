<?php

namespace App\Entity;

use App\Repository\CVRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CVRepository::class)]
class CV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\ManyToOne(inversedBy: 'cVs')]
    private ?User $user = null;
    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'cv', fileNameProperty: 'cvName', size: 'cvSize')]
    private ?File $cvFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $cvName = null;

    #[ORM\Column(nullable: true)]
    private ?int $cvSize = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $cvFile
     */
    public function setCVFile(?File $cvFile = null): void
    {
        $this->cvFile = $cvFile;

        if (null !== $cvFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getCVFile(): ?File
    {
        return $this->cvFile;
        if (null !== $cvFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function setCVName(?string $cvName): void
    {
        $this->cvName = $cvName;
    }

    public function getCVName(): ?string
    {
        return $this->cvName;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function setCVSize(?int $cvSize): void
    {
        $this->cvSize = $cvSize;
    }



    public function getCVSize(): ?int
    {
        return $this->cvSize;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
