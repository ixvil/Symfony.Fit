<?php

namespace App\Controller\Admin;

use App\Entity\LessonType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lesson_type")
 */
class LessonTypeController extends Controller
{
    /**
     * @Route("/", name="lesson_type_index", methods="GET")
     */
    public function index(): Response
    {
        $lessonTypes = $this->getDoctrine()
            ->getRepository(LessonType::class)
            ->findAll();

        return $this->render('lesson_type/index.html.twig', ['lesson_types' => $lessonTypes]);
    }

    /**
     * @Route("/new", name="lesson_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $lessonType = new LessonType();
        $form = $this->createForm(LessonType::class, $lessonType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lessonType);
            $em->flush();

            return $this->redirectToRoute('lesson_type_index');
        }

        return $this->render('lesson_type/new.html.twig', [
            'lesson_type' => $lessonType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_type_show", methods="GET")
     */
    public function show(LessonType $lessonType): Response
    {
        return $this->render('lesson_type/show.html.twig', ['lesson_type' => $lessonType]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, LessonType $lessonType): Response
    {
        $form = $this->createForm(\App\Form\LessonType::class, $lessonType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lesson_type_edit', ['id' => $lessonType->getId()]);
        }

        return $this->render('lesson_type/edit.html.twig', [
            'lesson_type' => $lessonType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_type_delete", methods="DELETE")
     */
    public function delete(Request $request, LessonType $lessonType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lessonType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lessonType);
            $em->flush();
        }

        return $this->redirectToRoute('lesson_type_index');
    }
}
