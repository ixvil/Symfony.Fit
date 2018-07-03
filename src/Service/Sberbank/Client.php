<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 12:33
 */

namespace App\Service\Sberbank;

use App\Service\Sberbank\Commands\Command;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    /**
     * @var ClientInterface
     */
    private $client;

    /** @var string */
    private $sberbankUrl;
    /** @var string */
    private $login;
    /** @var string */
    private $password;

    /**
     * Client constructor.
     * @param ClientInterface $client
     */
    public function __construct(
        ClientInterface $client
    )
    {
        $this->client = $client;
        $this->login = getenv('SBERBANK_LOGIN');
        $this->password = getenv('SBERBANK_PASSWORD');
        $this->sberbankUrl = getenv('SBERBANK_URL');
    }

    /**
     * @param Command $command
     * @return array
     */
    public function execute(Command $command): array
    {

        try {
            $response = $this->client->request(
                $command->getMethod(),
                $this->sberbankUrl . $command->getPath(),
                [
                    'form_params' => $command->getData() + [
                            'userName' => $this->login,
                            'password' => $this->password
                        ]
                ]
            );
        } catch (GuzzleException $e) {
            return [
                'error' => $e->getMessage()
            ];
        }

        return $command->prepareAnswer($response);
    }

}