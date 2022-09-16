<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

/**
 * Contract for a ConstructEntityFactory
 */
interface ConstructEntityFactoryContract
{

	/**
	 * Provide an object to construct action setting
	 */
	public function constructActionSetting();

	/**
	 * Provide an object to construct action settings
	 */
	public function constructActionSettings();
}
