<?php


namespace NFMailchimp\EmailCRM\WpBridge\Database;

use NFMailchimp\EmailCRM\Shared\Contracts\ArrayStore;

/**
 * A getter/setter for using transients as key/ value store where value is an array
 */
class TransientStore implements ArrayStore
{

	/**
	 * @var string
	 */
	protected $key;
	/**
	 * TransientStore constructor.
	 * @param string $key The name of the key
	 */
	public function __construct(string $key)
	{
			$this->key = $key;
	}

	/** @inheritDoc */
	public function getData(): array
	{
				$savedData = get_transient($this->key);
		if (is_array($savedData)) {
			return $savedData;
		}
				return [];
	}

	/** @inheritDoc */
	public function getKey(): string
	{
			return $this->key;
	}

	/** @inheritDoc */
	public function saveData(array $data)
	{
			set_transient($this->key, $data);
	}
}
