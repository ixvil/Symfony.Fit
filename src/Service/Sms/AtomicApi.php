<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/06/2018
 * Time: 01:05
 */

namespace App\Service\Sms;

use GuzzleHttp\ClientInterface;

class AtomicApi implements SenderApiInterface
{
    public $config = [
        'host' => 'http://api.atompark.com/api/sms/3.0/sendSMS',
        'public_key' => 'fa388e7ec22966c8422fa54126a1ff61',
        'sender' => '79096511403',
        'sms_lifetime' => '1'
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
            'key' => $this->config['public_key'],
            'sms_lifetime' => $this->config['sms_lifetime'],
            'text' => $code,
            'phone' => preg_replace('/[^0-9]/', '', $phone),
            'sender' => $this->config['sender'],
            'datetime' => '',
        ];

        $controlSum = $this->countSum($requestParams);

        $requestParams['sum'] = $controlSum;

        $url = $this->config['host'];

        $response = $this->client->request(
            'POST',
            $url,
            ['form_params' => $requestParams]
        );

//        throw new \RuntimeException(print_r($requestParams, 1) . $response->getBody());
        return true;
    }

    /**
     * @param array $requestParams
     * @return string
     */
    private function countSum(array $requestParams): string
    {

        $params = $requestParams + [
                'version' => '3.0',
                'action' => 'sendSMS',
//                'key' => $this->config['public_key']
            ];
        ksort($params);

        $line = join('', $params);

        $line .= getenv('ATOMIC_PRIVATE');
//        return $line;
        return md5($line);

    }
}