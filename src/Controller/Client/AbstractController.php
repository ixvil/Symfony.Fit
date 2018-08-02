<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 21/06/2018
 * Time: 22:42
 */

namespace App\Controller\Client;


use App\Entity\User;
use App\Entity\UserToken;
use App\Service\Auth\Token\TokenGenerator;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

abstract class AbstractController extends Controller
{
    use LoggerAwareTrait;
    const COOKIES_USER_ID = 'authUserId';
    const COOKIES_AUTH_TOKEN = 'authToken';

    /** @var User $user */
    private $user;

    /** @var EntityRepository $userRepo */
    private $userRepo;
    /**
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * AbstractController constructor.
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
        TokenGenerator $tokenGenerator
    )
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param int $userId
     * @param string $frontToken
     */
    public function checkAuth(int $userId, string $frontToken): void
    {
        $this->userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $this->userRepo->find($userId);
        if (!$user instanceof User) {
            throw new UnprocessableEntityHttpException();
        }

        $userTokenRepository = $this->getDoctrine()->getRepository(UserToken::class);
        $userTokenCollection = $userTokenRepository->matching(
            Criteria::create()
                ->andWhere(Criteria::expr()->eq('user', $user))
                ->andWhere(Criteria::expr()->eq('token', $this->tokenGenerator->generateSaltedToken($frontToken)))
                ->andWhere(Criteria::expr()->eq('isActive', 1))
        );

        if ($userTokenCollection->count() === 0) {
            throw new AccessDeniedHttpException();
        }

        $this->user = $user;
    }

    /**
     * @param Request $request
     */
    public function auth(Request $request): void
    {
	$this->logger->info('cookies: '.print_r($_COOKIE, 1));
        $userId = $request->cookies->get(self::COOKIES_USER_ID);
        $frontToken = $request->cookies->get(self::COOKIES_AUTH_TOKEN);
        $this->logger->info('userId:' . $userId . ' frontToken: ' . $frontToken.' userAgent: '.$_SERVER['HTTP_USER_AGENT']);
        if (!$userId || !$frontToken) {
            return;
        }
        $this->logger->info($userId, $request->cookies->all());
        $this->checkAuth($userId, $frontToken);
    }

    /**
     * @return User
     */
    protected function getCurrentUser(): User
    {
        if (!$this->user instanceof User) {
            throw new UnprocessableEntityHttpException();
        }

        return $this->user;
    }

    /**
     * @param $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = array(), array $context = array()): JsonResponse
    {
        if (!isset($headers['Access-Control-Allow-Origin'])) {
            $headers['Access-Control-Allow-Origin'] = getenv('LOCAL_URL');
            $headers['Access-Control-Allow-Credentials'] = 'true';
        }

        return parent::json($data, $status, $headers, $context);
    }
}
