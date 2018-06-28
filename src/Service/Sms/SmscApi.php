<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 28/06/2018
 * Time: 23:11
 */

namespace App\Service\Sms;


use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerAwareTrait;

class SmscApi implements SenderApiInterface
{

    use LoggerAwareTrait;

    public $config = [
        'host' => 'https://smsc.ru/sys/send.php',
        'login' => 'vk_516392',
        'psw' => 'gvUYTIUhb345678JHGVH'
    ];

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(
        ClientInterface $client
    )
    {
        $this->client = $client;
    }

    /**
     * @param string $phone
     * @param string $code
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $phone, string $code): bool
    {
        $requestParams = [
            'login' => $this->config['login'],
            'psw' => $this->config['psw'],
            'phones' => $phone,
            'mes' => $code
        ];

        $url = $this->config['host'];

        $response = $this->client->request(
            'POST',
            $url,
            ['form_params' => $requestParams]
        );
        if ($response->getStatusCode() != 200) {
            $this->logger->alert('Sms was not sent, message:' . $response->getBody());
        } else {
            $this->logger->info('Sms was sent:' . $response->getBody());
        }
//        throw new \RuntimeException(print_r($requestParams, 1) . $response->getBody());
        return true;
    }
}