<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 29/07/2018
 * Time: 13:06
 */

namespace App\Controller\Client;

use App\Entity\Discount;
use App\Entity\TicketPlan;
use App\Entity\TicketPlanType;
use App\Service\Auth\Token\TokenGenerator;
use App\Service\Discounts\Discounter;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticketPlan")
 */
class TicketPlanController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /** @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository $ticketPlanRepository */
    private $ticketPlanRepository;
    /** @var \App\Repository\DiscountRepository|\Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository */
    private $discountRepository;
    /**
     * @var Discounter
     */
    private $discounter;

    public function __construct(
        TokenGenerator $tokenGenerator,
        EntityManager $entityManager,
        Discounter $discounter
    ) {
        parent::__construct($tokenGenerator);
        $this->entityManager = $entityManager;
        $this->ticketPlanRepository = $this->entityManager->getRepository(TicketPlan::class);
        $this->discountRepository = $this->entityManager->getRepository(Discount::class);
        $this->discounter = $discounter;
    }

    /**
     * @Route("/", name="ticketPlan_index", methods={"POST"})
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function index(Request $request): Response
    {
        $this->auth($request);
        $user = $this->getCurrentUser();
        $content = json_decode($request->getContent());

        $useBonus = $content->useBonus;

        /** @var TicketPlan[] $ticketPlans */
        $ticketPlans = $this->ticketPlanRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('type', $this->entityManager->find(TicketPlanType::class, 1)))
                ->orWhere(Criteria::expr()->eq('type', $this->entityManager->find(TicketPlanType::class, 3)))
                ->orWhere(Criteria::expr()->eq('type', $this->entityManager->find(TicketPlanType::class, 4)))
        );

        foreach ($ticketPlans as &$ticketPlan) {
            $this->discounter->makeDiscount($ticketPlan, $user);
            if ($useBonus == true) {
                $this->discounter->useBonus($ticketPlan, $user);
            }
        }

        return $this->json(['ticketPlans' => $ticketPlans]);
    }
}