<?php

namespace App\Entity;

use App\Repository\LeconsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeconsRepository::class)]
class Lecons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnails = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $audio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'lecons')]
    private Collection $users;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'lecons')]
    private ?Programmes $programmes = null;

    #[ORM\ManyToOne(inversedBy: 'lecons')]
    private ?ProgrammesLecons $type_lecons = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
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

    public function getThumbnails(): ?string
    {
        return $this->thumbnails;
    }

    public function setThumbnails(string $thumbnails): self
    {
        $this->thumbnails = $thumbnails;

        return $this;
    }

    public function getAudio(): ?string
    {
        return $this->audio;
    }

    public function setAudio(?string $audio): self
    {
        $this->audio = $audio;

        return $this;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProgrammes(): ?Programmes
    {
        return $this->programmes;
    }

    public function setProgrammes(?Programmes $programmes): self
    {
        $this->programmes = $programmes;

        return $this;
    }

    public function getProgrammesLecons(): ?ProgrammesLecons
    {
        return $this->type_lecons;
    }

    public function setProgrammesLecons(?ProgrammesLecons $type_lecons): self
    {
        $this->type_lecons = $type_lecons;

        return $this;
    }
}
