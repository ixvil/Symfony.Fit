<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketPlan
 *
 * @ORM\Table(name="ticket_plan", indexes={@ORM\Index(name="ticket_plans_ticket_plan_types_id_fk", columns={"type_id"})})
 * @ORM\Entity
 */
class TicketPlan
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
     * @var int|null
     *
     * @ORM\Column(name="lessons_count", type="integer", nullable=true)
     */
    private $lessonsCount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="days_to_outdated", type="integer", nullable=true)
     */
    private $daysToOutdated;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=256, nullable=true)
     */
    private $name;

    /**
     * @var \TicketPlanType
     *
     * @ORM\ManyToOne(targetEntity="TicketPlanType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLessonsCount(): ?int
    {
        return $this->lessonsCount;
    }

    public function setLessonsCount(?int $lessonsCount): self
    {
        $this->lessonsCount = $lessonsCount;

        return $this;
    }

    public function getDaysToOutdated(): ?int
    {
        return $this->daysToOutdated;
    }

    public function setDaysToOutdated(?int $daysToOutdated): self
    {
        $this->daysToOutdated = $daysToOutdated;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
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

    public function getType(): ?TicketPlanType
    {
        return $this->type;
    }

    public function setType(?TicketPlanType $type): self
    {
        $this->type = $type;

        return $this;
    }


}
