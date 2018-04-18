<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LessonUser
 *
 * @ORM\Table(name="lesson_user", indexes={@ORM\Index(name="lesson_users_lessons_id_fk", columns={"lesson_id"}), @ORM\Index(name="lesson_users_users_id_fk", columns={"user_id"}), @ORM\Index(name="lesson_users_user_tickets_id_fk", columns={"user_ticket_id"})})
 * @ORM\Entity
 */
class LessonUser
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
     * @var \Lesson
     *
     * @ORM\ManyToOne(targetEntity="Lesson")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lesson_id", referencedColumnName="id")
     * })
     */
    private $lesson;

    /**
     * @var \UserTicket
     *
     * @ORM\ManyToOne(targetEntity="UserTicket")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_ticket_id", referencedColumnName="id")
     * })
     */
    private $userTicket;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
