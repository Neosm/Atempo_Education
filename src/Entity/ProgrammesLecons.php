<?php

namespace App\Entity;

use App\Repository\ProgrammesLeconsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammesLeconsRepository::class)]
class ProgrammesLecons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'ProgrammesLecons', targetEntity: Programmes::class)]
    private Collection $programmes;

    #[ORM\OneToMany(mappedBy: 'type_lecons', targetEntity: Lecons::class)]
    private Collection $lecons;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
        $this->lecons = new ArrayCollection();
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
            $programme->setProgrammesLecons($this);
        }

        return $this;
    }

    public function removeProgramme(Programmes $programme): self
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getProgrammesLecons() === $this) {
                $programme->setProgrammesLecons(null);
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

    public function addLecon(Lecons $lecon): self
    {
        if (!$this->lecons->contains($lecon)) {
            $this->lecons->add($lecon);
            $lecon->setProgrammesLecons($this);
        }

        return $this;
    }

    public function removeLecon(Lecons $lecon): self
    {
        if ($this->lecons->removeElement($lecon)) {
            // set the owning side to null (unless already changed)
            if ($lecon->getProgrammesLecons() === $this) {
                $lecon->setProgrammesLecons(null);
            }
        }

        return $this;
    }
}
