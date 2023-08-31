<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private $start;

    #[ORM\Column(type: 'integer')]
    private $duration;

    #[ORM\Column(type: 'datetime')]
    private $end;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comment;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Room $room = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?StudentClass $studentClass = null;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'events')]
    private Collection $students;

    #[ORM\ManyToOne(inversedBy: 'eventsteacher')]
    private ?Users $teacher = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $idUnique = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Delay::class)]
    private Collection $delays;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Absence::class)]
    private Collection $absences;

    #[ORM\ManyToOne(inversedBy: 'Event')]
    private ?Matieres $matieres;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objectif = null;

    #[ORM\ManyToMany(targetEntity: programmes::class, inversedBy: 'events')]
    private Collection $programme;

    #[ORM\ManyToMany(targetEntity: lecons::class, inversedBy: 'events')]
    private Collection $Lecons;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ZoomLink = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->delays = new ArrayCollection();
        $this->absences = new ArrayCollection();
        $this->programme = new ArrayCollection();
        $this->Lecons = new ArrayCollection();
    }

    public function getTitleAndStart(): string
    {
        return $this->getTitle() . ' - ' . $this->getStart()->format('d F Y à H:i');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getStudentClass(): ?studentClass
    {
        return $this->studentClass;
    }

    public function setStudentClass(?studentClass $studentClass): self
    {
        $this->studentClass = $studentClass;

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
        }

        return $this;
    }

    public function removeStudent(Users $student): self
    {
        $this->students->removeElement($student);

        return $this;
    }

    public function getTeacher(): ?Users
    {
        return $this->teacher;
    }

    public function setTeacher(?Users $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
            $delay->setEvent($this);
        }

        return $this;
    }

    public function removeDelay(Delay $delay): self
    {
        if ($this->delays->removeElement($delay)) {
            // set the owning side to null (unless already changed)
            if ($delay->getEvent() === $this) {
                $delay->setEvent(null);
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
            $absence->setEvent($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): self
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEvent() === $this) {
                $absence->setEvent(null);
            }
        }

        return $this;
    }

    public function getMatieres(): ?Matieres
    {
        return $this->matieres;
    }

    public function setMatieres(?Matieres $matieres): self
    {
        $this->matieres = $matieres;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(?string $objectif): self
    {
        $this->objectif = $objectif;

        return $this;
    }

    /**
     * @return Collection<int, programmes>
     */
    public function getProgramme(): Collection
    {
        return $this->programme;
    }

    public function addProgramme(programmes $programme): self
    {
        if (!$this->programme->contains($programme)) {
            $this->programme->add($programme);
        }

        return $this;
    }

    public function removeProgramme(programmes $programme): self
    {
        $this->programme->removeElement($programme);

        return $this;
    }

    /**
     * @return Collection<int, lecons>
     */
    public function getLecons(): Collection
    {
        return $this->Lecons;
    }

    public function addLecon(lecons $lecon): self
    {
        if (!$this->Lecons->contains($lecon)) {
            $this->Lecons->add($lecon);
        }

        return $this;
    }

    public function removeLecon(lecons $lecon): self
    {
        $this->Lecons->removeElement($lecon);

        return $this;
    }

    public function getZoomLink(): ?string
    {
        return $this->ZoomLink;
    }

    public function setZoomLink(?string $ZoomLink): self
    {
        $this->ZoomLink = $ZoomLink;

        return $this;
    }


}
