<?php


namespace NFMailchimp\EmailCRM\WpBridge\Database;

use NFMailchimp\EmailCRM\Shared\Contracts\ArrayStore;

/**
 * A getter/setter for using options table as key/ value store where value is an array
 */
class OptionsStore implements ArrayStore
{

	/**
	 * @var string
	 */
	protected $key;
	/**
	 * OptionsStore constructor.
	 * @param string $key The name of the key
	 */
	public function __construct(string $key)
	{
		$this->key = $key;
	}

	/** @inheritDoc */
	public function getData(): array
	{
		return get_option($this->getKey(), []);
	}

	/** @inheritDoc */
	public function getKey(): string
	{
		return $this->key;
	}

	/** @inheritDoc */
	public function saveData(array $data)
	{
		 update_option($this->getKey(), $data);
	}
}
