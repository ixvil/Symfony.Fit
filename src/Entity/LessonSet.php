<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

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
     * @var LessonType
     * @MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="LessonType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lesson_type_id", referencedColumnName="id")
     * })
     */
    private $lessonType;

    /**
     * @var integer
     *
     * @ORM\Column(name="users_limit", type="integer")
     */
    private $usersLimit;

    /**
     * @var User
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trainer_user_id", referencedColumnName="id")
     * })
     */
    private $trainerUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $description;

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

    /**
     * @param int $usersLimit
     *
     * @return LessonSet
     */
    public function setUsersLimit(int $usersLimit): LessonSet
    {
        $this->usersLimit = $usersLimit;

        return $this;
    }

    /**
     * @return int
     */
    public function getUsersLimit(): int
    {
        return $this->usersLimit;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


}
