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

class TinkoffClient
{
	/**
	 * @var ClientInterface
	 */
	private $client;

	/** @var string */
	private $bankUrl;
	/** @var string */
	private $password;
	/** @var string */
	private $terminalKey;

	/**
	 * Client constructor.
	 *
	 * @param ClientInterface $client
	 */
	public function __construct(
		ClientInterface $client
	) {
		$this->client = $client;
		$this->terminalKey = getenv('TINKOFF_TERMINAL_KEY');
		$this->password = getenv('TINKOFF_PASSWORD');
		$this->bankUrl = getenv('TINKOFF_URL');
	}

	/**
	 * @param Command $command
	 *
	 * @return array
	 */
	public function execute(Command $command): array
	{

		try {
			$data = $command->getData() + [
					'TerminalKey' => $this->terminalKey,
				];
			$token = $this->countToken($data);
			$response = $this->client->request(
				$command->getMethod(),
				$this->bankUrl.$command->getPath(),
				[
					'json' => $data + [
							'Token' => $token,
						],
				]
			);
		} catch (GuzzleException $e) {
			return [
				'error' => $e->getMessage(),
			];
		}

		return $command->prepareAnswer($response);
	}

	private function countToken(array $data): string
	{
		$data['Password'] = $this->password;
		ksort($data);
		$concat = implode( $data);

		return hash('sha256', $concat);
	}

}