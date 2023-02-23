<?php


namespace NFMailchimp\EmailCRM\Shared;

use phpDocumentor\Reflection\Types\Static_;
use NFMailchimp\EmailCRM\Shared\Contracts\Arrayable;

/**
 * Class ArrayLike
 *
 * Implementation of ArrayAccess for objects we are not ready to type.
 */
class ArrayLike implements \ArrayAccess, Arrayable
{
	/**
	 * Items being collected
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * ArrayLike constructor.
	 * @param array $items
	 */
	public function __construct(array  $items = [])
	{
		$this->items = $items;
	}

	/** @inheritDoc */
	public function toArray(): array
	{
		return  $this->items;
	}


	/** @inheritDoc */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}
	/** @inheritDoc */
	public function offsetExists($offset)
	{
		return isset($this->items[$offset]);
	}
	/** @inheritDoc */
	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	/** @inheritDoc */
	public function offsetGet($offset)
	{
		return isset($this->items[$offset]) ? $this->items[$offset] : null;
	}

	/** @inheritDoc */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
