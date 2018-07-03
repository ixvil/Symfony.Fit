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
use App\Repository\PaymentOrderRepository;
use App\Repository\PaymentOrderStatusRepository;
use App\Service\Sberbank\Client;
use App\Service\Sberbank\Commands\GetOrderStatus;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerAwareTrait;

class Check
{
    use LoggerAwareTrait;
    private $statuses = [
        0 => PaymentOrderStatusRepository::STATUS_NEW,
        1 => PaymentOrderStatusRepository::STATUS_PAID,
        2 => PaymentOrderStatusRepository::STATUS_PAID,
        3 => PaymentOrderStatusRepository::STATUS_CANCELED,
        4 => PaymentOrderStatusRepository::STATUS_CANCELED,
        5 => PaymentOrderStatusRepository::STATUS_NEW,
        6 => PaymentOrderStatusRepository::STATUS_CANCELED
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

    public function __construct(
        EntityManager $entityManager,
        Client $sberbankClient,
        Buy $buy
    )
    {
        $this->paymentOrderStatusRepository = $entityManager->getRepository(PaymentOrderStatus::class);
        $this->paymentOrderRepository = $entityManager->getRepository(PaymentOrder::class);
        $this->sberbankClient = $sberbankClient;
        $this->buy = $buy;
    }

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
            $this->logger->info(print_r($answer));

            if (isset($answer['orderStatus'])) {
                $this->logger->info('Order status id ' . $answer['orderStatus']);
                if (true||$this->statuses[$answer['orderStatus']] == PaymentOrderStatusRepository::STATUS_PAID) {
                    $this->buy->confirmOrder($paymentOrder);
                } else if ($this->statuses[$answer['orderStatus']] == PaymentOrderStatusRepository::STATUS_CANCELED) {
                    $this->buy->cancelOrder($paymentOrder);
                }
            }
        }
    }
}