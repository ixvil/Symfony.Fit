<?php

namespace App\Controller\Admin;

use App\Entity\Hall;
use App\Form\HallType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hall")
 */
class HallController extends Controller
{
    /**
     * @Route("/", name="hall_index", methods="GET")
     */
    public function index(): Response
    {
        $halls = $this->getDoctrine()
            ->getRepository(Hall::class)
            ->findAll();

        return $this->render('hall/index.html.twig', ['halls' => $halls]);
    }

    /**
     * @Route("/new", name="hall_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $hall = new Hall();
        $form = $this->createForm(HallType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($hall);
            $em->flush();

            return $this->redirectToRoute('hall_index');
        }

        return $this->render('hall/new.html.twig', [
            'hall' => $hall,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hall_show", methods="GET")
     */
    public function show(Hall $hall): Response
    {
        return $this->render('hall/show.html.twig', ['hall' => $hall]);
    }

    /**
     * @Route("/{id}/edit", name="hall_edit", methods="GET|POST")
     */
    public function edit(Request $request, Hall $hall): Response
    {
        $form = $this->createForm(HallType::class, $hall);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hall_edit', ['id' => $hall->getId()]);
        }

        return $this->render('hall/edit.html.twig', [
            'hall' => $hall,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hall_delete", methods="DELETE")
     */
    public function delete(Request $request, Hall $hall): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hall->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($hall);
            $em->flush();
        }

        return $this->redirectToRoute('hall_index');
    }
}
