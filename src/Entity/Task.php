<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $taskName;

    /**
     * @ORM\Column(type="smallint")
     */
    private $taskTime;

    /**
     * @ORM\Column(type="smallint")
     */
    private $taskDifficulty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskName(): ?string
    {
        return $this->taskName;
    }

    public function setTaskName(string $taskName): self
    {
        $this->taskName = $taskName;

        return $this;
    }

    public function getTaskTime(): ?int
    {
        return $this->taskTime;
    }

    public function setTaskTime(int $taskTime): self
    {
        $this->taskTime = $taskTime;

        return $this;
    }

    public function getTaskDifficulty(): ?int
    {
        return $this->taskDifficulty;
    }

    public function setTaskDifficulty(int $taskDifficulty): self
    {
        $this->taskDifficulty = $taskDifficulty;

        return $this;
    }
}
