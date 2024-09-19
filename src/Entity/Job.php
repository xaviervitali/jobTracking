<?php

namespace App\Entity;


use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups as AttributeGroups;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[AttributeGroups(["job"])]
    private ?int $id = null;

    #[AttributeGroups(["job"])]
    #[ORM\Column(length: 255)]
    private ?string $recruiter = null;

    #[AttributeGroups(["job"])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[AttributeGroups(["job"])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $offerDescription = null;

    #[AttributeGroups(["job"])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    #[AttributeGroups(["job"])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $coverLetter = null;

    #[AttributeGroups(["job"])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    /**
     * @var Collection<int, JobTracking>
     */


    #[ORM\OneToMany(targetEntity: JobTracking::class, mappedBy: 'job', orphanRemoval: true)]
    private Collection $jobTracking;

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    private ?User $user = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'job', orphanRemoval: true)]
    private Collection $notes;



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



    public function getCoverLetter(): ?string
    {
        return $this->coverLetter;
    }

    public function setCoverLetter(?string $coverLetter): static
    {
        $this->coverLetter = $coverLetter;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }


    /**
     * @return Collection<int, JobTracking>
     */
    public function getJobTracking(): Collection
    {
        return $this->jobTracking;
    }

    public function getJobTrackingByUser(User $user): Collection
    {
        return $this->jobTracking->get;
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
}
