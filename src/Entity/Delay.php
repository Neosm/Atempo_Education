<?php

namespace App\Entity;

use App\Repository\DelayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelayRepository::class)]
class Delay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'delays')]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'delays')]
    private ?Users $student = null;

    #[ORM\Column(type: 'integer')]
    private ?int $delayMinutes = null;


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

    public function getDelayMinutes(): ?int
    {
        return $this->delayMinutes;
    }

    public function setDelayMinutes(?int $delayMinutes): self
    {
        $this->delayMinutes = $delayMinutes;

        return $this;
    }

    public function getDelayTime(): ?\DateTimeInterface
    {
        if ($this->delayMinutes !== null) {
            $delayTime = new \DateTime();
            $delayTime->setTime(0, $this->delayMinutes, 0);
            return $delayTime;
        }

        return null;
    }
}
