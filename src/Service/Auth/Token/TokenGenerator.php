<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/06/2018
 * Time: 02:50
 */

namespace App\Service\Auth\Token;


use App\Entity\User;
use App\Entity\UserToken;

class TokenGenerator
{
    public function generateFrontToken(): string
    {
        return md5(rand(0, 9999999999));
    }

    public function generateSaltedToken(string $frontToken): string
    {
        return md5($frontToken . getenv('TOKEN_SALT') . $_SERVER['HTTP_USER_AGENT']);
    }

    public function generateUserToken(string $frontToken, User $user): UserToken
    {
        $userToken = new UserToken();

        $userToken
            ->setUser($user)
            ->setIsActive(true)
            ->setToken($this->generateSaltedToken($frontToken));

        return $userToken;
    }
}