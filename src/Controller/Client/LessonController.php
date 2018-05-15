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
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/lesson")
 */
class LessonController extends Controller
{
    /**
     * @Route("/", name="lesson_index", methods="GET")
     */
    public function index(): Response
    {
        /** @var EntityRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Lesson::class);

        $lessons = $repo->matching(
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