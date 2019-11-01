<?php


namespace App\Service\Sberbank\Commands;


use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class Init implements Command
{
	/** @var integer */
	private $amount;
	/** @var integer */
	private $orderId;

	const PATH = 'Init';

	public function getData(): array
	{
		return [
			'OrderId' => $this->orderId,
			'Amount'  => $this->amount * 100, //копейки превращает в рубли
		];
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
		if (isset($data->ErrorCode) && $data->ErrorCode != 0) {
			return ['error' => $data->Message];
		}

		return ['formUrl' => $data->PaymentURL, 'status' => 'ok', 'bank_payment_id' => $data->PaymentId];
	}

	/**
	 * @param int $amount
	 *
	 * @return Init
	 */
	public function setAmount(int $amount): Init
	{
		$this->amount = $amount;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getAmount(): int
	{
		return $this->amount;
	}

	/**
	 * @param int $orderId
	 *
	 * @return Init
	 */
	public function setOrderId(int $orderId): Init
	{
		$this->orderId = $orderId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOrderId(): int
	{
		return $this->orderId;
	}
}