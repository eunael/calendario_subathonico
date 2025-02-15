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
        private ?string $finalTime = null,
        #[ORM\Column(length: 255)]
        private ?string $timeToUpdate = null,
        #[ORM\Column()]
        private ?int $totalDays = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFinalTime(): ?string
    {
        return $this->finalTime;
    }

    public function setFinalTime(string $finalTime): static
    {
        $this->finalTime = $finalTime;

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

    public function getTotalDays(): ?string
    {
        return $this->totalDays;
    }

    public function setTotalDays(string $totalDays): static
    {
        $this->totalDays = $totalDays;

        return $this;
    }
}
