<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 23/06/2018
 * Time: 23:26
 */

namespace App\Controller\Client;


use App\Service\Auth\Token\TokenGenerator;
use App\Service\PromoCode\Activator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/promoCode")
 */
class PromoCodeController extends AbstractController
{
    /**
     * @var Activator
     */
    private $activator;

    public function __construct(
        TokenGenerator $tokenGenerator,
        Activator $activator
    )
    {
        parent::__construct($tokenGenerator);
        $this->activator = $activator;
    }

    /**
     * @Route("/", name="promoCode_post", methods="POST")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function postPromoCode(Request $request): Response
    {
        $this->auth($request);
        $user = $this->getCurrentUser();
        $user->clearCircularReferences();

        $content = json_decode($request->getContent());
        $promoCodeString = $content->promoCode;

        $success = $this->activator->activate($promoCodeString, $user);

        if (!$success) {
            return $this->json(['error' => 'Введен неверный промокод'], 422);
        }

        return $this->json($user);
    }

}