<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 29/07/2018
 * Time: 00:13
 */

namespace App\Service\Lesson;


use App\Entity\Lesson;
use Doctrine\ORM\EntityManager;

class LessonManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Lesson $lesson
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function closeLesson(Lesson $lesson)
    {
        $lesson->setOverriddenUsersLimit(0);

        $this->entityManager->persist($lesson);
        $this->entityManager->flush($lesson);

    }
}