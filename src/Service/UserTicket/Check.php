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
use App\Service\Sberbank\Commands\GetState;
use App\Service\Sberbank\TinkoffClient;
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
    private $tinkoffStatuses = [
    	'CANCELED' => PaymentOrderStatusRepository::STATUS_CANCELED,
		'DEADLINE_EXPIRED' => PaymentOrderStatusRepository::STATUS_CANCELED,
		'CONFIRMED' => PaymentOrderStatusRepository::STATUS_PAID,
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
	/**
	 * @var TinkoffClient
	 */
	private $tinkoffClient;

	public function __construct(
        EntityManager $entityManager,
        Client $sberbankClient,
        Buy $buy,
        Sender $sender,
		TinkoffClient $tinkoffClient
    ) {
        $this->paymentOrderStatusRepository = $entityManager->getRepository(PaymentOrderStatus::class);
        $this->paymentOrderRepository = $entityManager->getRepository(PaymentOrder::class);
        $this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
        $this->entityManager = $entityManager;
        $this->sberbankClient = $sberbankClient;
        $this->buy = $buy;
        $this->sender = $sender;
		$this->tinkoffClient = $tinkoffClient;
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
        	if($paymentOrder->getBankPaymentId() !== null){
        		$command = new GetState();
        		$command->setPaymentId($paymentOrder->getBankPaymentId());
        		$answer = $this->tinkoffClient->execute($command);
				$this->logger->info($paymentOrder->getId().':: '.print_r($answer));
				if (isset($answer['orderStatus'])) {
					$this->logger->info('Order status id '.$answer['orderStatus']);
					if(!isset($this->tinkoffStatuses[$answer['orderStatus']])){
						continue;
					}
					if ($this->tinkoffStatuses[$answer['orderStatus']] == PaymentOrderStatusRepository::STATUS_PAID) {
						$this->buy->confirmOrder($paymentOrder);
						$this->sender->sendToOwner('Hooray! New payment =) '.$paymentOrder->getAmount());
					} else {
						if ($this->tinkoffStatuses[$answer['orderStatus']] == PaymentOrderStatusRepository::STATUS_CANCELED) {
							$this->buy->cancelOrder($paymentOrder);
						}
					}
				}
			} else {
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
                $this->entityManager->persist($userTicket);
                $this->entityManager->flush($userTicket);

                if ($userTicket->getTicketPlan()->getType()->getId() === 1) {
                    $bonusesAmount = $this->chargeBack($userTicket);

                    $this->logger->info(
                        'User ticket #'.$userTicket->getId().' expired. '.$bonusesAmount.' bonuses added'
                    );
                } else {
                    $this->logger->info('User ticket #'.$userTicket->getId().' expired. no bonuses added');
                }
            }
        }
    }

    public function calculateBonusesAmount(int $price, int $lessonsExpires, int $lessonsCount): int
    {
        return ($price * $lessonsExpires / $lessonsCount) / 2;
    }

    /**
     * @param $userTicket
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function chargeBack(UserTicket $userTicket): int
    {

        $ticketPlan = $userTicket->getTicketPlan();

        $bonusesAmount = $this->calculateBonusesAmount(
            $ticketPlan->getPrice(),
            $userTicket->getLessonsExpires(),
            $ticketPlan->getLessonsCount()
        );

        $user = $userTicket->getUser();
        $user->setBonusBalance($user->getBonusBalance() + $bonusesAmount);

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $bonusesAmount;
    }
}