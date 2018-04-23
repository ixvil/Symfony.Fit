<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 21.04.2018
 * Time: 3:18
 */

namespace App\Controller\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthorizeController extends Controller
{

    /**
     * @Route("/login", name="client_auth_get_login", methods="GET")
     */
    public function getLogin(): Response
    {
        return $this->render('client/auth/login.html.twig', []);
    }

}