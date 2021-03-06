<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 10/06/2018
 * Time: 22:44
 */

namespace App\Service\Sms;


class Sender
{
    /**
     * @var SenderApiInterface
     */
    private $senderApi;

    public function __construct(
        SenderApiInterface $senderApi
    ) {
        $this->senderApi = $senderApi;
    }

    /**
     * @param string $phone
     * @param int    $code
     */
    public function send(string $phone, int $code): void
    {
        if (getenv('SEND_SMS')) {
            $this->senderApi->send($phone, 'Your login code for stretchandgo: '.$code);
        }
    }

    public function sendToOwner(string $message): void
    {
        if (getenv('SEND_SMS')) {
            $this->senderApi->send(
                getenv('PHONE_OWNER'),
                $message
            );
        }
    }

    public function sendToAdmin(string $message): void
    {
        if (getenv('SEND_SMS')) {
            $this->senderApi->send(
                getenv('PHONE_ADMIN'),
                $message
            );
        }
    }
}