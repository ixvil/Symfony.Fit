<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserTicket
 *
 * @ORM\Table(name="user_ticket", indexes={@ORM\Index(name="user_tickets_ticket_plans_id_fk", columns={"ticket_plan_id"}), @ORM\Index(name="user_tickets_users_id_fk", columns={"user_id"})})
 * @ORM\Entity
 */
class UserTicket
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_created_at", type="datetime", nullable=true)
     */
    private $dateCreatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="lessons_expires", type="integer", nullable=true)
     */
    private $lessonsExpires;

    /**
     * @var \TicketPlan
     *
     * @ORM\ManyToOne(targetEntity="TicketPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ticket_plan_id", referencedColumnName="id")
     * })
     */
    private $ticketPlan;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LessonUser", mappedBy="userTicket")
     */
    private $lessonUsers;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreatedAt(): ?\DateTimeInterface
    {
        return $this->dateCreatedAt;
    }

    public function setDateCreatedAt(?\DateTimeInterface $dateCreatedAt): self
    {
        $this->dateCreatedAt = $dateCreatedAt;

        return $this;
    }

    public function getLessonsExpires(): ?int
    {
        return $this->lessonsExpires;
    }

    public function setLessonsExpires(?int $lessonsExpires): self
    {
        $this->lessonsExpires = $lessonsExpires;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|LessonUser[]
     */
    public function getLessonUsers(): ?Collection
    {
        return $this->lessonUsers;
    }

    public function setLessonUsers(?Collection $lessonUsers): self
    {
        $this->lessonUsers = $lessonUsers;

        return $this;
    }


}
