<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 03/03/2019
 * Time: 15:17
 */

namespace App\Helpers;


/**
 * Trait NameTrait
 * @method getName
 *
 * @package App\Helpers
 */
trait NameTrait
{
	public function __toString(): string
	{
		return $this->getName();
	}

}