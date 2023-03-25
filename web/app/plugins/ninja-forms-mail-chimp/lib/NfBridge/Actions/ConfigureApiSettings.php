<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSetting;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;

/**
 * Construct NF settings configuration from ApiSettings
 *
 */
class ConfigureApiSettings
{

	/**
	 *
	 * @var ApiSettings
	 */
	protected $apiSettings;

	/**
	 * NF Settings configuration array
	 * @var array
	 */
	protected $settingsConfig = [];

	/**
	 * Construct NF settings configuration from ApiSettings
	 * @param ApiSettings $apiSettings
	 */
	public function __construct($apiSettings)
	{
		$this->apiSettings = $apiSettings;
		$this->constructSettingsConfig();
	}

	/**
	 * Iterate ApiSettings to construct NF Settings
	 */
	protected function constructSettingsConfig()
	{
		$settingsConfig = array();
		/** @var ApiSetting $setting */
		foreach ((array) $this->apiSettings->getApiSettings() as $setting) {
			$type = $this->selectSettingType($setting->getExpectedDataType());

			if ('none' === $type) {
				continue;
			}
			
			$settingsConfig[$setting->getId()] = array(
				'id' => $setting->getId(),
				'type' => $type,
				'label' => $setting->getLabel()
			);
		}

		$this->settingsConfig = $settingsConfig;
	}

	/**
	 * Return the correct NF setting field type for the expected data type
	 * @param string $expectedDataType
	 * @return string
	 */
	protected function selectSettingType(string $expectedDataType)
	{
		$type = 'textbox';

		switch ($expectedDataType) {
			case 'externallySetString':
				$type = 'html';
				break;
			case 'passThrough':
				$type = 'none';
				break;
			case 'userProvidedString':
			default:
		}

		return $type;
	}

	/**
	 * Return configured NF settings array
	 * @return array
	 */
	public function getSettingsConfig()
	{
		return $this->settingsConfig;
	}
}
