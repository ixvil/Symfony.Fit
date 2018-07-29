<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 24/04/2018
 * Time: 01:16
 */

namespace App\Controller\Client;


use App\Entity\Lesson;
use App\Entity\LessonUser;
use App\Service\Auth\Token\TokenGenerator;
use App\Service\Lesson\LessonManager;
use App\Service\Lesson\Manager;
use App\Service\LessonUser\Checker;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    /**
     * @var Checker
     */
    private $checker;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var LessonManager
     */
    private $lessonManager;

    public function __construct(
        TokenGenerator $tokenGenerator,
        Checker $checker,
        EntityManager $entityManager,
        LessonManager $lessonManager
    )
    {
        $this->checker = $checker;
        $this->entityManager = $entityManager;
        $this->lessonManager = $lessonManager;
        parent::__construct($tokenGenerator);
    }

    /**
     * @Route("/", name="lesson_index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        /** @var EntityRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Lesson::class);

        $lessons = $repo->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->gte('startDateTime', new DateTime(date('Y-m-d'))))
                ->orderBy(['startDateTime' => 'ASC'])
        );

        /** @var Lesson $lesson */
        foreach ($lessons as $lesson) {
            $lesson->clearCircularReferences();
        }

        return $this->json($lessons, 200);
    }

    /**
     * @Route("/close", name="lessonClose_post", methods="POST")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function close(Request $request): Response
    {
        $this->auth($request);
        $user = $this->getCurrentUser();
        $content = json_decode($request->getContent());
        $lessonId = $content->lessonId;

        if (!$this->checker->checkUserCanClose($user, $lessonId)) {
            return $this->json(['error' => 'You can\'t manage this lesson']);
        }

        /** @var Lesson $lesson */
        $lesson = $this->entityManager->find(Lesson::class, $lessonId);

        $this->lessonManager->closeLesson($lesson);
        return $this->json(['lesson' => $lesson->clearCircularReferences()]);

    }
}