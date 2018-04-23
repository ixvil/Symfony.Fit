<?php

namespace App\Controller\Admin;

use App\Entity\UserType;
use App\Form\UserType1Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user_type")
 */
class UserTypeController extends Controller
{
    /**
     * @Route("/", name="user_type_index", methods="GET")
     */
    public function index(): Response
    {
        $userTypes = $this->getDoctrine()
            ->getRepository(UserType::class)
            ->findAll();

        return $this->render('user_type/index.html.twig', ['user_types' => $userTypes]);
    }

    /**
     * @Route("/new", name="user_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $userType = new UserType();
        $form = $this->createForm(UserType1Type::class, $userType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userType);
            $em->flush();

            return $this->redirectToRoute('user_type_index');
        }

        return $this->render('user_type/new.html.twig', [
            'user_type' => $userType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_type_show", methods="GET")
     */
    public function show(UserType $userType): Response
    {
        return $this->render('user_type/show.html.twig', ['user_type' => $userType]);
    }

    /**
     * @Route("/{id}/edit", name="user_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, UserType $userType): Response
    {
        $form = $this->createForm(UserType1Type::class, $userType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_type_edit', ['id' => $userType->getId()]);
        }

        return $this->render('user_type/edit.html.twig', [
            'user_type' => $userType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_type_delete", methods="DELETE")
     */
    public function delete(Request $request, UserType $userType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userType);
            $em->flush();
        }

        return $this->redirectToRoute('user_type_index');
    }
}
