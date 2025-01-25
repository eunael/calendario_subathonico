<?php

namespace App\Entity;

use App\Repository\TimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeRepository::class)]
class Time
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    public function __construct(
        #[ORM\Column(length: 255)]
        private ?string $timestamp = null,
        #[ORM\Column(length: 255)]
        private ?string $timeToUpdate = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimeToUpdate(): ?string
    {
        return $this->timeToUpdate;
    }

    public function setTimeToUpdate(string $timeToUpdate): static
    {
        $this->timeToUpdate = $timeToUpdate;

        return $this;
    }
}
