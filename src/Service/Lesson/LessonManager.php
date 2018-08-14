<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 29/07/2018
 * Time: 00:13
 */

namespace App\Service\Lesson;


use App\Entity\Lesson;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

class LessonManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    private $lessonRepository;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityManager $entityManager,
        Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->lessonRepository = $entityManager->getRepository(Lesson::class);
        $this->logger = $logger;
    }

    /**
     * @param Lesson $lesson
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function closeLesson(Lesson $lesson)
    {
        $lesson->setOverriddenUsersLimit(0);

        $this->entityManager->persist($lesson);
        $this->entityManager->flush($lesson);

        $this->logger->info("Closing lesson ".$lesson->getId());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function checkLessons()
    {
        $lessons = $this->lessonRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->isNull('overriddenUsersLimit'))
                ->orWhere(Criteria::expr()->neq('overriddenUsersLimit', 0))
                ->andWhere(Criteria::expr()->lte('startDateTime', new \DateTime("+1 hour")))
                ->andWhere(Criteria::expr()->gte('startDateTime', new \DateTime("today")))
        );

        /** @var Lesson $lesson */
        foreach ($lessons as $lesson) {
            if (count($lesson->getLessonUsers()) == 0) {
                $this->closeLesson($lesson);
            }
        }
    }
}