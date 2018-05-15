<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 21.04.2018
 * Time: 3:18
 */

namespace App\Controller\Client;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthorizeController extends Controller
{
    /** @var EntityRepository $userRepo */
    private $userRepo;

    /** @var \Doctrine\Common\Persistence\ObjectManager $entityManager */
    private $entityManager;

    /**
     * @Route("/requestCode/", name="client_auth_post_request_code", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function postRequestCode(Request $request): Response
    {

        $content = json_decode($request->getContent());

        $phoneNumber = $content->phone;
        $newCode = '1111'; //TODO:: write to temp table

        return $this->json(['status' => 'ok', $phoneNumber, $newCode], 200, ['Access-Control-Allow-Origin' => "*"]);
    }

    /**
     * @Route("/login/", name="client_auth_post_login", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function postLogin(Request $request): Response
    {
        $content = json_decode($request->getContent());

        $phoneNumber = $content->phone;
        $code = $content->code; //TODO:: check from temp table

        if ($code != '1111') {
            return $this->json(['status' => false], 200, ['Access-Control-Allow-Origin' => "*"]);
        }

        $this->userRepo = $this->getDoctrine()->getRepository(User::class);
        $this->entityManager = $this->getDoctrine()->getManager();


        $userCollection = $this->userRepo->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('phone', $phoneNumber))
        );
        if ($userCollection->count() == 0) {
            $user = new User();
            $user
                ->setPhone($phoneNumber)
                ->setName('')
                ->setType($this->entityManager->find(UserType::class, 3));
            $this->entityManager->persist($user);
        } else {
            $user = $userCollection->get(0);
        }

        $this->entityManager->flush();

        return $this->json(['user' => $user, 'status' => true], 200, ['Access-Control-Allow-Origin' => "*"]);
    }

}
