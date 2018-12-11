<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/12/2018
 * Time: 02:11
 */

namespace App\Service\User;


use App\Entity\User;
use App\Service\LessonUser\ApplyToLessonException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;

class Finder
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
	 */
	private $userRepository;

	public function __construct(
		EntityManager $entityManager
	) {
		$this->entityManager = $entityManager;
		$this->userRepository = $entityManager->getRepository(User::class);
	}

	/**
	 * @param string $phone
	 *
	 * @return User
	 */
	public function findByPhone(string $phone): User
	{
		$formattedPhone = $this->formatPhone($phone);

		$users = $this->userRepository->matching(
			Criteria::create()->andWhere(
				Criteria::expr()->contains('phone', $formattedPhone)
			)
		);
		if (count($users) == 0) {
			throw new ApplyToLessonException("Такой пользователь не найден");
		}
		if (count($users) > 1) {
			throw new ApplyToLessonException("Найдено больше одного пользователей");
		}
		/** @var User $user */
		$user = $users->current();

		return $user;
	}

	/**
	 * @param string $phone
	 *
	 * @return string
	 */
	public function formatPhone(string $phone): string
	{
		if (!ctype_digit($phone)) {
			throw new ApplyToLessonException('Вводить можно только цифры');
		}

		if (mb_strlen($phone) > 11) {
			throw new ApplyToLessonException('Слишком много цифр');
		} elseif (mb_strlen($phone) >= 10) {
			$formattedPhone = '+7('.mb_substr($phone, -10, 3).')'.mb_substr($phone, -7);
		} elseif (mb_strlen($phone) > 7) {
			$formattedPhone = mb_substr($phone, (-1 * mb_strlen($phone)), mb_strlen($phone) - 7)
				.')'
				.mb_substr($phone, -7);
		} else {
			$formattedPhone = $phone;
		}

		return $formattedPhone;
	}

}