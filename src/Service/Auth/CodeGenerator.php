<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 10/06/2018
 * Time: 23:11
 */

namespace App\Service\Auth;


class CodeGenerator
{
    /**
     * @return int
     */
    public function generate(): int
    {
        return rand(100000, 999999);
    }
}