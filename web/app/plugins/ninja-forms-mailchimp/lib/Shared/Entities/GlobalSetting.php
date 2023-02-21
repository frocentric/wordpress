<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;

/**
 * Single Global setting in form design
 *
 * This entity describes a piece of data that the Global requires.  NF / CF
 * is responsible for storing and delivering this data back to the Global.
 *
 */
class GlobalSetting extends SimpleEntity
{

	/**
	 * Id for Global setting entity
	 * @var string
	 */
	protected $id;

	/**
	 * Label for Global setting entity
	 * @var string
	 */
	protected $label;

	/**
	 * String classification of this GlobalSetting
		 *
		 * Types:
		 *
		 * userProvidedString : User prompted to enter value (e.g. API Key)
		 * externallySetString : Value is set through non-user method (e.g. automatically generated RefreshToken )
		 *
	 * @var string
	 */
	protected $expectedDataType;

		/**
		 * Stored value of the setting
		 *
		 * Upon initial configuration inside the ApiModule, this value is null.
		 * The Integrating Plugin manages the solicitation of  values from the
		 * user, storing of the values, and returning the values upon demand.
		 *
		 * @var mixed
		 */
		protected $value=null;
		
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
	 * Return Global setting Id
	 * @return string
	 */
	public function getId(): string
	{
		return isset($this->id) ? (string) $this->id : '';
	}

	/**
	 * Return Global setting label
	 * @return string
	 */
	public function getLabel(): string
	{
		return isset($this->label) ? (string) $this->label : '';
	}

	/**
	 * Return Global setting data type
	 * @return string
	 */
	public function getExpectedDataType(): string
	{
		return isset($this->expectedDataType) ? (string) $this->expectedDataType : '';
	}

		/**
		 * Get the value for the GlobalSetting
		 * @return mixed
		 */
	public function getValue()
	{
		return $this->value;
	}
		
	/**
	 * Set Global setting Id
	 * @param string $stringValue
	 * @return GlobalSetting
	 */
	public function setId(string $stringValue): GlobalSetting
	{
		$this->id = $stringValue;

		return $this;
	}

	/**
	 * Set Global setting label
	 * @param string $stringValue
	 * @return GlobalSetting
	 */
	public function setLabel(string $stringValue): GlobalSetting
	{
		$this->label = $stringValue;

		return $this;
	}

	/**
	 * Set Global setting expected data type
		 *
	 * @param string $stringValue
	 * @return GlobalSetting
	 */
	public function setExpectedDataType(string $stringValue): GlobalSetting
	{
		$this->expectedDataType = $stringValue;

		return $this;
	}
		
		/**
		 * Set the value for the GlobalSetting
		 *
		 * @param type $param
		 * @return \NFMailchimp\EmailCRM\Shared\Entities\GlobalSetting
		 */
	public function setValue($param):GlobalSetting
	{
		$this->value = $param;
			
		return $this;
	}
}
