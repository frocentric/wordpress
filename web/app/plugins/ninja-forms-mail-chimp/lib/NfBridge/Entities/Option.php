<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * NF option entity
 *
 * This entity is used to construct a Ninja Forms option
 *
 */
class Option extends SimpleEntity
{

	/**
	 * Label for option
	 * @var string
	 */
	protected $label;
	
	/**
	 * Value for option
	 * @var string
	 */
	protected $value;
	
	/**
	 * Return option label
	 * @return string
	 */
	public function getLabel(): string
	{
		return isset($this->label) ? (string) $this->label : '';
	}

	/**
	 * Return option value
	 * @return string
	 */
	public function getValue(): string
	{
		return isset($this->value) ? (string) $this->value : '';
	}

	/**
	 * Set option label
	 * @param string $stringValue
	 * @return Option
	 */
	public function setLabel(string $stringValue): Option
	{
		$this->label = $stringValue;

		return $this;
	}

	/**
	 * Set option label
	 * @param string $stringValue
	 * @return Option
	 */
	public function setValue(string $stringValue): Option
	{
		$this->value = $stringValue;

		return $this;
	}
}
