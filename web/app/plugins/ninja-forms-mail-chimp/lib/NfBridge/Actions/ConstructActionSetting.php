<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSetting;

/**
 * Construct a single action setting
 *
 */
class ConstructActionSetting
{

	/**
	 * Action settings
	 * @var ActionSetting
	 */
	protected $actionSetting;

	/**
	 * Adds field mapping textbox to action settings
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 */
	public function createTextboxFieldMap(string $name, string $label, string $value = ''): ActionSetting
	{

		$settingArray = array(
			'name' => $name,
			'label' => $label,
			'group' => 'primary',
			'type' => 'textbox',
			'use_merge_tags' => 1,
			'width' => 'full'
		);

		if ('' !== $value) {
			$settingArray['value'] = $value;
		}

		$actionSetting = ActionSetting::fromArray($settingArray);

		return $actionSetting;
	}

	/**
	 * Adds toggle setting to action settings
	 * @param string $name
	 * @param string $label
	 * @param int $value
	 */
	public function createToggleSetting(string $name, string $label, int $value = 0): ActionSetting
	{
	

		$settingArray = array(
			'name' => $name,
			'label' => $label,
			'group' => 'primary',
			'type' => 'textbox',
			'use_merge_tags' => 1,
			'width' => 'full'
		);

		if (0 !== $value) {
			$settingArray['value'] = 1;
		} else {
			$settingArray['value'] = 0;
		}
		
		$actionSetting = ActionSetting::fromArray($settingArray);

		return $actionSetting;
	}
}
