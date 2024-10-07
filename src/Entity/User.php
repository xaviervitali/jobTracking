<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;



    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    /**
     * @var Collection<int, JobTracking>
     */
    #[ORM\OneToMany(targetEntity: JobTracking::class, mappedBy: 'user', cascade:['remove'])]
    private Collection $jobTrackings;

    /**
     * @var Collection<int, Job>
     */
    #[ORM\OneToMany(targetEntity: Job::class, mappedBy: 'user'
    , cascade:['remove'] )]
    private Collection $jobs;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'user', cascade:['remove'])]
    private Collection $notes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    /**
     * @var Collection<int, CV>
     */
    #[ORM\OneToMany(targetEntity: CV::class, mappedBy: 'user')]
    private Collection $cVs;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?AdzunaApiSettings $adzunaApiSettings = null;

    public function __construct()
    {
        $this->jobTrackings = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->cVs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }



  

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, JobTracking>
     */
    public function getJobTrackings(): Collection
    {
        return $this->jobTrackings;
    }

    public function addJobTracking(JobTracking $jobTracking): static
    {
        if (!$this->jobTrackings->contains($jobTracking)) {
            $this->jobTrackings->add($jobTracking);
            $jobTracking->setUser($this);
        }

        return $this;
    }

    public function removeJobTracking(JobTracking $jobTracking): static
    {
        if ($this->jobTrackings->removeElement($jobTracking)) {
            // set the owning side to null (unless already changed)
            if ($jobTracking->getUser() === $this) {
                $jobTracking->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): static
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
            $job->setUser($this);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getUser() === $this) {
                $job->setUser(null);
            }
        }

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
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, CV>
     */
    public function getCVs(): Collection
    {
        return $this->cVs;
    }

    public function addCV(CV $cV): static
    {
        if (!$this->cVs->contains($cV)) {
            $this->cVs->add($cV);
            $cV->setUser($this);
        }

        return $this;
    }

    public function removeCV(CV $cV): static
    {
        if ($this->cVs->removeElement($cV)) {
            // set the owning side to null (unless already changed)
            if ($cV->getUser() === $this) {
                $cV->setUser(null);
            }
        }

        return $this;
    }
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getAdzunaApiSettings(): ?AdzunaApiSettings
    {
        return $this->adzunaApiSettings;
    }

    public function setAdzunaApiSettings(?AdzunaApiSettings $adzunaApiSettings): static
    {
        // unset the owning side of the relation if necessary
        if ($adzunaApiSettings === null && $this->adzunaApiSettings !== null) {
            $this->adzunaApiSettings->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($adzunaApiSettings !== null && $adzunaApiSettings->getUser() !== $this) {
            $adzunaApiSettings->setUser($this);
        }

        $this->adzunaApiSettings = $adzunaApiSettings;

        return $this;
    }

}
