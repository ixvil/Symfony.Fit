<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 24/04/2018
 * Time: 01:16
 */

namespace App\Controller\Client;


use App\Entity\Lesson;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $lessons = $this->getDoctrine()
            ->getRepository(Lesson::class)
            ->findBy([], ['startDateTime' => 'asc']);

        return $this->json($lessons, 200, ['Access-Control-Allow-Origin' => "*"]);
    }
}