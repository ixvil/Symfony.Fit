<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 23:57
 */

namespace App\Service\UserTicket;

use App\Entity\Discount;
use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderStatus;
use App\Entity\TicketPlan;
use App\Entity\User;
use App\Entity\UserTicket;
use App\Repository\PaymentOrderStatusRepository;
use App\Service\Discounts\Discounter;
use App\Service\Sberbank\Client;
use App\Service\Sberbank\Commands\RegisterCommand;
use App\Service\Sms\Sender;
use Doctrine\Common\Collections\Criteria;
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
	/**
	 * @var Discounter
	 */
	private $discounter;
	/**
	 * @var Sender
	 */
	private $sender;
	/**
	 * @var \App\Repository\DiscountRepository|\Doctrine\Common\Persistence\ObjectRepository|EntityRepository
	 */
	private $discountRepository;

	public function __construct(
		EntityManager $entityManager,
		Client $sberbankClient,
		Discounter $discounter,
		Sender $sender
	) {
		$this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
		$this->ticketPlanRepository = $entityManager->getRepository(TicketPlan::class);
		$this->paymentOrderStatusRepository = $entityManager->getRepository(PaymentOrderStatus::class);
		$this->entityManager = $entityManager;
		$this->sberbankClient = $sberbankClient;
		$this->discounter = $discounter;
		$this->sender = $sender;
		$this->discountRepository = $entityManager->getRepository(Discount::class);
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

		$this->swapBlackFriday($paymentOrder->getUser(), $ticketPlan);
	}

	public function swapBlackFriday(User $user, TicketPlan $ticketPlan)
	{
		if (!in_array($ticketPlan->getId(), [1, 6, 12])) {

			return;
		}

		$discounts = $this->discountRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->eq('user', $user))
				->andWhere(Criteria::expr()->eq('ticketPlan', $ticketPlan))
				->andWhere(Criteria::expr()->lte('activeFrom', new \DateTime()))
				->andWhere(Criteria::expr()->gte('activeTo', new \DateTime()))
		);

		if ($discounts->count() > 0) {
			foreach ($discounts as $discount) {
				$this->entityManager->remove($discount);
				$this->entityManager->flush($discount);
				// $this->entityManager->persist($discount);
			}
		} else {
			$discount = new Discount();
			$discount->setActiveFrom(new \DateTime('2018-11-22 21:00:00'));
			$discount->setActiveTo(new \DateTime('2018-11-25 23:59:00'));
			$discount->setTicketPlan($ticketPlan);
			$discount->setUser($user);
			$discount->setValue($ticketPlan->getPrice() * 0.2);

			$this->entityManager->persist($discount);
			$this->entityManager->flush($discount);
		}

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
		$command = new RegisterCommand();
		$command->setAmount($paymentOrder->getAmount());
		$command->setOrderNumber($paymentOrder->getId());

		$answer = $this->sberbankClient->execute($command);

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