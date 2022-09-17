<?php

namespace NFMailchimp\EmailCRM\NfBridge\Entities;

use NFMailchimp\EmailCRM\Shared\SimpleEntity;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSetting;
use NFMailchimp\EmailCRM\NfBridge\Exception;

/**
 * Describes a collection of NF Bridge API Settings
 */
class ApiSettings extends SimpleEntity
{

	/**
	 * Id of the API settings collection
	 * @var string
	 */
	protected $id;

	/**
	 * Label of the API settings collection
	 * @var string
	 */
	protected $label;

	/**
	 * @var ApiSetting[]
	 */
	protected $apiSettings;

	public function __construct()
	{
		$this->apiSettings = [];
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
	 * Set API setting Id
	 * @param string $stringValue
	 * @return ApiSettings
	 */
	public function setId(string $stringValue): ApiSettings
	{
		$this->id = $stringValue;

		return $this;
	}

	/**
	 * Set API setting label
	 * @param string $stringValue
	 * @return ApiSettings
	 */
	public function setLabel(string $stringValue): ApiSettings
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
			if ('apiSettings' !== $property) {
				$obj = $obj->__set($property, $value);
				continue;
			}
			// Add entities stored in array

			foreach ((array) $value as $list) {
				if (!is_array($list)) {
					if (is_a($list, ApiSetting::class)) {
						$obj->addApiSetting($list);
						continue;
					} else {
						$list = (array) $list;
					}
				}

				$obj->addApiSetting(ApiSetting::fromArray($list));
			}
		}

		return $obj;
	}
/**
		 * Convert ApiSettings object into associative array
		 *
		 * @return array
		 */
	public function toArray(): array
	{
			
		$vars = get_object_vars($this);
				
		$array = [];
				
		foreach ($vars as $property => $value) {
			if ('apiSettings'===$property) {
				$apiSettings=[];
					
				if (!is_array($value)) {
					$array['apiSettings']=[];
					continue;
				}
								
				foreach ($value as $key => $apiSetting) {
					if (is_a($apiSetting, ApiSetting::class)) {
						$apiSettings[$key]=$apiSetting->toArray();
					}
				}
							
				$value = $apiSettings;
			} elseif (is_object($value) && is_callable([$value, 'toArray'])) {
				$value = $value->toArray();
			}
						
			$array[$property] = $value;
		}
				
		return $array;
	}
		
	/**
	 * Add an API Setting to collection
	 *
	 * @param ApiSetting $apiSetting
	 *
	 * @return ApiSettings
	 */
	public function addApiSetting(ApiSetting $apiSetting): ApiSettings
	{

		$this->apiSettings[$apiSetting->getId()] = $apiSetting;
		return $this;
	}

	/**
	 * Get an API Setting from collection
	 *
	 * @param string $key
	 *
	 * @return ApiSetting
	 * @throws Exception
	 */
	public function getApiSetting(string $key): ApiSetting
	{
		if (!isset($this->apiSettings[$key])) {
			throw new Exception();
		}
		return $this->apiSettings[$key];
	}

	/**
	 * Get all API Settings in collection
	 *
	 * @return ApiSetting[]
	 */
	public function getApiSettings(): array
	{
		return $this->apiSettings;
	}
}
