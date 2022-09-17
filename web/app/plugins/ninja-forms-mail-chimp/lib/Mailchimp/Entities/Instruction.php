<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Describes one Instruction for Mailchimp API request
 */
class Instruction extends SimpleEntity
{

	/**
	 * Text name of instruction
	 * @var string
	 */
	protected $instruction = '';

	/**
	 * Value associated with the instruction
	 * @var mixed
	 */
	protected $value = null;

	/**
	 * Key used on specific instructions
	 * @var string
	 */
	protected $key = '';

	/**
	 * @return string
	 */
	public function getInstruction(): string
	{
		return $this->instruction;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}


	/**
	 * @param string $instruction
	 *
	 * @return Instruction
	 */
	public function setInstruction(string $instruction): Instruction
	{
		$this->instruction = $instruction;
		return $this;
	}

		/**
	 * @param string $value
	 *
	 * @return Instruction
	 */
	public function setValue(string $value): Instruction
	{
		$this->value = $value;
		return $this;
	}
		/**
	 * @param string $key
	 *
	 * @return Instruction
	 */
	public function setKey(string $key): Instruction
	{
		$this->key = $key;
		return $this;
	}
	/**
	 * @inheritdoc
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			if (null !== $value) {
				$obj = $obj->__set($property, $value);
			}
		}

		return $obj;
	}
}
