<?php


namespace NFMailchimp\EmailCRM\WpBridge\Database;

use NFMailchimp\EmailCRM\WpBridge\Contracts\WpOptionsApiContract;

class WPOptionsApi implements WpOptionsApiContract
{
	/**
	 * @inheritDoc
	 */
	public function updateOption(string $key, $data): void
	{
		update_option($key, $data);
	}

	/**
	 * @inheritDoc
	 */
	public function getOption(string $key)
	{
		return get_option($key);
	}
}
