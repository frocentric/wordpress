<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

use NFMailchimp\EmailCRM\Shared\Entities\GlobalSettings;

/**
 * Contract to store and retrieve  Global Settings
 */
interface GlobalSettingsStorageContract
{
	
	public function setGlobalSettings(GlobalSettings $globalSettings):GlobalSettingsStorageContract;

/**
 * Get GlobalSettings from this class, does NOT retrieve from storage location
 *
 * @return GlobalSettings
 */
	public function getGlobalSettings(): GlobalSettings;
/**
 * Store entire collection of GlobalSettings in a storage location
 *
 * @return \NFMailchimp\EmailCRM\AmoCrm\Contracts\GlobalSettingsStorageContract
 */
	public function storeGlobalSettings(): GlobalSettingsStorageContract;

/**
 * Retrieve entire collection of GlobalSettings from a storage location
 *
 * @return \NFMailchimp\EmailCRM\AmoCrm\Contracts\GlobalSettingsStorageContract
 */
	public function retrieveGlobalSettings(): GlobalSettingsStorageContract;

/**
 * Store a specified GlobalSetting in a storage location
 * @param string $id
 * @return \NFMailchimp\EmailCRM\AmoCrm\Contracts\GlobalSettingsStorageContract
 */
	public function storeGlobalSetting(string $id): GlobalSettingsStorageContract;

/**
 * Retrieve a global setting from a storage location
 *
 * @param string $id
 * @return \NFMailchimp\EmailCRM\AmoCrm\Contracts\GlobalSettingsStorageContract
 */
	public function retrieveGlobalSetting(string $id): GlobalSettingsStorageContract;
}
