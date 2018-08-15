<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 02/07/2018
 * Time: 01:05
 */

namespace App\Service\UserTicket;


use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderStatus;
use App\Entity\UserTicket;
use App\Repository\PaymentOrderRepository;
use App\Repository\PaymentOrderStatusRepository;
use App\Service\Sberbank\Client;
use App\Service\Sberbank\Commands\GetOrderStatus;
use App\Service\Sms\Sender;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerAwareTrait;

class Check
{
    use LoggerAwareTrait;
    private $statuses
        = [
            0 => PaymentOrderStatusRepository::STATUS_NEW,
            1 => PaymentOrderStatusRepository::STATUS_PAID,
            2 => PaymentOrderStatusRepository::STATUS_PAID,
            3 => PaymentOrderStatusRepository::STATUS_CANCELED,
            4 => PaymentOrderStatusRepository::STATUS_CANCELED,
            5 => PaymentOrderStatusRepository::STATUS_NEW,
            6 => PaymentOrderStatusRepository::STATUS_CANCELED,
        ];
    /**
     * @var PaymentOrderStatusRepository
     */
    private $paymentOrderStatusRepository;
    /**
     * @var PaymentOrderRepository
     */
    private $paymentOrderRepository;
    /**
     * @var Client
     */
    private $sberbankClient;
    /**
     * @var Buy
     */
    private $buy;

    private $userTicketRepository;
    private $entityManager;
    /**
     * @var Sender
     */
    private $sender;

    public function __construct(
        EntityManager $entityManager,
        Client $sberbankClient,
        Buy $buy,
        Sender $sender
    ) {
        $this->paymentOrderStatusRepository = $entityManager->getRepository(PaymentOrderStatus::class);
        $this->paymentOrderRepository = $entityManager->getRepository(PaymentOrder::class);
        $this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
        $this->entityManager = $entityManager;
        $this->sberbankClient = $sberbankClient;
        $this->buy = $buy;
        $this->sender = $sender;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function check()
    {
        $newStatus = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_NEW);

        $paymentOrders = $this->paymentOrderRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('status', $newStatus))
        );

        /** @var PaymentOrder $paymentOrder */
        foreach ($paymentOrders as $paymentOrder) {
            $command = new GetOrderStatus();
            $command->setOrderNumber($paymentOrder->getId());
            $answer = $this->sberbankClient->execute($command);
            $this->logger->info($paymentOrder->getId().':: '.print_r($answer));

            if (isset($answer['orderStatus'])) {
                $this->logger->info('Order status id '.$answer['orderStatus']);
                if ($this->statuses[$answer['orderStatus']] == PaymentOrderStatusRepository::STATUS_PAID) {
                    $this->buy->confirmOrder($paymentOrder);
                    $this->sender->sendToOwner('Hooray! New payment =) '.$paymentOrder->getAmount());
                } else {
                    if ($this->statuses[$answer['orderStatus']] == PaymentOrderStatusRepository::STATUS_CANCELED) {
                        $this->buy->cancelOrder($paymentOrder);
                    }
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function checkExpiration()
    {
        $userTickets = $this->userTicketRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('isActive', true))
        );

        /** @var UserTicket $userTicket */
        foreach ($userTickets as $userTicket) {
            if ($userTicket->getExpirationDate() < new \DateTime()) {
                $userTicket->setIsActive(false);
                $ticketPlan = $userTicket->getTicketPlan();
                $bonusesAmount = $this->calculateBonusesAmount(
                    $ticketPlan->getPrice(),
                    $userTicket->getLessonsExpires(),
                    $ticketPlan->getLessonsCount()
                );

                $user = $userTicket->getUser();
                $user->setBonusBalance($user->getBonusBalance() + $bonusesAmount);

                $this->entityManager->persist($userTicket);
                $this->entityManager->flush($userTicket);

                $this->entityManager->persist($user);
                $this->entityManager->flush($user);

                $this->logger->info('User ticket #'.$userTicket->getId().' expired. '.$bonusesAmount.' bonuses added');
            }
        }
    }

    public function calculateBonusesAmount(int $price, int $lessonsExpires, int $lessonsCount): int
    {
        return ($price * $lessonsExpires / $lessonsCount) / 2;
    }

    public function getOutdating()
    {
        $sql
            = "
            select tic.*, ut2.date_created_at, ut2.lessons_expires, tp.lessons_count, u2.phone, u2.name, u2.id from (
            select ut.id, min(l.start_date_time) first_lesson from user_ticket ut
              left join lesson_user u on ut.id = u.user_ticket_id
              left join lesson l on u.lesson_id = l.id
            group by ut.id ) tic
              left join user_ticket ut2 on ut2.id = tic.id
              left join ticket_plan tp on tp.id = ut2.ticket_plan_id
              left join user u2 on ut2.user_id = u2.id
            where u2.id not in (30,31)
            order by u2.id asc, date_created_at asc
        ";

    }
}