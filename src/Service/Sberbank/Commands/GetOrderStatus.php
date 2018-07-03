<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 02/07/2018
 * Time: 01:09
 */

namespace App\Service\Sberbank\Commands;


use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class GetOrderStatus implements Command
{
    private $orderNumber;

    public function getData(): array
    {
        return ['orderNumber' => $this->orderNumber];
    }

    public function getMethod(): string
    {
        return Request::METHOD_POST;
    }

    public function getPath(): string
    {
        return '/getOrderStatusExtended.do';
    }

    public function prepareAnswer(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();
        $data = json_decode($contents);
        if (isset($data->orderStatus)) {
            return ['orderStatus' => $data->orderStatus];
        }
        if (isset($data->errorCode)) {
            return ['error' => $data->errorMessage, 'json' => $contents];
        }
        return [];
    }

    /**
     * @param mixed $orderNumber
     * @return GetOrderStatus
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }
}