<?php

namespace App\Entity;

use App\Repository\ProgrammesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammesRepository::class)]
class Programmes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'programmes', targetEntity: Lecons::class)]
    private Collection $lecons;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'programmes')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'programmes')]
    private ?ProgrammesLecons $ProgrammesLecons = null;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'programme')]
    private Collection $events;

    public function __construct()
    {
        $this->lecons = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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
            $lecon->setProgrammes($this);
        }

        return $this;
    }

    public function removeLecon(Lecons $lecon): self
    {
        if ($this->lecons->removeElement($lecon)) {
            // set the owning side to null (unless already changed)
            if ($lecon->getProgrammes() === $this) {
                $lecon->setProgrammes(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(Users $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getProgrammesLecons(): ?ProgrammesLecons
    {
        return $this->ProgrammesLecons;
    }

    public function setProgrammesLecons(?ProgrammesLecons $ProgrammesLecons): self
    {
        $this->ProgrammesLecons = $ProgrammesLecons;

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
            $event->addProgramme($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeProgramme($this);
        }

        return $this;
    }
}
