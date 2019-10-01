<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SalaryByLessonRepository")
 */
class SalaryByLesson
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $trainer_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trainer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lesson;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="integer")
     */
    private $cnt;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $summ;

    public function getId()
    {
        return $this->id;
    }

    public function getTrainerId(): ?int
    {
        return $this->trainer_id;
    }

    public function setTrainerId(int $trainer_id): self
    {
        $this->trainer_id = $trainer_id;

        return $this;
    }

    public function getTrainer(): ?string
    {
        return $this->trainer;
    }

    public function setTrainer(string $trainer): self
    {
        $this->trainer = $trainer;

        return $this;
    }

    public function getLesson(): ?string
    {
        return $this->lesson;
    }

    public function setLesson(string $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
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

    public function getCnt(): ?int
    {
        return $this->cnt;
    }

    public function setCnt(int $cnt): self
    {
        $this->cnt = $cnt;

        return $this;
    }

    public function getSumm(): ?string
    {
        return $this->summ;
    }

    public function setSumm(string $summ): self
    {
        $this->summ = $summ;

        return $this;
    }
}
