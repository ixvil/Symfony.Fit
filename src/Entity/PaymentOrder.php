<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentOrderRepository")
 */
class PaymentOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentOrderStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TicketPlan")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ticketPlan;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserTicket", cascade={"persist", "remove"})
     */
    private $userTicket;

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getStatus(): ?PaymentOrderStatus
    {
        return $this->status;
    }

    public function setStatus(?PaymentOrderStatus $status): self
    {
        $this->status = $status;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUserTicket(): ?UserTicket
    {
        return $this->userTicket;
    }

    public function setUserTicket(?UserTicket $userTicket): self
    {
        $this->userTicket = $userTicket;

        return $this;
    }
}
