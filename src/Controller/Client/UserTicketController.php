<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 03/10/2018
 * Time: 11:02
 */

namespace App\Controller\Client;


use App\Entity\UserTicket;
use App\Service\Auth\Token\TokenGenerator;
use App\Service\UserTicket\GetList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @Route("/userTicket")
 */
class UserTicketController extends AbstractController
{
	/**
	 * @var GetList $getList
	 */
	private $getList;

	public function __construct(
		TokenGenerator $tokenGenerator,
		GetList $getList
	) {
		parent::__construct($tokenGenerator);
		$this->getList = $getList;
	}

	/**
	 * @Route("/expiration/", name="ut_expiration_get", methods="GET")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function getExpiration(Request $request): Response
	{
		$this->auth($request);

		$user = $this->getCurrentUser();
		if ($user->getType()->getId() !== 1) {
			throw new AccessDeniedHttpException();
		}

		$userTickets = $this->getList->getExpirationUserTickets();
		$array = [];
		/** @var UserTicket $userTicket */
		foreach ($userTickets as $userTicket) {
			$user = $userTicket->getUser();

			$dUserTickets = $user->getUserTickets();
			$new = [];
			foreach ($dUserTickets as $dUserTicket){
				$clone = clone $dUserTicket;
				$clone->setUser(null);
				$clone->setLessonUsers(new ArrayCollection());
				$new[] = $clone;
			}
			$user->setUserTickets(new ArrayCollection($new));

			$lessonUsers = $userTicket->getLessonUsers();
			foreach ($lessonUsers as $lessonUser){
				$lesson = $lessonUser->getLesson();
				$lesson->setLessonUsers(new ArrayCollection());
				$lesson->setLessonSet(null);
				$lessonUser->setUser(null);
				$lessonUser->setUserTicket(null);
			}
			$array[] = $userTicket;
		}

		return $this->json(['tickets' => $array]);
	}


	/**
	 * @Route("/new/", name="ut_new_get", methods="GET")
	 * @param Request $request
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function getNew(Request $request): Response
	{
		$this->auth($request);

		$user = $this->getCurrentUser();
		if ($user->getType()->getId() !== 1) {
			throw new AccessDeniedHttpException();
		}

		$userTickets = $this->getList->getNewTickets();
		$array = [];
		/** @var UserTicket $userTicket */
		foreach ($userTickets as $userTicket) {
			$user = $userTicket->getUser();

			$dUserTickets = $user->getUserTickets();
			$new = [];
			foreach ($dUserTickets as $dUserTicket){
				$clone = clone $dUserTicket;
				$clone->setUser(null);
				$clone->setLessonUsers(new ArrayCollection());
				$new[] = $clone;
			}
			$user->setUserTickets(new ArrayCollection($new));

			$lessonUsers = $userTicket->getLessonUsers();
			foreach ($lessonUsers as $lessonUser){
				$lesson = $lessonUser->getLesson();
				$lesson->setLessonUsers(new ArrayCollection());
				$lesson->setLessonSet(null);
				$lessonUser->setUser(null);
				$lessonUser->setUserTicket(null);
			}
			$array[] = $userTicket;
		}

		return $this->json(['tickets' => $array]);
	}
}