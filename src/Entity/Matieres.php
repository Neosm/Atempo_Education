<?php

namespace App\Entity;

use App\Repository\MatieresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatieresRepository::class)]
class Matieres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'matieres', targetEntity: Event::class)]
    private Collection $Event;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: Notes::class)]
    private Collection $notes;

    #[ORM\ManyToOne(inversedBy: 'matieres')]
    private ?Ecoles $ecoles = null;

    public function __construct()
    {
        $this->Event = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvent(): Collection
    {
        return $this->Event;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->Event->contains($event)) {
            $this->Event->add($event);
            $event->setMatieres($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->Event->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getMatieres() === $this) {
                $event->setMatieres(null);
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
            $note->setMatiere($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getMatiere() === $this) {
                $note->setMatiere(null);
            }
        }

        return $this;
    }

    public function getEcoles(): ?Ecoles
    {
        return $this->ecoles;
    }

    public function setEcoles(?Ecoles $ecoles): static
    {
        $this->ecoles = $ecoles;

        return $this;
    }


}
