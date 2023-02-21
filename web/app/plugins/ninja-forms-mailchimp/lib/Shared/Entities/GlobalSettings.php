<?php

namespace NFMailchimp\EmailCRM\Shared\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\Shared\Entities\GlobalSetting;

/**
 * Describes a collection of Global Settings
 *
 * Global settings are shared among all the forms, not form-specific
 */
class GlobalSettings extends SimpleEntity
{

	/**
	 * Id of the Global settings collection
	 * @var string
	 */
	protected $id;

	/**
	 * Label of the Global settings collection
	 * @var string
	 */
	protected $label;

	/**
	 * @var GlobalSetting[]
	 */
	protected $globalSettings;

	public function __construct()
	{
		$this->globalSettings = [];
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
	 * Set Global setting Id
	 * @param string $stringValue
	 * @return GlobalSettings
	 */
	public function setId(string $stringValue): GlobalSettings
	{
		$this->id = $stringValue;

		return $this;
	}

	/**
	 * Set Global setting label
	 * @param string $stringValue
	 * @return GlobalSettings
	 */
	public function setLabel(string $stringValue): GlobalSettings
	{
		$this->label = $stringValue;

		return $this;
	}

	/** @inheritDoc */
	public static function fromArray(array $items): SimpleEntity
	{

		$obj = new static();


		foreach ($items as $property => $value) {
			// if string, set property value
			if ('globalSettings' !== $property) {
				$obj = $obj->__set($property, $value);
				continue;
			}
			// Add entities stored in array

			foreach ((array) $value as $list) {
				if (!is_array($list)) {
					if (is_a($list, GlobalSetting::class)) {
						$obj->addGlobalSetting($list);
						continue;
					} else {
						$list = (array) $list;
					}
				}

				$obj->addGlobalSetting(GlobalSetting::fromArray($list));
			}
		}

		return $obj;
	}

		/**
		 * Convert GlobalSetting object into associative array
		 *
		 * @return array
		 */
	public function toArray(): array
	{
			
		$vars = get_object_vars($this);
				
		$array = [];
				
		foreach ($vars as $property => $value) {
			if ('globalSettings'===$property) {
				$globalSettings=[];
					
				if (!is_array($value)) {
					$array['globalSettings']=[];
					continue;
				}
								
				foreach ($value as $key => $globalSetting) {
					if (is_a($globalSetting, GlobalSetting::class)) {
						$globalSettings[$key]=$globalSetting->toArray();
					}
				}
							
				$value = $globalSettings;
			} elseif (is_object($value) && is_callable([$value, 'toArray'])) {
				$value = $value->toArray();
			}
						
			$array[$property] = $value;
		}
				
		return $array;
	}
		
	/**
	 * Add an Global Setting to collection
	 *
	 * @param GlobalSetting $globalSetting
	 *
	 * @return GlobalSettings
	 */
	public function addGlobalSetting(GlobalSetting $globalSetting): GlobalSettings
	{

		$this->globalSettings[$globalSetting->getId()] = $globalSetting;
		return $this;
	}

	/**
	 * Get a Global Setting from collection
	 *
	 * @param string $key
	 *
	 * @return GlobalSetting
	 * @throws Exception
	 */
	public function getGlobalSetting(string $key): GlobalSetting
	{
		if (!isset($this->globalSettings[$key])) {
			throw new Exception();
		}
		return $this->globalSettings[$key];
	}

	/**
	 * Get all Global Settings in collection
	 *
	 * @return GlobalSetting[]
	 */
	public function getGlobalSettings(): array
	{
		return $this->globalSettings;
	}
}
