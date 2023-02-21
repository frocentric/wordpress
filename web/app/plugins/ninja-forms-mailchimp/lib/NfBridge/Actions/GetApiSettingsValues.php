<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSetting;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;

/**
 * Get API settings per ApiSettings Entity specification
 *
 * This is the NF version; could be abstracted with contract when compared
 * with CF
 */
class GetApiSettingsValues
{

	/**
	 *
	 * @var ApiSettings
	 */
	protected $apiSettings;

	/**
	 * Array of key-value pairs of API settings and values
	 * @var array
	 */
	protected $apiSettingsValues = [];

	/**
	 * Inject ApiSettings entity to retrieve values from Ninja Forms
	 * @param ApiSettings $apiSettings
	 */
	public function __construct(ApiSettings $apiSettings)
	{
		$this->apiSettings = $apiSettings;
	}

	/**
	 * Get API settings stored in core plugin
		 *
		 * @return array
		 */
	public function getApiSettingsValues(): array
	{

		/** @var ApiSetting $apiSetting */
		foreach ($this->apiSettings->getApiSettings() as $apiSetting) {
			$key = $apiSetting->getId();

			$expectedDataType = $apiSetting->getExpectedDataType();

			switch ($expectedDataType) {
				case 'userProvidedString':
				case 'externallySetString':
					$value = Ninja_Forms()->get_setting($key);

					$this->apiSettingsValues[$key] = $value;
					break;
				case 'passThrough':
					$value = $apiSetting->getLabel();

					$this->apiSettingsValues[$key] = $value;
					break;
				case 'bool':
				default:
					break;
			}
		}

		return $this->apiSettingsValues;
	}
}
