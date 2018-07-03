<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 13:03
 */

namespace App\Service\Sberbank\Commands;


use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class RegisterCommand implements Command
{
    /** @var string */
    private $token;

    /** @var integer */
    private $orderNumber;

    /** @var integer */
    private $amount;

    /** @var string */
    private $returnUrl;

    const PATH = 'register.do';

    public function __construct()
    {
        $this->returnUrl = getenv('LOCAL_URL');
    }

    public function getData(): array
    {
        return [
            'token' => $this->token,
            'orderNumber' => $this->orderNumber,
            'amount' => $this->amount * 100, //копейки превращает в рубли и делаем скидку в 20 процентов
            'returnUrl' => $this->returnUrl
        ];
    }

    /**
     * @param string $token
     * @return RegisterCommand
     */
    public function setToken(string $token): RegisterCommand
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param int $orderNumber
     * @return RegisterCommand
     */
    public function setOrderNumber(int $orderNumber): RegisterCommand
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * @param int $amount
     * @return RegisterCommand
     */
    public function setAmount(int $amount): RegisterCommand
    {
        $this->amount = $amount;
        return $this;
    }

    public function getMethod(): string
    {
        return Request::METHOD_POST;
    }

    public function getPath(): string
    {
        return self::PATH;
    }

    public function prepareAnswer(ResponseInterface $response): array
    {
        $data = json_decode($response->getBody()->getContents());
        if (isset($data->errorCode)) {
            return ['error' => $data->errorMessage];
        }

        return ['formUrl' => $data->formUrl, 'status' => 'ok'];
    }

    /**
     * @param string $returnUrl
     * @return RegisterCommand
     */
    public function setReturnUrl(string $returnUrl): RegisterCommand
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }
}