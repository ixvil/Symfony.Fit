<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="users_user_types_id_fk", columns={"type_id"})})
 * @ORM\Entity
 */
class User
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
     * @ORM\Column(name="phone", type="string", length=64, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=256, nullable=true)
     */
    private $name;

    /**
     * @var Collection
     * @MaxDepth(1)
     * @ORM\OneToMany(targetEntity="App\Entity\UserTicket", mappedBy="user")
     */
    private $userTickets;

    /**
     * @var UserType
     * @MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="UserType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getType(): ?UserType
    {
        return $this->type;
    }

    public function setType(?UserType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param Collection $userTickets
     * @return User
     */
    public function setUserTickets(?Collection $userTickets): User
    {
        $this->userTickets = $userTickets;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserTickets(): ?Collection
    {
        return $this->userTickets;
    }

    /**
     * @return $this
     */
    public function clearCircularReferences()
    {
        if ($this->userTickets != null) {
            /** @var UserTicket $userTicket */
            foreach ($this->userTickets as $userTicket) {
                $userTicket->setUser(null);
                $lessonUsers = $userTicket->getLessonUsers();
                foreach ($lessonUsers as $lessonUser) {
                    $lessonUser->setUserTicket(null);
                    $lessonUser->setUser(null);
                    $lessonUser->getLesson()->setLessonUsers(new ArrayCollection());
                }
            }
        }
        return $this;
    }

}
