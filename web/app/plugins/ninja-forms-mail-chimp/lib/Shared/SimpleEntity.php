<?php


namespace NFMailchimp\EmailCRM\Shared;

use NFMailchimp\EmailCRM\Shared\Contracts\Arrayable;

abstract class SimpleEntity implements Arrayable
{
	/** @inheritdoc */
	public function toArray(): array
	{
		$vars = get_object_vars($this);
		$array = [];
		foreach ($vars as $property => $value) {
			if (is_object($value) && is_callable([$value, 'toArray'])) {
				$value = $value->toArray();
			}
			$array[$property] = $value;
		}
		return $array;
	}

	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			$obj = $obj->__set($property, $value);
		}
		return $obj;
	}

	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/** @inheritdoc */
	public function __get($name)
	{
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter)) {
			return call_user_func([$this, $getter]);
		}
		if (property_exists($this, $name)) {
			return $this->$name;
		}
	}

	/** @inheritdoc */
	public function __set($name, $value)
	{
		$setter = 'set' . ucfirst($name);
		if (method_exists($this, $setter)) {
			return call_user_func([$this, $setter], $value);
		}
		if (property_exists($this, $name)) {
			$this->$name = $value;
			return $this;
		}
		return $this;
	}
}
