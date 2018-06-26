<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 21.04.2018
 * Time: 3:18
 */

namespace App\Controller\Client;

use App\Entity\User;
use App\Entity\UserTicket;
use App\Entity\UserToken;
use App\Entity\UserType;
use App\Service\Auth\CodeChecker;
use App\Service\Auth\CodeProcessor;
use App\Service\Auth\Token\Creator;
use App\Service\Auth\Token\TokenGenerator;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class AuthorizeController extends AbstractController
{
    /**
     * @var CodeProcessor
     */
    private $codeProcessor;
    /**
     * @var CodeChecker
     */
    private $codeChecker;
    /**
     * @var Creator
     */
    private $tokenCreator;


    public function __construct(
        CodeProcessor $codeProcessor,
        CodeChecker $codeChecker,
        Creator $tokenCreator,
        TokenGenerator $tokenGenerator
    )
    {
        $this->codeProcessor = $codeProcessor;
        $this->codeChecker = $codeChecker;
        $this->tokenCreator = $tokenCreator;
        parent::__construct($tokenGenerator);
    }

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

        $this->userRepo = $this->getDoctrine()->getRepository(User::class);
        $this->entityManager = $this->getDoctrine()->getManager();

        $userCollection = $this->userRepo->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('phone', $phoneNumber))
        );
        if ($userCollection->count() !== 0) {
            $user = $userCollection->get(0);
        } else {
            throw new UnprocessableEntityHttpException();
        }

        $this->codeProcessor->process($user);

        return $this->json(['status' => 'ok'], 200, ['Access-Control-Allow-Origin' => "*"]);
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
        $code = $content->code;

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

        if (!$this->codeChecker->check($code, $user)) {
            return $this->json(['status' => false], 200, ['Access-Control-Allow-Origin' => "*"]);
        }

        $frontToken = $this->tokenCreator->create($user);

        $this->entityManager->flush();
        $user->clearCircularReferences();
        return $this->json(['user' => $user, 'status' => true, 'token' => $frontToken], 200, ['Access-Control-Allow-Origin' => "*"]);
    }

    /**
     * @Route("/tokenLogin/", name="client_auth_post_token_login", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function postTokenLogin(Request $request): Response
    {
        $this->auth($request);

        $user = $this->getCurrentUser();

        //Fucking hack to avoid circular exception
        /** @var UserTicket $userTicket */
        foreach ($user->getUserTickets() as $userTicket) {
            $userTicket->setUser(null);
        }

        return $this->json(['user' => $this->getCurrentUser(), 'status' => true], 200);

    }

}
