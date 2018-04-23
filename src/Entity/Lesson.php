<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lesson
 *
 * @ORM\Table(name="lesson", indexes={@ORM\Index(name="lessons_halls_id_fk", columns={"hall_id"}), @ORM\Index(name="lessons_lesson_sets_id_fk", columns={"lesson_set_id"})})
 * @ORM\Entity
 */
class Lesson
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
     * @var Hall
     *
     * @ORM\ManyToOne(targetEntity="Hall")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hall_id", referencedColumnName="id")
     * })
     */
    private $hall;

    /**
     * @var LessonSet
     *
     * @ORM\ManyToOne(targetEntity="LessonSet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lesson_set_id", referencedColumnName="id")
     * })
     */
    private $lessonSet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date_time", type="datetime", nullable=false)
     *
     */
    private $startDateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHall(): ?Hall
    {
        return $this->hall;
    }

    public function setHall(?Hall $hall): self
    {
        $this->hall = $hall;

        return $this;
    }

    public function getLessonSet(): ?LessonSet
    {
        return $this->lessonSet;
    }

    public function setLessonSet(?LessonSet $lessonSet): self
    {
        $this->lessonSet = $lessonSet;

        return $this;
    }

    /**
     * @param \DateTime $startDateTime
     * @return Lesson
     */
    public function setStartDateTime(\DateTime $startDateTime): Lesson
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }


}
