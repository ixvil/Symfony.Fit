<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 12/07/2018
 * Time: 00:17
 */

namespace App\Controller\Client;

use App\Service\Auth\Token\TokenGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        TokenGenerator $tokenGenerator,
        EntityManager $entityManager)
    {
        parent::__construct($tokenGenerator);
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/welcome/", name="welcomeForm_post", methods="POST")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postWelcomeForm(Request $request): Response
    {
        $this->auth($request);

        $content = json_decode($request->getContent());

        $name = $content->name;

        $user = $this->getCurrentUser();
        $user->setName($name);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['user' => $user->clearCircularReferences()]);
    }
}