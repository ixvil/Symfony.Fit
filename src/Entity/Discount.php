<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiscountRepository")
 */
class Discount
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TicketPlan")
     */
    private $ticketPlan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $activeFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $activeTo;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTicketPlan(): ?TicketPlan
    {
        return $this->ticketPlan;
    }

    public function setTicketPlan(?TicketPlan $ticketPlan): self
    {
        $this->ticketPlan = $ticketPlan;

        return $this;
    }

    public function getActiveFrom(): ?\DateTimeInterface
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(\DateTimeInterface $activeFrom): self
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    public function getActiveTo(): ?\DateTimeInterface
    {
        return $this->activeTo;
    }

    public function setActiveTo(\DateTimeInterface $activeTo): self
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    /**
     * @param mixed $value
     * @return Discount
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
