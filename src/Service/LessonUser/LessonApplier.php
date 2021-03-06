<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 23/06/2018
 * Time: 18:43
 */

namespace App\Service\LessonUser;


use App\Entity\Lesson;
use App\Entity\LessonUser;
use App\Entity\LessonUserStatus;
use App\Entity\TicketPlan;
use App\Entity\User;
use App\Entity\UserTicket;
use App\Service\User\Finder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerAwareTrait;

class LessonApplier
{
	use LoggerAwareTrait;
	/**
	 * @var EntityManager
	 */
	private $entityManager;
	private $lessonUserRepository;
	private $userTicketRepository;
	private $lessonUserStatusRepository;
	private $ticketPlanRepository;

	/** @var Finder $finder */
	private $finder;


	public function __construct(
		EntityManager $entityManager,
		Finder $finder
	) {
		$this->entityManager = $entityManager;
		$this->lessonUserRepository = $entityManager->getRepository(LessonUser::class);
		$this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
		$this->lessonUserStatusRepository = $entityManager->getRepository(LessonUserStatus::class);
		$this->ticketPlanRepository = $entityManager->getRepository(TicketPlan::class);

		$this->finder = $finder;
	}

	/**
	 * @param Lesson $lesson
	 * @param User   $user
	 *
	 * @param bool   $force
	 *
	 * @return bool
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function unApplyToLesson(Lesson $lesson, User $user, $force = false): bool
	{
		if (!$force && $lesson->getStartDateTime()->getTimestamp() - 60 * 60 * 6 < time()) {
			throw new ApplyToLessonException('Отмена невозможна - до начала занятия осталось меньше 6 часов');
		}

		$lessonUserCollection = $this->lessonUserRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->eq('user', $user))
				->andWhere(Criteria::expr()->eq('lesson', $lesson))
		);

		if ($lessonUserCollection->count() == 0) {
			throw new ApplyToLessonException('Вы не записаны на это занятие');
		}
		/** @var LessonUser $lessonUser */
		$lessonUser = $lessonUserCollection->current();

		$userTicket = $lessonUser->getUserTicket();
		$userTicket->setLessonsExpires($userTicket->getLessonsExpires() + 1);

		$this->entityManager->persist($userTicket);
		$this->entityManager->remove($lessonUser);
		$this->entityManager->flush();

		return true;
	}

	/**
	 * @param Lesson $lesson
	 * @param string $phone
	 *
	 * @return bool
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function addToLesson(Lesson $lesson, string $phone): bool
	{
		$user = $this->finder->findByPhone($phone);

		$this->applyUser($lesson, $user);

		$this->entityManager->flush();
		$this->entityManager->detach($lesson);

		return true;
	}

	/**
	 * @param Lesson $lesson
	 * @param User   $user
	 *
	 * @return bool
	 * @throws ApplyToLessonException
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function applyToLesson(Lesson $lesson, User $user): bool
	{
		$limit = $lesson->getLessonSet()->getUsersLimit();
		if ($lesson->getOverriddenUsersLimit() !== null) {
			$limit = $lesson->getOverriddenUsersLimit();
		}

		if (count($lesson->getLessonUsers()) >= $limit) {
			throw new ApplyToLessonException('Записи на это занятие больше нет');
		}

		$this->applyUser($lesson, $user);

		$this->entityManager->flush();
		$this->entityManager->detach($lesson);

		return true;
	}

	/**
	 * @param User $user
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getActiveUserTickets(User $user): \Doctrine\Common\Collections\Collection
	{
		$userTicketCollection = $this->userTicketRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->gte('lessonsExpires', 1))
				->andWhere(Criteria::expr()->eq('user', $user))
				->andWhere(Criteria::expr()->eq('isActive', true))
				->andWhere(
					Criteria::expr()->in(
						'ticketPlan',
						$this->ticketPlanRepository->matching(
							Criteria::create()->andWhere(Criteria::expr()->in('type', [1, 2, 4]))
						)->toArray()
					)
				)
		);

		$this->logger->info('userId: '.$user->getId().' tickets:'.$userTicketCollection->count());
		if ($userTicketCollection->count() < 1) {
			throw new ApplyToLessonException('У вас недостаточно свободных занятий');
		}

		return $userTicketCollection;
}

	/**
	 * @param Lesson $lesson
	 * @param User   $user
	 *
	 * @return UserTicket|null
	 */
	public function getUnexpiredTicketForLesson(Lesson $lesson, User $user)
	{
		$userTicketCollection = $this->getActiveUserTickets($user);

		$goodTicket = null;
		/** @var UserTicket|null $userTicket */
		foreach ($userTicketCollection as $userTicket) {
			if ($userTicket->getExpirationDate() > $lesson->getStartDateTime()) {
				$goodTicket = $userTicket;
				break;
			}
		}

		if (!$goodTicket) {
			throw new ApplyToLessonException('На момент начала этого занятия вам абонемент будет уже недействителен');
		}

		return $userTicket;
}

	/**
	 * @param Lesson $lesson
	 * @param User   $user
	 *
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function applyUser(Lesson $lesson, User $user): void
	{
		$lessonUserCollection = $this->lessonUserRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->eq('user', $user))
				->andWhere(Criteria::expr()->eq('lesson', $lesson))
		);
		$userTicket = $this->getUnexpiredTicketForLesson($lesson, $user);

		if ($lessonUserCollection->count() == 0) {
			$lessonUser = new LessonUser();
			$lessonUser
				->setUser($user)
				->setLesson($lesson)
				->setStatus($this->lessonUserStatusRepository->find(1))
				->setUserTicket($userTicket);
			$userTicket->setLessonsExpires($userTicket->getLessonsExpires() - 1);
			$this->entityManager->persist($userTicket);
			$this->entityManager->persist($lessonUser);
		} else {
			throw new ApplyToLessonException('Вы уже записаны на это занятие');
		}
	}
}