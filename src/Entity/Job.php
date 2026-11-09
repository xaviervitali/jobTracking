<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['job:read']],
    denormalizationContext: ['groups' => ['job:write']],
    security: "is_granted('ROLE_USER')",
    operations: [
        new GetCollection(),
        new Get(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Post(),
        new Patch(security: "is_granted('ROLE_USER') and object.getUser() == user"),
        new Delete(security: "is_granted('ROLE_USER') and object.getUser() == user"),
    ]
)]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["job:read"])]
    private ?int $id = null;

    #[Groups(["job:read", "job:write"])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $recruiter = null;

    #[Groups(["job:read", "job:write"])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[Groups(["job:read", "job:write"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $offerDescription = null;

    #[Groups(["job:read"])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    /**
     * @var Collection<int, JobTracking>
     */


    #[ORM\OneToMany(targetEntity: JobTracking::class, mappedBy: 'job', cascade:['remove'])]
    private Collection $jobTracking;

    #[ORM\ManyToOne(inversedBy: 'job')]
    private ?User $user = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'job',  cascade:['remove'])]
    private Collection $notes;

  

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    #[Groups(["job:read", "job:write"])]
    private ?JobSource $source = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["job:read", "job:write"])]
    private ?string $url = null;





    public function __construct()
    {
        $this->jobTracking = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecruiter(): ?string
    {
        return $this->recruiter;
    }

    public function setRecruiter(string $recruiter): static
    {
        $this->recruiter = $recruiter;

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

    public function getOfferDescription(): ?string
    {
        return $this->offerDescription;
    }

    public function setOfferDescription(string $offerDescription): static
    {
        $this->offerDescription = $offerDescription;

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






    /**
     * @return Collection<int, JobTracking>
     */
    public function getJobTracking(): Collection
    {
        return $this->jobTracking;
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

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setJob($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getJob() === $this) {
                $note->setJob(null);
            }
        }

        return $this;
    }



    public function getSource(): ?JobSource
    {
        return $this->source;
    }

    public function setSource(?JobSource $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }


}
