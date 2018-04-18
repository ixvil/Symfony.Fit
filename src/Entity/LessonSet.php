<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LessonSet
 *
 * @ORM\Table(name="lesson_set", indexes={@ORM\Index(name="lesson_sets_users_id_fk", columns={"trainer_user_id"}), @ORM\Index(name="lesson_set_lesson_type_id_fk", columns={"lesson_type_id"})})
 * @ORM\Entity
 */
class LessonSet
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
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=256, nullable=true)
     */
    private $name;

    /**
     * @var \LessonType
     *
     * @ORM\ManyToOne(targetEntity="LessonType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lesson_type_id", referencedColumnName="id")
     * })
     */
    private $lessonType;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trainer_user_id", referencedColumnName="id")
     * })
     */
    private $trainerUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLessonType(): ?LessonType
    {
        return $this->lessonType;
    }

    public function setLessonType(?LessonType $lessonType): self
    {
        $this->lessonType = $lessonType;

        return $this;
    }

    public function getTrainerUser(): ?User
    {
        return $this->trainerUser;
    }

    public function setTrainerUser(?User $trainerUser): self
    {
        $this->trainerUser = $trainerUser;

        return $this;
    }


}
