<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 29/07/2018
 * Time: 14:53
 */

namespace App\Service\Discounts;


use App\Entity\Discount;
use App\Entity\TicketPlan;
use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class Discounter
{
    /** @var \App\Repository\DiscountRepository|\Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository */
    private $discountRepository;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->discountRepository = $entityManager->getRepository(Discount::class);
    }

    /**
     * @param TicketPlan $ticketPlan
     * @param User       $user
     *
     * @return TicketPlan
     */
    public function makeDiscount(TicketPlan $ticketPlan, User $user): TicketPlan
    {
        $discounts = $this->discountRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->eq('ticketPlan', $ticketPlan))
                ->andWhere(Criteria::expr()->lte('activeFrom', new \DateTime()))
                ->andWhere(Criteria::expr()->gte('activeTo', new \DateTime()))
        );

        if ($discounts->count() > 0) {
            /** @var Discount $discount */
            $discount = $discounts->current();
            $ticketPlan->setOldPrice($ticketPlan->getPrice());
            $ticketPlan->setPrice($ticketPlan->getPrice() - $discount->getValue());
        }

        return $ticketPlan;
    }

    public function useBonus(TicketPlan $ticketPlan, User $user): int
    {
        $bonusBalance = $user->getBonusBalance();
        if ($bonusBalance > $ticketPlan->getPrice()) {
            $bonusBalance = $ticketPlan->getPrice();
        }

        $ticketPlan->setPrice($ticketPlan->getPrice() - $bonusBalance);

        return $bonusBalance;
    }
}