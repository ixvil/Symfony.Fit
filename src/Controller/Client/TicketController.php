<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 22:15
 */

namespace App\Controller\Client;

use App\Service\Auth\Token\TokenGenerator;
use App\Service\UserTicket\Buy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/userTicket")
 */
class TicketController extends AbstractController
{
    /**
     * @var Buy
     */
    private $buy;

    public function __construct(
        TokenGenerator $tokenGenerator,
        Buy $buy
    )
    {
        parent::__construct($tokenGenerator);
        $this->buy = $buy;
    }

    /**
     * @Route("/buy/", name="ticketBuy_post", methods="POST")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postBuyTicket(Request $request): Response
    {
        $this->auth($request);
        $content = json_decode($request->getContent());

        $ticketPlanId = $content->ticket_plan_id;
        $useBonus = $content->useBonus;

        if ($ticketPlanId) {
            $data = $this->buy->registerOrder($ticketPlanId, $this->getCurrentUser(), $useBonus);
        } else {
            return $this->json(['error' => 'No such ticketPlan']);
        }

        return $this->json($data);
    }
}