<?php


namespace NFMailchimp\EmailCRM\WpBridge\Contracts;

interface WpOptionsApiContract
{

	/**
	 * Wraps WordPress' update_option()
	 * @param string $key
	 * @param array|string|integer $data
	 */
	public function updateOption(string $key, $data) : void;

	/**
	 * Wraps WordPress' get_option()
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getOption(string $key);
}
