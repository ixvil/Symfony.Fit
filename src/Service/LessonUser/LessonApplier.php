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
use App\Entity\User;
use App\Entity\UserTicket;
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


    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->lessonUserRepository = $entityManager->getRepository(LessonUser::class);
        $this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
        $this->lessonUserStatusRepository = $entityManager->getRepository(LessonUserStatus::class);
    }

    /**
     * @param Lesson $lesson
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function unApplyToLesson(Lesson $lesson, User $user): bool
    {
        if ($lesson->getStartDateTime()->getTimestamp() - 60 * 60 * 6 < time()) {
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
     * @param User $user
     * @return bool
     * @throws ApplyToLessonException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function applyToLesson(Lesson $lesson, User $user): bool
    {
        if (count($lesson->getLessonUsers()) >= $lesson->getLessonSet()->getUsersLimit()) {
            throw new ApplyToLessonException('Записи на это занятие больше нет');
        }

        $lessonUserCollection = $this->lessonUserRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->eq('lesson', $lesson))
        );
        $userTicketCollection = $this->userTicketRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->gte('lessonsExpires', 1))
                ->andWhere(Criteria::expr()->eq('user', $user))
//                ->orderBy(['dateCreatedAt', 'ASC'])
                ->setMaxResults(1)
        );

        $this->logger->info('userId: ' . $user->getId() . ' tickets:' . $userTicketCollection->count());
        if ($userTicketCollection->count() < 1) {
            throw new ApplyToLessonException('У вас недостаточно свободных занятий');
        }

        /** @var UserTicket $userTicket */
        $userTicket = $userTicketCollection->current();
        if ($userTicket->getLessonsExpires() < 1) {
            throw new ApplyToLessonException('У вас недостаточно свободных занятий');
        }

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

        $this->entityManager->flush();
        $this->entityManager->detach($lesson);

        return true;
    }
}