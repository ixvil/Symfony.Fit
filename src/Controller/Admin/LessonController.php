<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Form\Lesson1Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
            ->findAll();

        return $this->render('lesson/index.html.twig', ['lessons' => $lessons]);
    }

    /**
     * @Route("/new", name="lesson_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(Lesson1Type::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lesson);
            $em->flush();

            return $this->redirectToRoute('lesson_index');
        }

        return $this->render('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_show", methods="GET")
     */
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', ['lesson' => $lesson]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods="GET|POST")
     */
    public function edit(Request $request, Lesson $lesson): Response
    {
        $form = $this->createForm(Lesson1Type::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lesson_edit', ['id' => $lesson->getId()]);
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_delete", methods="DELETE")
     */
    public function delete(Request $request, Lesson $lesson): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lesson);
            $em->flush();
        }

        return $this->redirectToRoute('lesson_index');
    }
}
