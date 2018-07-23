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

        //Fucking hack to avoid circular exception
        /** @var Lesson $lesson */
        foreach ($lessons as $lesson) {
            $lesson->clearCircularReferences();
        }

        return $this->json($lessons, 200);
    }
}