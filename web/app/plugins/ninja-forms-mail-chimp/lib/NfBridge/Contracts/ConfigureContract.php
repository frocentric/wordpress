<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

use NFMailchimp\EmailCRM\NfBridge\Entities\Modal;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionEntity;

/**
 * Contract specifying required configurations for all NF Integrating Plugins
 */
interface ConfigureContract
{
	/**
	 * Provide a Modal entity for Autogenerating a form from the Add New menu
	 *
	 * @return Modal
	 */
	public function autogenerateModalMarkup(): Modal;
	
	/**
	 * Provide ActionEntity defining the primary action of the integrating plugin
	 *
	 * @return ActionEntity
	 */
	public function actionEntity():ActionEntity;
}
