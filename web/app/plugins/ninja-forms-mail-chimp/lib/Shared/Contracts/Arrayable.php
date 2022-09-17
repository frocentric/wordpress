<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Interface Arrayable
 *
 * contract that classes that convert to arrays for JSON serialization MUST impliment
 */
interface Arrayable extends \JsonSerializable
{
	/**
	 * Convert object to array
	 *
	 * @return array
	 */
	public function toArray() : array;
}
