<?php


namespace NFMailchimp\EmailCRM\Shared\Storage;

use NFMailchimp\EmailCRM\Shared\Contracts\ArrayStore;

/**
 * Implimentation of ArrayStore that is for testing.
 */
class InMemoryArrayStore implements ArrayStore
{

	/**
	 * @var string
	 */
	protected $key;
	/**
	 * @var array
	 */
	protected $data;

	public function __construct(string $key)
	{
		$this->key = $key;
		$this->data = [];
	}

	public function getData(): array
	{
		return $this->data;
	}

	/** @inheritDoc */
	public function saveData(array $data)
	{
		//would save to database in a real adapter
		$this->data = $data;
	}

	/** @inheritDoc */
	public function getKey(): string
	{
		return $this->key;
	}
}
