<?php

namespace App\Entity;

use App\Repository\EcolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcolesRepository::class)]
class Ecoles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $campus = null;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Absence::class)]
    private Collection $absence;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Articles::class)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Categories::class)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Delay::class)]
    private Collection $delay;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Event::class)]
    private Collection $event;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Lecons::class)]
    private Collection $lecons;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Materials::class)]
    private Collection $materials;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Matieres::class)]
    private Collection $matieres;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Notes::class)]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Programmes::class)]
    private Collection $programmes;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Room::class)]
    private Collection $room;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: Users::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'ecoles', targetEntity: StudentClass::class)]
    private Collection $studentClass;

    public function __construct()
    {
        $this->absence = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->delay = new ArrayCollection();
        $this->event = new ArrayCollection();
        $this->lecons = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->matieres = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->programmes = new ArrayCollection();
        $this->room = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->studentClass = new ArrayCollection();
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCampus(): ?string
    {
        return $this->campus;
    }

    public function setCampus(string $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsence(): Collection
    {
        return $this->absence;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absence->contains($absence)) {
            $this->absence->add($absence);
            $absence->setEcoles($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absence->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEcoles() === $this) {
                $absence->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setEcoles($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getEcoles() === $this) {
                $article->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categories $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setEcoles($this);
        }

        return $this;
    }

    public function removeCategory(Categories $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getEcoles() === $this) {
                $category->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Delay>
     */
    public function getDelay(): Collection
    {
        return $this->delay;
    }

    public function addDelay(Delay $delay): static
    {
        if (!$this->delay->contains($delay)) {
            $this->delay->add($delay);
            $delay->setEcoles($this);
        }

        return $this;
    }

    public function removeDelay(Delay $delay): static
    {
        if ($this->delay->removeElement($delay)) {
            // set the owning side to null (unless already changed)
            if ($delay->getEcoles() === $this) {
                $delay->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->event->contains($event)) {
            $this->event->add($event);
            $event->setEcoles($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->event->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getEcoles() === $this) {
                $event->setEcoles(null);
            }
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

    public function addLecon(Lecons $lecon): static
    {
        if (!$this->lecons->contains($lecon)) {
            $this->lecons->add($lecon);
            $lecon->setEcoles($this);
        }

        return $this;
    }

    public function removeLecon(Lecons $lecon): static
    {
        if ($this->lecons->removeElement($lecon)) {
            // set the owning side to null (unless already changed)
            if ($lecon->getEcoles() === $this) {
                $lecon->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Materials>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Materials $material): static
    {
        if (!$this->materials->contains($material)) {
            $this->materials->add($material);
            $material->setEcoles($this);
        }

        return $this;
    }

    public function removeMaterial(Materials $material): static
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getEcoles() === $this) {
                $material->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Matieres>
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matieres $matiere): static
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres->add($matiere);
            $matiere->setEcoles($this);
        }

        return $this;
    }

    public function removeMatiere(Matieres $matiere): static
    {
        if ($this->matieres->removeElement($matiere)) {
            // set the owning side to null (unless already changed)
            if ($matiere->getEcoles() === $this) {
                $matiere->setEcoles(null);
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

    public function addNote(Notes $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setEcoles($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEcoles() === $this) {
                $note->setEcoles(null);
            }
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

    public function addProgramme(Programmes $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setEcoles($this);
        }

        return $this;
    }

    public function removeProgramme(Programmes $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getEcoles() === $this) {
                $programme->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoom(): Collection
    {
        return $this->room;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->room->contains($room)) {
            $this->room->add($room);
            $room->setEcoles($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->room->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getEcoles() === $this) {
                $room->setEcoles(null);
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

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setEcoles($this);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getEcoles() === $this) {
                $user->setEcoles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StudentClass>
     */
    public function getStudentClass(): Collection
    {
        return $this->studentClass;
    }

    public function addStudentClass(StudentClass $studentClass): static
    {
        if (!$this->studentClass->contains($studentClass)) {
            $this->studentClass->add($studentClass);
            $studentClass->setEcoles($this);
        }

        return $this;
    }

    public function removeStudentClass(StudentClass $studentClass): static
    {
        if ($this->studentClass->removeElement($studentClass)) {
            // set the owning side to null (unless already changed)
            if ($studentClass->getEcoles() === $this) {
                $studentClass->setEcoles(null);
            }
        }

        return $this;
    }

}