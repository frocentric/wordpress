<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Actions\ConstructActionSetting;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSettings;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSetting;

/**
 * Construct collection of action settings
 *
 */
class ConstructActionSettings
{

	/**
	 * Action settings
	 * @var ActionSettings
	 */
	protected $actionSettings;

	/**
	 * Construct collection of action settings
	 */
	public function __construct()
	{
		$this->actionSettings = new ActionSettings();
	}

	/**
	 * Adds field mapping textbox to action settings
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 */
	public function addTextboxFieldMap(string $name, string $label, string $value): ActionSettings
	{
		/** @var ConstructActionSetting $creator */
		/** @var ActionSetting $actionSetting */
		$creator = new ConstructActionSetting();

		$actionSetting = $creator->createTextboxFieldMap($name, $label, $value);

		$this->actionSettings->addActionSetting($actionSetting);

		return $this->actionSettings;
	}

	/**
	 * Adds toggle setting to action settings
	 * @param string $name
	 * @param string $label
	 * @param int $value
	 */
	public function addToggleSetting(string $name, string $label, int $value): ActionSettings
	{
		/** @var ConstructActionSetting $creator */
		/** @var ActionSetting $actionSetting */
		$creator = new ConstructActionSetting();

		$actionSetting = $creator->createToggleSetting($name, $label, $value);

		$this->actionSettings->addActionSetting($actionSetting);

		return $this->actionSettings;
	}

	/**
	 *
	 * @return ActionSettings
	 */
	public function getActionSettings(): ActionSettings
	{
		return $this->actionSettings;
	}
}
