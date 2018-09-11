<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/09/2018
 * Time: 09:35
 */

namespace App\Service\UserTicket;


use App\Entity\UserTicket;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class GetList
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    private $userTicketRepository;

    public function __construct(
        EntityManager $entityManager
    ) {

        $this->entityManager = $entityManager;
        $this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
    }

    public function get(): Collection
    {
        $userTickets = $this->userTicketRepository->matching(
            Criteria::create()
        );

        return $userTickets;
    }
}