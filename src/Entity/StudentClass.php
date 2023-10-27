<?php

namespace App\Entity;

use App\Repository\StudentClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentClassRepository::class)]
class StudentClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'studentClass', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'studentClass', targetEntity: Users::class)]
    private Collection $students;

    #[ORM\ManyToOne(inversedBy: 'studentClass')]
    private ?Ecoles $ecoles = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->students = new ArrayCollection();
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
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setStudentClass($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getStudentClass() === $this) {
                $event->setStudentClass(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Users $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setStudentClass($this);
        }

        return $this;
    }

    public function removeStudent(Users $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getStudentClass() === $this) {
                $student->setStudentClass(null);
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
