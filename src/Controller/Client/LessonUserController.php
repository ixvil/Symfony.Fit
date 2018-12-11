<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 08/05/2018
 * Time: 22:29
 */

namespace App\Controller\Client;


use App\Entity\Lesson;
use App\Entity\LessonUser;
use App\Entity\User;
use App\Entity\UserTicket;
use App\Entity\UserType;
use App\Service\Auth\Token\TokenGenerator;
use App\Service\LessonUser\ApplyToLessonException;
use App\Service\LessonUser\Checker;
use App\Service\LessonUser\LessonApplier;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lessonUser")
 */
class LessonUserController extends AbstractController
{

	/** @var \Doctrine\Common\Persistence\ObjectManager $entityManager */
	private $entityManager;

	/** @var EntityRepository $lessonRepository */
	private $lessonRepository;

	/**
	 * @var LessonApplier
	 */
	private $lessonApplier;
	/**
	 * @var Checker
	 */
	private $checker;
	/**
	 * @var \Doctrine\Common\Persistence\ObjectRepository|EntityRepository
	 */
	private $lessonUserRepository;

	public function __construct(
		TokenGenerator $tokenGenerator,
		EntityManager $entityManager,
		LessonApplier $lessonApplier,
		Checker $checker
	) {
		parent::__construct($tokenGenerator);
		$this->lessonApplier = $lessonApplier;
		$this->entityManager = $entityManager;
		$this->lessonRepository = $entityManager->getRepository(Lesson::class);
		$this->lessonUserRepository = $entityManager->getRepository(LessonUser::class);
		$this->checker = $checker;
	}

	/**
	 * @Route("/delete", name="lessonUser_delete", methods="POST")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ORMException
	 */
	public function delete(Request $request): Response
	{
		$this->auth($request);
		$user = $this->getCurrentUser();

		$content = json_decode($request->getContent());
		$lessonId = $content->lesson->id;

		/** @var Lesson $lesson */
		$lesson = $this->lessonRepository->find($lessonId);

		try {
			$this->lessonApplier->unApplyToLesson($lesson, $user);
		} catch (ApplyToLessonException $e) {
			return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
		}


		$lessons = $this->lessonRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->gte('startDateTime', new DateTime(date('Y-m-d'))))
				->orderBy(['startDateTime' => 'ASC'])
		);

		/** @var Lesson $lesson */
		foreach ($lessons as $lesson) {
			$lesson->clearCircularReferences();
		}

		return $this->json(['lessons' => $lessons, 'user' => $user->clearCircularReferences()], 200);
	}

	/**
	 * @Route("/managerDelete", name="lessonUser_managerDelete", methods="POST")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ORMException
	 */
	public function managerDelete(Request $request): Response
	{
		$this->auth($request);
		$user = $this->getCurrentUser();

		$content = json_decode($request->getContent());
		$lessonUserId = $content->lessonUserId;

		/** @var LessonUser $lessonUser */
		$lessonUser = $this->entityManager->find(LessonUser::class, $lessonUserId);
		$userCanManage = $this->checker->checkUserCanManage($user, $lessonUser->getLesson()->getId());

		if (!$userCanManage) {
			return $this->json(['error' => 'You can\'t manage this lesson']);
		}

		try {
			$this->lessonApplier->unApplyToLesson(
				$lessonUser->getLesson(),
				$lessonUser->getUser(),
				true
			);
		} catch (ApplyToLessonException $e) {
			return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
		}


		$lesson = $this->entityManager->find(Lesson::class, $lessonUser->getLesson()->getId());

		return $this->json([
			'lesson' => $lesson->clearCircularReferences(false),
			'user'   => $user->clearCircularReferences(),
		], 200);
	}

	/**
	 * @Route("/check", name="lessonUser_check", methods="POST")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function check(Request $request): Response
	{
		$this->auth($request);
		$user = $this->getCurrentUser();
		$content = json_decode($request->getContent());
		$lessonId = $content->lessonId;
		$clientId = $content->clientId;

		if (!is_numeric($clientId)) {
			return $this->json(['error' => 'Wrong client Id']);
		}
		$clientId = (int)$clientId;

		if (!$this->checker->checkUserCanManage($user, $lessonId)) {
			return $this->json(['error' => 'You can\'t manage this lesson']);
		}

		if (!$this->checker->markAsChecked($lessonId, (int)$clientId)) {
			return $this->json(['error' => 'There is no lessonUser']);
		}

		$lesson = $this->entityManager->find(Lesson::class, $lessonId);

		return $this->json([
			'lesson' => $lesson->clearCircularReferences(false),
			'user'   => $user->clearCircularReferences(),
		], 200);
	}

	/**
	 * @Route("/managerAdd", name="lessonUser_managerAdd", methods="POST")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws \Doctrine\ORM\TransactionRequiredException
	 */
	public function managerAdd(Request $request): Response
	{
		$this->auth($request);
		$user = $this->getCurrentUser();

		$content = json_decode($request->getContent());
		$lessonId = $content->lessonId;

		/** @var Lesson $lesson */
		$lesson = $this->entityManager->find(Lesson::class, $lessonId);
		$userCanManage = $this->checker->checkUserCanManage($user, $lesson->getId());

		if (!$userCanManage) {
			return $this->json(['error' => 'You can\'t manage this lesson']);
		}

		$phone = $content->phone;
		if (!$phone) {
			return $this->json(['error' => 'Введите номер для поиска']);
		}
		try {
			$this->lessonApplier->addToLesson(
				$lesson,
				$phone
			);
		} catch (ApplyToLessonException $e) {
			return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
		}

		return $this->json([
			'lesson' => $lesson->clearCircularReferences(false),
			'user'   => $user->clearCircularReferences(),
		], 200);
	}

	/**
	 * @Route("/", name="lessonUser_post", methods="POST")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function post(Request $request): Response
	{
		$this->auth($request);
		$user = $this->getCurrentUser();
		$content = json_decode($request->getContent());
		$lessonId = $content->state->dialog->id;

		/** @var Lesson $lesson */
		$lesson = $this->lessonRepository->find($lessonId);

		try {
			$this->lessonApplier->applyToLesson($lesson, $user);
		} catch (ApplyToLessonException $e) {
			return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
		}

		$lessons = $this->lessonRepository->matching(
			Criteria::create()
				->andWhere(Criteria::expr()->gte('startDateTime', new DateTime(date('Y-m-d'))))
				->orderBy(['startDateTime' => 'ASC'])
		);

		/** @var Lesson $lesson */
		foreach ($lessons as $lesson) {
			$lesson->clearCircularReferences();
		}

		return $this->json(['lessons' => $lessons, 'user' => $user], 200);
	}
}