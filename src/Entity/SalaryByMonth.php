<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SalaryByMonthRepository")
 */
class SalaryByMonth
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
    private $year;
    private $date;
    /**
     * @ORM\Column(type="integer")
     */
    private $month;

    /**
     * @ORM\Column(type="integer")
     */
    private $trainer_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trainer;

    /**
     * @ORM\Column(type="integer")
     */
    private $cnt;

    /**
     * @ORM\Column(type="integer")
     */
    private $summ;

    public function getId()
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getDate() :string
    {
	return $this->year.'.'.$this->month;
    }
    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
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

    public function getCnt(): ?int
    {
        return $this->cnt;
    }

    public function setCnt(int $cnt): self
    {
        $this->cnt = $cnt;

        return $this;
    }

    public function getSumm(): ?int
    {
        return $this->summ;
    }

    public function setSumm(int $summ): self
    {
        $this->summ = $summ;

        return $this;
    }
}
