<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/06/2018
 * Time: 02:46
 */

namespace App\Service\Auth\Token;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class Creator
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        TokenGenerator $tokenGenerator,
        EntityManagerInterface $entityManager
    )
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @return string   front token
     */
    public function create(User $user): string
    {
        $frontToken = $this->tokenGenerator->generateFrontToken();

        $userToken = $this->tokenGenerator->generateUserToken($frontToken, $user);

        $this->entityManager->persist($userToken);
        $this->entityManager->flush();

        return $frontToken;
    }
}