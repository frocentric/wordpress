<?php

namespace NFMailchimp\EmailCRM\NfBridge\Factories;

use NFMailchimp\EmailCRM\NfBridge\Actions\ConstructActionSetting;
use NFMailchimp\EmailCRM\NfBridge\Actions\ConstructActionSettings;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ConstructEntityFactoryContract;

/**
 * Factory to provide actions that construct or configure entities
 *
 */
class ConstructEntityFactory implements ConstructEntityFactoryContract
{

	/**
	 *
	 * @return ConstructActionSetting
	 */
	public function constructActionSetting()
	{
		return new ConstructActionSetting();
	}

	/**
	 *
	 * @return ConstructActionSettings
	 */
	public function constructActionSettings()
	{
		return new ConstructActionSettings();
	}
}
