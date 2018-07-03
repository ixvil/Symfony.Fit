<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 23:57
 */

namespace App\Service\UserTicket;


use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderStatus;
use App\Entity\TicketPlan;
use App\Entity\User;
use App\Entity\UserTicket;
use App\Repository\PaymentOrderStatusRepository;
use App\Service\Sberbank\Client;
use App\Service\Sberbank\Commands\RegisterCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerAwareTrait;

class Buy
{
    use LoggerAwareTrait;
    /**
     * @var EntityRepository
     */
    private $userTicketRepository;
    /**
     * @var EntityRepository
     */
    private $ticketPlanRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var Client
     */
    private $sberbankClient;
    /**
     * @var PaymentOrderStatusRepository
     */
    private $paymentOrderStatusRepository;

    public function __construct(
        EntityManager $entityManager,
        Client $sberbankClient
    )
    {
        $this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
        $this->ticketPlanRepository = $entityManager->getRepository(TicketPlan::class);
        $this->paymentOrderStatusRepository = $entityManager->getRepository(PaymentOrderStatus::class);
        $this->entityManager = $entityManager;
        $this->sberbankClient = $sberbankClient;
    }

    /**
     * @param int $ticketPlanId
     * @param User $user
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registerOrder(int $ticketPlanId, User $user): array
    {
        /** @var TicketPlan $ticketPlan */
        $ticketPlan = $this->ticketPlanRepository->find($ticketPlanId);

        $paymentOrder = new PaymentOrder();
        $paymentOrder
            ->setAmount($ticketPlan->getPrice())
            ->setUser($user)
            ->setTicketPlan($ticketPlan)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $status = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_NEW);
        $paymentOrder->setStatus($status);

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush();

        $command = new RegisterCommand();
        $command->setAmount($paymentOrder->getAmount());
        $command->setOrderNumber($paymentOrder->getId());

        $answer = $this->sberbankClient->execute($command);

        return $answer;
    }

    /**
     * @param PaymentOrder $paymentOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function confirmOrder(PaymentOrder $paymentOrder)
    {
        $this->logger->info('will confirm PaymentOrder #' . $paymentOrder->getId());
        $ticketPlan = $paymentOrder->getTicketPlan();

        $userTicket = new UserTicket();
        $userTicket->setDateCreatedAt(new \DateTime())
            ->setTicketPlan($ticketPlan)
            ->setLessonsExpires($ticketPlan->getLessonsCount())
            ->setUser($paymentOrder->getUser());

        $newStatus = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_PAID);

        $paymentOrder->setTicketPlan($ticketPlan)
            ->setStatus($newStatus)
            ->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->persist($userTicket);
        $this->entityManager->flush();

        $paymentOrder->setUserTicket($userTicket);
        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush();


    }

    /**
     * @param PaymentOrder $paymentOrder
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function cancelOrder(PaymentOrder $paymentOrder)
    {
        $this->logger->info('will cancel PaymentOrder #' . $paymentOrder->getId());
        $newStatus = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_CANCELED);

        $paymentOrder
            ->setUpdatedAt(new \DateTime())
            ->setStatus($newStatus);

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush();
    }

}