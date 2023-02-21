<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

interface SubmissionDataContract extends Arrayable
{
	/**
	 * Get value or default
	 *
	 * @param string $key
	 * @param null $default
	 * @return mixed|null
	 */
	public function getValue(string $key, $default = null);
}
