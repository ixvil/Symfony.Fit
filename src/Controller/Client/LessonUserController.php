<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 08/05/2018
 * Time: 22:29
 */

namespace App\Controller\Client;


use App\Entity\Lesson;
use App\Entity\LessonUser;
use App\Entity\User;
use App\Entity\UserType;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lessonUser")
 */
class LessonUserController extends Controller
{

    /** @var \Doctrine\Common\Persistence\ObjectManager $entityManager */
    private $entityManager;

    /** @var EntityRepository $userRepo */
    private $userRepo;

    /** @var EntityRepository $userRepo */
    private $lessonUserRepository;

    /** @var EntityRepository $userRepo */
    private $lessonRepository;

    public function prep()
    {
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->userRepo = $this->getDoctrine()->getRepository(User::class);
        $this->lessonUserRepository = $this->getDoctrine()->getRepository(LessonUser::class);
        $this->lessonRepository = $this->getDoctrine()->getRepository(Lesson::class);
    }

    /**
     * @Route("/", name="lessonUser_post", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function post(Request $request): Response
    {
        $this->prep();
        $content = json_decode($request->getContent());
        $phoneNumber = $content->state->phone->phoneNumber;
        $lessonId = $content->state->dialog->id;

        $userCollection = $this->userRepo->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('phone', $phoneNumber))
        );
        if ($userCollection->count() == 0) {
            $user = new User();
            $user
                ->setPhone($phoneNumber)
                ->setName('auto')
                ->setType($this->entityManager->find(UserType::class, 1));
            $this->entityManager->persist($user);
        } else {
            $user = $userCollection->get(0);
        }
        /** @var Lesson $lesson */
        $lesson = $this->lessonRepository->find($lessonId);

        if (count($lesson->getLessonUsers()) >= $lesson->getLessonSet()->getUsersLimit()) {
            return new JsonResponse(
                json_encode(['error' => 'Записи на это занятие больше нет']),
                400,
                ['Access-Control-Allow-Origin' => "*"]
            );
        }

        $lessonUserCollection = $this->lessonUserRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->eq('lesson', $lesson))
        );
        if ($lessonUserCollection->count() == 0) {
            $lessonUser = new LessonUser();
            $lessonUser->setUser($user)->setLesson($lesson);
            $this->entityManager->persist($lessonUser);
        } else {
            return new JsonResponse(
                json_encode(['error' => 'Вы уже записаны на это занятие']),
                400,
                ['Access-Control-Allow-Origin' => "*"]
            );
        }

        $this->entityManager->flush();

        $lessons = $this->lessonRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->gte('startDateTime', new DateTime(date('Y-m-d'))))
                ->orderBy(['startDateTime' => 'ASC'])
        );

        //Fucking hack to avoid circular exception
        /** @var Lesson $lesson */
        foreach ($lessons as $lesson) {
            $lessonUsers = $lesson->getLessonUsers();
            /** @var LessonUser $lessonUser */
            foreach ($lessonUsers as $lessonUser) {
                $lessonUser->setLesson(null);
            }
        }

        return $this->json($lessons, 200, ['Access-Control-Allow-Origin' => "*"]);
    }
}