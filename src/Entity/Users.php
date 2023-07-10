<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?bool $online = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = null;

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: Articles::class)]
    private Collection $articles;

    #[ORM\ManyToMany(targetEntity: Articles::class, mappedBy: 'Favoris')]
    private Collection $favoris;

    #[ORM\ManyToMany(targetEntity: Lecons::class, mappedBy: 'users')]
    private Collection $lecons;

    #[ORM\ManyToMany(targetEntity: Programmes::class, mappedBy: 'users')]
    private Collection $programmes;

    #[ORM\Column(length: 10)]
    private ?string $telephone = null;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'students')]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Event::class)]
    private Collection $eventsteacher;

    #[ORM\ManyToOne(inversedBy: 'students')]
    private ?StudentClass $studentClass = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateOfBirth = null;

    #[ORM\Column(length: 255)]
    private ?string $idUnique = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Delay::class)]
    private Collection $delays;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Absence::class)]
    private Collection $absences;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notes::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->lecons = new ArrayCollection();
        $this->programmes = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->eventsteacher = new ArrayCollection();
        $this->delays = new ArrayCollection();
        $this->absences = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setUsers($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUsers() === $this) {
                $article->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Articles $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->addFavori($this);
        }

        return $this;
    }

    public function removeFavori(Articles $favori): self
    {
        if ($this->favoris->removeElement($favori)) {
            $favori->removeFavori($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Lecons>
     */
    public function getLecons(): Collection
    {
        return $this->lecons;
    }

    public function addLecon(Lecons $lecon): self
    {
        if (!$this->lecons->contains($lecon)) {
            $this->lecons->add($lecon);
            $lecon->addUser($this);
        }

        return $this;
    }

    public function removeLecon(Lecons $lecon): self
    {
        if ($this->lecons->removeElement($lecon)) {
            $lecon->removeUser($this);
        }

        return $this;
    }

    
    /**
     * @return Collection<int, Programmes>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programmes $programme): self
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->addUser($this);
        }

        return $this;
    }

    public function removeProgramme(Programmes $programme): self
    {
        if ($this->programmes->removeElement($programme)) {
            $programme->removeUser($this);
        }

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addStudent($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeStudent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsteacher(): Collection
    {
        return $this->eventsteacher;
    }

    public function addEventsteacher(Event $eventsteacher): self
    {
        if (!$this->eventsteacher->contains($eventsteacher)) {
            $this->eventsteacher->add($eventsteacher);
            $eventsteacher->setTeacher($this);
        }

        return $this;
    }

    public function removeEventsteacher(Event $eventsteacher): self
    {
        if ($this->eventsteacher->removeElement($eventsteacher)) {
            // set the owning side to null (unless already changed)
            if ($eventsteacher->getTeacher() === $this) {
                $eventsteacher->setTeacher(null);
            }
        }

        return $this;
    }

    public function getStudentClass(): ?StudentClass
    {
        return $this->studentClass;
    }

    public function setStudentClass(?StudentClass $studentClass): self
    {
        $this->studentClass = $studentClass;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->DateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $DateOfBirth): self
    {
        $this->DateOfBirth = $DateOfBirth;

        return $this;
    }

    public function getIdUnique(): ?string
    {
        return $this->idUnique;
    }

    public function setIdUnique(string $idUnique): self
    {
        $this->idUnique = $idUnique;

        return $this;
    }

    /**
     * @return Collection<int, Delay>
     */
    public function getDelays(): Collection
    {
        return $this->delays;
    }

    public function addDelay(Delay $delay): self
    {
        if (!$this->delays->contains($delay)) {
            $this->delays->add($delay);
            $delay->setStudent($this);
        }

        return $this;
    }

    public function removeDelay(Delay $delay): self
    {
        if ($this->delays->removeElement($delay)) {
            // set the owning side to null (unless already changed)
            if ($delay->getStudent() === $this) {
                $delay->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): self
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setStudent($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): self
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getStudent() === $this) {
                $absence->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notes>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

}
