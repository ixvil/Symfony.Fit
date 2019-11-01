<?php


namespace App\Service\Sberbank\Commands;


use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class GetState implements Command
{
	/** @var integer */
	private $paymentId;


	public function getData(): array
	{
		return [
			'PaymentId' => $this->paymentId,
		];
	}

	public function getMethod(): string
	{
		return Request::METHOD_POST;
	}

	public function getPath(): string
	{
		return 'GetState';
	}

	public function prepareAnswer(ResponseInterface $response): array
	{
		$contents = $response->getBody()->getContents();
		$data = json_decode($contents);
		if (isset($data->Status)) {
			return ['orderStatus' => $data->Status];
		}
		if (isset($data->ErrorCode)) {
			return ['error' => $data->Message, 'json' => $contents];
		}

		return [];
	}

	/**
	 * @param int $paymentId
	 *
	 * @return GetState
	 */
	public function setPaymentId(int $paymentId): GetState
	{
		$this->paymentId = $paymentId;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPaymentId(): int
	{
		return $this->paymentId;
	}
}