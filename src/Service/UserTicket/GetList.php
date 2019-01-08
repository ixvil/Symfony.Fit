<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/09/2018
 * Time: 09:35
 */

namespace App\Service\UserTicket;


use App\Entity\UserTicket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class GetList
{
	const INTERVAL = "P7D";

	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
	 */
	private $userTicketRepository;
	/**
	 * @var UserTicketsProcessor
	 */
	private $userTicketsProcessor;

	public function __construct(
		EntityManager $entityManager,
		UserTicketsProcessor $userTicketsProcessor
	) {

		$this->entityManager = $entityManager;
		$this->userTicketRepository = $entityManager->getRepository(UserTicket::class);
		$this->userTicketsProcessor = $userTicketsProcessor;
	}

	/**
	 * @param int|null $limit
	 *
	 * @return Collection
	 */
	private function get(?int $limit = null): Collection
	{
		$userTickets = $this->userTicketRepository->matching(
			Criteria::create()->orderBy(['id' => 'DESC'])->setMaxResults($limit)
		);

		return $userTickets;
	}

	/**
	 * @return Collection
	 */
	public function getNewTickets(): Collection
	{
		$userTickets = $this->get(30);

		/** @var UserTicket[] $userTicketsArray */
		$userTicketsArray = $userTickets->toArray();

		return new ArrayCollection($userTicketsArray);
	}

	/**
	 * @return Collection
	 * @throws \Exception
	 */
	public function getExpirationUserTickets(): Collection
	{
		$userTickets = $this->get();

		/** @var UserTicket[] $userTicketsArray */
		$userTicketsArray = $userTickets->toArray();

		usort(
			$userTicketsArray, function (UserTicket $a, UserTicket $b) {
			return ($a->getExpirationDate() < $b->getExpirationDate()) ? -1 : 1;
		}
		);

		foreach ($userTicketsArray as $key => $userTicket) {
			if ($userTicket->getTicketPlan()->getType()->getId() == 3) {
				unset($userTicketsArray[$key]);
			}
			if ($userTicket->getExpirationDate() < (new \DateTime())->sub(new \DateInterval(self::INTERVAL))) {
				unset($userTicketsArray[$key]);
			}
			if ($userTicket->getExpirationDate() > (new \DateTime())->add(new \DateInterval(self::INTERVAL))) {
				unset($userTicketsArray[$key]);
			}
			if ($this->userTicketsProcessor->userTicketSum($userTicket->getUser()->getUserTickets())
				> $userTicket->getLessonsExpires()
			) {
				unset($userTicketsArray[$key]);
			}
		}

		return new ArrayCollection($userTicketsArray);
	}
}