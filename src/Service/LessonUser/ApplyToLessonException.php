<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 23/06/2018
 * Time: 19:09
 */

namespace App\Service\LessonUser;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApplyToLessonException extends HttpException
{
    public function __construct(string $message = null, \Exception $previous = null, array $headers = array(), ?int $code = 0)
    {
        parent::__construct(400, $message, $previous, $headers, $code);
    }
}