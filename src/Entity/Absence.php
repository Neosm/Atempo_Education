<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    private ?Users $student = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $absenceDate = null;

    #[ORM\ManyToOne(inversedBy: 'absence')]
    private ?Ecoles $ecoles = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getStudent(): ?Users
    {
        return $this->student;
    }

    public function setStudent(?Users $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getAbsenceDate(): ?\DateTimeInterface
    {
        return $this->absenceDate;
    }

    public function setAbsenceDate(\DateTimeInterface $absenceDate): self
    {
        $this->absenceDate = $absenceDate;

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
