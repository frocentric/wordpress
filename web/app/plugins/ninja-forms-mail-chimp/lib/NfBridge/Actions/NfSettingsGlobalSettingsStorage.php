<?php

namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\Shared\Contracts\GlobalSettingsStorageContract;
use NFMailchimp\EmailCRM\Shared\Entities\GlobalSetting;
use NFMailchimp\EmailCRM\Shared\Entities\GlobalSettings;

/**
 * Store and retrieve GlobalSettings using Ninja_Forms()->
 *
 */
class NfSettingsGlobalSettingsStorage implements GlobalSettingsStorageContract
{

	/**
	 *
	 * @var GlobalSettings
	 */
	protected $globalSettings;

	public function __construct(GlobalSettings $globalSettings)
	{
		$this->setGlobalSettings($globalSettings);
	}

	/** @inheritDoc */
	public function setGlobalSettings(GlobalSettings $globalSettings): GlobalSettingsStorageContract
	{
		$this->globalSettings = $globalSettings;
		return $this;
	}

	/** @inheritDoc */
	public function getGlobalSettings(): GlobalSettings
	{
		return $this->globalSettings;
	}

	/** @inheritDoc */
	public function storeGlobalSettings(): GlobalSettingsStorageContract
	{
		/** @var GlobalSetting $globalSetting */
		foreach ($this->globalSettings->getGlobalSettings() as $key => $globalSetting) {
			$value = $globalSetting->getValue();

			Ninja_Forms()->update_setting($key, $value);
		}

		return $this;
	}

	/** @inheritDoc */
	public function retrieveGlobalSettings(): GlobalSettingsStorageContract
	{
		/** @var GlobalSetting $globalSetting */
		$globalSettings = $this->globalSettings->getGlobalSettings();

		foreach ($globalSettings as $key => $globalSetting) {
			$value = Ninja_Forms()->get_setting($key);
			$globalSetting->setValue($value);
			$this->globalSettings->addGlobalSetting($globalSetting);
		}

		return $this;
	}

	/** @inheritDoc */
	public function storeGlobalSetting(string $id): GlobalSettingsStorageContract
	{
		/** @var GlobalSetting $globalSetting */
		$globalSetting = $this->globalSettings->getGlobalSetting($id);

		$value = $globalSetting->getValue();

		Ninja_Forms()->update_setting($id, $value);


		return $this;
	}

	/** @inheritDoc */
	public function retrieveGlobalSetting(string $id): GlobalSettingsStorageContract
	{

		$globalSetting = $this->globalSettings->getGlobalSetting($id);
		$value = Ninja_Forms()->get_setting($id);
		$globalSetting->setValue($value);
		$this->globalSettings->addGlobalSetting($globalSetting);

		return $this;
	}
}
