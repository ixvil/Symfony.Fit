<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/06/2018
 * Time: 00:06
 */

namespace App\Service\Auth;


use App\Entity\User;
use App\Entity\UserCode;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CodeChecker
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $code
     * @param User $user
     * @return bool
     */
    public function check(string $code, User $user)
    {
        /** @var EntityRepository $userCodeRepository */
        $userCodeRepository = $this->entityManager->getRepository(UserCode::class);
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('user', $user))
            ->andWhere(Criteria::expr()->eq('code', $code))
            ->andWhere(Criteria::expr()->gte('timestamp', new \DateTime('10 minutes ago')));

        $userCodeCollection = $userCodeRepository->matching(
            $criteria
        );

        if ($userCodeCollection->count() !== 0) {
            return true;
        } else {
            return false;
        }
    }
}