<?php


namespace NFMailchimp\EmailCRM\Shared\Traits;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

trait ConvertsFromArrayWithSnakeCaseing
{
	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{

		$obj = new static();
		foreach ($items as $property => $value) {
			$obj->__set(static::unSnake($property), $value);
		}
		return $obj;
	}


	/**
	 * Convert snake case to camel case
	 * @param string $str
	 * @return string
	 */
	protected static function unSnake(string $str): string
	{
		if (false === strpos($str, '_')) {
			return $str;
		}
		$str = ucWords($str, '_');
		return lcfirst(str_replace('_', '', $str));
	}
}
