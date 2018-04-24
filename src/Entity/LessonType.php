<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LessonType
 *
 * @ORM\Table(name="lesson_type")
 * @ORM\Entity
 */
class LessonType
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
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=256, nullable=true)
     */
    private $image;


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

    /**
     * @param null|string $image
     * @return LessonType
     */
    public function setImage(?string $image): LessonType
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }


}
