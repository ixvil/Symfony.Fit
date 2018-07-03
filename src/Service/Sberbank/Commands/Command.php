<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 01/07/2018
 * Time: 13:03
 */

namespace App\Service\Sberbank\Commands;

use Psr\Http\Message\ResponseInterface;

interface Command
{
    public function getData(): array;

    public function getMethod(): string;

    public function getPath(): string;

    public function prepareAnswer(ResponseInterface $response): array;
}