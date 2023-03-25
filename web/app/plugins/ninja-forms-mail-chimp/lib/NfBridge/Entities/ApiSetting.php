<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Single API setting in form design
 *
 * This entity describes a piece of data that the API requires.  NF / CF
 * is responsible for storing and delivering this data back to the API.
 *
 */
class ApiSetting extends SimpleEntity
{

	/**
	 * Id for API setting entity
	 * @var string
	 */
	protected $id;

	/**
	 * Label for API setting entity
	 * @var string
	 */
	protected $label;

	/**
	 * Type of data expected in this setting
	 * @var string
	 */
	protected $expectedDataType;

	/**
	 * Constructs object from given array
	 * @param array $items
	 * @return SimpleEntity
	 */
	public static function fromArray(array $items): SimpleEntity
	{
		$obj = new static();
		foreach ($items as $property => $value) {
			$obj = $obj->__set($property, $value);
		}
		return $obj;
	}

	/**
	 * Return API setting Id
	 * @return string
	 */
	public function getId(): string
	{
		return isset($this->id) ? (string) $this->id : '';
	}

	/**
	 * Return API setting label
	 * @return string
	 */
	public function getLabel(): string
	{
		return isset($this->label) ? (string) $this->label : '';
	}

	/**
	 * Return API setting data type
	 * @return string
	 */
	public function getExpectedDataType(): string
	{
		return isset($this->expectedDataType) ? (string) $this->expectedDataType : '';
	}

	/**
	 * Set API setting Id
	 * @param string $stringValue
	 * @return ApiSetting
	 */
	public function setId(string $stringValue): ApiSetting
	{
		$this->id = $stringValue;

		return $this;
	}

	/**
	 * Set API setting label
	 * @param string $stringValue
	 * @return ApiSetting
	 */
	public function setLabel(string $stringValue): ApiSetting
	{
		$this->label = $stringValue;

		return $this;
	}

	/**
	 * Set API setting expected data type
	 * @param string $stringValue
	 * @return ApiSetting
	 */
	public function setExpectedDataType(string $stringValue): ApiSetting
	{
		$this->expectedDataType = $stringValue;

		return $this;
	}
}
