<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 10/06/2018
 * Time: 23:12
 */

namespace App\Service\Auth;


use App\Entity\User;
use App\Entity\UserCode;
use App\Service\Sms\Sender;
use Doctrine\ORM\EntityManagerInterface;

class CodeProcessor
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Sender
     */
    private $sender;

    /**
     * CodeProcessor constructor.
     * @param CodeGenerator $codeGenerator
     * @param EntityManagerInterface $entityManager
     * @param Sender $sender
     */
    public function __construct(
        CodeGenerator $codeGenerator,
        EntityManagerInterface $entityManager,
        Sender $sender
    )
    {
        $this->codeGenerator = $codeGenerator;
        $this->entityManager = $entityManager;
        $this->sender = $sender;
    }

    /**
     * @param User $user
     */
    public function process(User $user): void
    {
        $code = $this->codeGenerator->generate();

        $userCode = new UserCode();
        $userCode
            ->setCode($code)
			->setTimestamp(time())
            ->setUser($user)
            ->setIsUsed(false);

        $this->entityManager->persist($userCode);
        $this->entityManager->flush();

        $this->sender->send($user->getPhone(), $code);
    }
}