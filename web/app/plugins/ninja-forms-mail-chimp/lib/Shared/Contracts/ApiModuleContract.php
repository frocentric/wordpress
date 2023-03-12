<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

// Shared
use NFMailchimp\EmailCRM\Shared\Contracts\Module;
use NFMailchimp\EmailCRM\Shared\Entities\GlobalSettings;
use NFMailchimp\EmailCRM\Shared\Entities\FormFields;

/**
 * Defines requirements of all ApiModules
 */
interface ApiModuleContract extends Module
{

	/**
	 * Return configured GlobalSettings for the ApiModule
	 *
	 * By definition as a 'configured' entity, it does not include any stored
	 * values and is standardized across all integrations and implementations
	 *
	 * @return GlobalSettings
	 */
	public function getGlobalSettings(): GlobalSettings;
	/**
	 * Return configured standard API FormFields for the ApiModule
	 *
	 * By definition as a 'configured' entity, it does not include any
	 *  custom values and is standardized across all integrations and implementations
	 *
	 * @return FormFields
	 */
	public function getFormFields(): FormFields;
}
