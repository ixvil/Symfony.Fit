<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 20/07/2018
 * Time: 00:36
 */

namespace App\Service\LessonUser;


use App\Entity\Lesson;
use App\Entity\LessonUser;
use App\Entity\LessonUserStatus;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class Checker
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /** @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository */
    private $lessonUserRepository;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
        $this->lessonUserRepository = $entityManager->getRepository(LessonUser::class);
    }

    /**
     * @param int $lessonId
     * @param int $userId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function markAsChecked(int $lessonId, int $userId): bool
    {
        $lessonUserCollection = $this->lessonUserRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('lesson', $this->entityManager->find(Lesson::class, $lessonId)))
                ->andWhere(Criteria::expr()->eq('user', $this->entityManager->find(User::class, $userId)))
        );

        if ($lessonUserCollection->count() === 0) {
            return false;
        }

        /** @var LessonUser $lessonUser */
        $lessonUser = $lessonUserCollection->current();

        $lessonUser->setStatus($this->entityManager->find(LessonUserStatus::class, LessonUserStatus::APPROVED));
        $this->entityManager->persist($lessonUser);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param int $lessonId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function checkUserCanManage(User $user, int $lessonId): bool
    {
        /** @var Lesson $lesson */
        $lesson = $this->entityManager->find(Lesson::class, $lessonId);

        if ($user->getType()->getId() === UserType::ADMIN) {
            return true;
        }

        if ($lesson->getLessonSet()->getTrainerUser()->getId() === $user->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param int $lessonId
     * @return bool
     */
    public function checkUserCanClose(User $user, int $lessonId): bool
    {
        if ($user->getType()->getId() === UserType::ADMIN) {
            return true;
        }

        return false;
    }
}