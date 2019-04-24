<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 23/06/2018
 * Time: 23:34
 */

namespace App\Service\PromoCode;


use App\Entity\PromoCode;
use App\Entity\User;
use App\Entity\UserTicket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerAwareTrait;

class Activator
{
	use LoggerAwareTrait;
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/** @var EntityRepository $promoCodeRepository */
	private $promoCodeRepository;

	public function __construct(
		EntityManager $entityManager
	) {
		$this->entityManager = $entityManager;
		$this->promoCodeRepository = $entityManager->getRepository(PromoCode::class);
	}

	/**
	 * @param string $promoCodeString
	 * @param User   $user
	 *
	 * @return bool
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function activate(string $promoCodeString, User $user): bool
	{
		$promoCodeCollection = $this->promoCodeRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->eq('code', $promoCodeString))
				->andWhere(Criteria::expr()->eq('isActivated', false))
		);

		$this->logger->info('count: '.$promoCodeCollection->count());
		if ($promoCodeCollection->count() < 1) {
			return false;
		}

		/** @var PromoCode $promoCode */
		$promoCode = $promoCodeCollection->current();
		if ($promoCode->getTicketPlan() !== null) {
			$this->addPromoCodeTicket($user, $promoCode);
		}
		if ($promoCode->getBonusAmount() > 0) {
			$this->addPromoCodeBonus($user, $promoCode);
		}

		return true;
	}

	/**
	 * @param User      $user
	 * @param PromoCode $promoCode
	 *
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function addPromoCodeTicket(User $user, PromoCode $promoCode): void
	{
		$userTicket = new UserTicket();
		$userTicket
			->setUser($user)
			->setTicketPlan($promoCode->getTicketPlan())
			->setLessonsExpires($promoCode->getTicketPlan()->getLessonsCount())
			->setIsActive(true)
			->setLessonUsers(new ArrayCollection())
			->setDateCreatedAt(new \DateTime());

		$promoCode->setIsActivated(true);
		$promoCode->setActivatedBy($user);


		$this->entityManager->persist($userTicket);
		$this->entityManager->persist($promoCode);

		$this->entityManager->flush();
	}

	/**
	 * @param User      $user
	 * @param PromoCode $promoCode
	 *
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	private function addPromoCodeBonus(User $user, PromoCode $promoCode)
	{
		$user->setBonusBalance($user->getBonusBalance() + $promoCode->getBonusAmount());

		$promoCode->setIsActivated(true);
		$promoCode->setActivatedBy($user);

		$this->entityManager->persist($user);
		$this->entityManager->persist($promoCode);

		$this->entityManager->flush();
	}


}