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
use App\Service\Discounts\Discounter;
use App\Service\Sberbank\Client;
use App\Service\Sberbank\Commands\Init;
use App\Service\Sberbank\Commands\RegisterCommand;
use App\Service\Sberbank\TinkoffClient;
use App\Service\Sms\Sender;
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
    private $bankClient;
    /**
     * @var PaymentOrderStatusRepository
     */
    private $paymentOrderStatusRepository;
    /**
     * @var Discounter
     */
    private $discounter;
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
        Client $bankClient,
        Discounter $discounter,
        Sender $sender,
		TinkoffClient $tinkoffClient
    ) {
        $this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
        $this->ticketPlanRepository = $entityManager->getRepository(TicketPlan::class);
        $this->paymentOrderStatusRepository = $entityManager->getRepository(PaymentOrderStatus::class);
        $this->entityManager = $entityManager;
        $this->bankClient = $bankClient;
        $this->discounter = $discounter;
        $this->sender = $sender;
		$this->tinkoffClient = $tinkoffClient;
	}

    /**
     * @param int  $ticketPlanId
     * @param User $user
     * @param bool $useBonus
     *
     * @return array
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registerOrder(int $ticketPlanId, User $user, bool $useBonus): array
    {
        /** @var TicketPlan $ticketPlan */
        $ticketPlan = $this->ticketPlanRepository->find($ticketPlanId);
        $this->discounter->makeDiscount($ticketPlan, $user);
        if ($useBonus) {
            $bonusAmount = $this->discounter->useBonus($ticketPlan, $user);
        } else {
            $bonusAmount = 0;
        }

        $paymentOrder = new PaymentOrder();
        $paymentOrder
            ->setAmount($ticketPlan->getPrice())
            ->setBonusAmount($bonusAmount)
            ->setUser($user)
            ->setTicketPlan($ticketPlan)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $status = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_NEW);
        $paymentOrder->setStatus($status);

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush($paymentOrder);

        if ($paymentOrder->getAmount() > 0) {
            $answer = $this->registerCommand($paymentOrder);
        } else {
            $this->confirmOrder($paymentOrder);
            $answer = [
                'status'  => 'ok',
                'formUrl' => '/',
            ];
        }

        return $answer;
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function confirmOrder(PaymentOrder $paymentOrder)
    {
        $this->logger->info('will confirm PaymentOrder #'.$paymentOrder->getId());
        $ticketPlan = $paymentOrder->getTicketPlan();

        $userTicket = new UserTicket();
        $userTicket->setDateCreatedAt(new \DateTime())
            ->setTicketPlan($ticketPlan)
            ->setLessonsExpires($ticketPlan->getLessonsCount())
            ->setIsActive(true)
            ->setUser($paymentOrder->getUser());

        $newStatus = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_PAID);

        $paymentOrder->setTicketPlan($ticketPlan)
            ->setStatus($newStatus)
            ->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($userTicket);
        $this->entityManager->flush($userTicket);

        $paymentOrder->setUserTicket($userTicket);

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush($paymentOrder);

        $this->updateBonusBalance($paymentOrder);
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function cancelOrder(PaymentOrder $paymentOrder)
    {
        $this->logger->info('will cancel PaymentOrder #'.$paymentOrder->getId());
        $newStatus = $this->paymentOrderStatusRepository->find(PaymentOrderStatusRepository::STATUS_CANCELED);

        $paymentOrder
            ->setUpdatedAt(new \DateTime())
            ->setStatus($newStatus);

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush();
    }

    public function registerCommand(PaymentOrder $paymentOrder): array
    {
		if($paymentOrder->getUser()->getId() == 30){
			$command = new Init();
			$command->setAmount($paymentOrder->getAmount());
			$command->setOrderId($paymentOrder->getId());

			$answer = $this->tinkoffClient->execute($command);

			if(isset($answer['bank_payment_id'])){
				$paymentOrder->setBankPaymentId($answer['bank_payment_id']);
				$this->entityManager->persist($paymentOrder);
				$this->entityManager->flush();
			}
			return $answer;
		}

		$command = new RegisterCommand();
        $command->setAmount($paymentOrder->getAmount());
        $command->setOrderNumber($paymentOrder->getId());

        $answer = $this->bankClient->execute($command);

        return $answer;
    }

    /**
     * @param PaymentOrder $paymentOrder
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateBonusBalance(PaymentOrder $paymentOrder): void
    {
        if ($paymentOrder->getBonusAmount() > 0) {
            $user = $paymentOrder->getUser();
            $bonusBalance = $user->getBonusBalance();
            $newBonusBalance = $bonusBalance - $paymentOrder->getBonusAmount();
            $user->setBonusBalance($newBonusBalance);
            $this->entityManager->persist($user);
            $this->entityManager->flush($user);

            if ($newBonusBalance < 0) {
                $this->sender->sendToAdmin('Alert! negative balance user '.$user->getPhone());
                $this->logger->alert(
                    "User bonus balance is negative: ",
                    [
                        'phone'                     => $user->getPhone(),
                        'bonusBalance'              => $bonusBalance,
                        'newBonusBalance'           => $newBonusBalance,
                        'paymentOrder->id'          => $paymentOrder->getId(),
                        'paymentOrder->bonusAmount' => $paymentOrder->getBonusAmount(),
                        'paymentOrder->amount'      => $paymentOrder->getAmount(),
                    ]
                );
            }
        }
    }

}