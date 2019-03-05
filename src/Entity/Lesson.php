<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

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
	 * @var int|null
	 *
	 * @ORM\Column(name="overridden_users_limit", type="integer", nullable=true)
	 */
	private $overriddenUsersLimit;

	/**
	 * @var Hall
	 * @MaxDepth(1)
	 *
	 * @ORM\ManyToOne(targetEntity="Hall")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="hall_id", referencedColumnName="id")
	 * })
	 */
	private $hall;

	/**
	 * @var LessonSet
	 * @MaxDepth(1)
	 * @ORM\ManyToOne(targetEntity="LessonSet")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="lesson_set_id", referencedColumnName="id")
	 * })
	 */
	private $lessonSet;

	/**
	 * @var Collection
	 * @MaxDepth(1)
	 * @ORM\OneToMany(targetEntity="LessonUser", mappedBy="lesson")
	 */
	private $lessonUsers;

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
	 *
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
	public function getStartDateTime(): ?\DateTime
	{
		return $this->startDateTime;
	}

	/**
	 * @param Collection $lessonUsers
	 *
	 * @return Lesson
	 */
	public function setLessonUsers(Collection $lessonUsers): Lesson
	{
		$this->lessonUsers = $lessonUsers;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getLessonUsers(): Collection
	{
		return $this->lessonUsers;
	}

	/**
	 * @param bool $isGuest
	 *
	 * @return $this
	 */
	public function clearCircularReferences($isGuest = true): Lesson
	{
		$lessonUsers = $this->getLessonUsers();
		/** @var LessonUser $lessonUser */
		foreach ($lessonUsers as $lessonUser) {
			$lessonUser->setLesson(null);
			$lessonUser->getUser()->setUserTickets(null);
			$lessonUser->getUserTicket()->setLessonUsers(null);
			$lessonUser->getUserTicket()->setUser(null);
			if ($isGuest) {
				$lessonUser->getUser()->setName('-');
				$lessonUser->getUser()->setPhone(md5($lessonUser->getUser()->getPhone()));
				$lessonUser->getUser()->setBonusBalance(0);

			}
		}

		return $this;
	}


	/**
	 * @param int|null $overriddenUsersLimit
	 *
	 * @return Lesson
	 */
	public function setOverriddenUsersLimit(int $overriddenUsersLimit = null): Lesson
	{
		$this->overriddenUsersLimit = $overriddenUsersLimit;

		return $this;
	}


	/**
	 * @return int|null
	 */
	public function getOverriddenUsersLimit(): ?int
	{
		return $this->overriddenUsersLimit;
	}

	public function __toString(): string
	{
		return $this->getLessonSet()->getLessonType()->getName().' ('.$this->getStartDateTime()->format('H:i:s d.m.Y')
			.')';
	}

}
