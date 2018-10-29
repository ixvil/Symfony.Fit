<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 03/10/2018
 * Time: 10:37
 */

namespace App\Service\UserTicket;


use Doctrine\Common\Collections\Collection;

class UserTicketsProcessor
{
    /**
     * @param Collection $userTickets
     * @param int[]      $types
     *
     * @return int
     */
    public function userTicketSum(Collection $userTickets, array $types = [1, 2, 4]): int
    {
        $sum = 0;
        foreach ($userTickets as $userTicket) {
            if (!in_array($userTicket->getTicketPlan()->getType()->getId(), $types)) {
                continue;
            }
            if (!$userTicket->getIsActive()) {
                continue;
            }
            $sum += $userTicket->getLessonsExpires();
        }

        return $sum;

    }
}