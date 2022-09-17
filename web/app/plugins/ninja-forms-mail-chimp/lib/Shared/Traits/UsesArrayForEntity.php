<?php


namespace NFMailchimp\EmailCRM\Shared\Traits;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

trait UsesArrayForEntity
{
	/**
	 * @var array
	 */
	private $items;

	/**
	 * Get a single config value
	 *
	 * @param string $name Value name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->items)) {
			return $this->items[$name];
		}
	}

	/**
	 * Update a single config value
	 *
	 * @param string $name Value name
	 * @param mixed $value New value
	 * @return SimpleEntity
	 */
	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->items)) {
			$this->items[$name] = $value;
		}

		return $this;
	}

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{
		return new static($items);
	}

	/** @inheritDoc */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Can this object supply a value?
	 *
	 * @param string $name Name of the value
	 * @return bool
	 */
	public function hasValue($name): bool
	{
		return ! empty($this->items) && array_key_exists($name, $this->items);
	}

	/** @inheritDoc */
	public function toArray(): array
	{
		return $this->items;
	}

	protected function setItems(array $items)
	{
		$this->items = $items;
	}

	/**
	 * At item to collection
	 *
	 * @param string $key
	 * @param $value
	 */
	public function addData(string $key, $value)
	{
		$this->items[$key] = $value;
	}
}
