<?php

namespace NFMailchimp\EmailCRM\Mailchimp\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Standard field used in all Subscribers
 *
 */
class StandardSubscriberField extends SimpleEntity
{

	/**
	 * Programmatic name
	 * @var string
	 */
	protected $name;

	/**
	 * Human readable version of name
	 * @var string
	 */
	protected $title;

	/**
	 * Variable type
	 * @var string
	 */
	protected $type;

	/**
	 * Values allowed for this field
	 * @var array
	 */
	protected $allowedValues;

	/**
	 * Get name
	 * @return string
	 */
	public function getName(): string
	{
		return isset($this->name) ? (string) $this->name : '';
	}

	/**
	 * Get title
	 * @return string
	 */
	public function getTitle(): string
	{
		return isset($this->title) ? (string) $this->title : '';
	}

	/**
	 * Get type
	 * @return string
	 */
	public function getType(): string
	{
		return isset($this->type) ? (string) $this->type : '';
	}

	/**
	 * Get allowed values
	 * @return string
	 */
	public function getAllowedValues(): array
	{
		return isset($this->allowedValues) ? (array) $this->allowedValues : [];
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
