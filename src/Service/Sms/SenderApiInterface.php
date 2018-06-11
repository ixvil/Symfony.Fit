<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/06/2018
 * Time: 01:03
 */

namespace App\Service\Sms;


interface SenderApiInterface
{
    public function send(string $phone, string $code): bool;
}