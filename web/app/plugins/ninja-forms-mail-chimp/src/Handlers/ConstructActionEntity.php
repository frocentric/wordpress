<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

// Integrating plugin
use NFMailchimp\NinjaForms\Mailchimp\NinjaFormsMailchimp;
// Mailchimp
use NFMailchimp\EmailCRM\Mailchimp\Factories\ConfigurationFactory;
// NF Bridge
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionEntity;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionSettings;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;

/**
 * Construct Action Entity for Subscribe to Mailchimp Newsletter action
 */
class ConstructActionEntity
{

	/**
	 *
	 * @var NinjaFormsMailchimp
	 */
	protected $nfMailchimp;

	/**
	 *
	 * @var ActionEntity
	 */
	protected $actionEntity;

	/**
	 *
	 * @var ApiSettings
	 */
	protected $apiSettings;

		/**
		 * Contruct class with integrating plugin instance
		 * @param NinjaFormsMailchimp $nfMailchimp
		 */
	public function __construct(NinjaFormsMailchimp $nfMailchimp)
	{
		$this->nfMailchimp = $nfMailchimp;
		$this->instantiateActionEntity();

		$this->addGlobalSettings();
		$this->addActionSettings();
	}

		/**
		 * Add global settings
		 */
	public function addGlobalSettings()
	{
		
		$apiSettings['id'] = 'mail_chimp';
		$apiSettings['label'] = 'Mailchimp';
		$apiSettings['apiSettings']  = [
				'ninja_forms_mc_api'=>[
					'id' => 'ninja_forms_mc_api',
					'label' => 'API Key',
					'expectedDataType' => 'userProvidedString'
				]
		];

		$this->actionEntity->setApiSettings(ApiSettings::fromArray($apiSettings));
	}

		/**
		 * Add action settings used in NF Action
		 */
	protected function addActionSettings()
	{
				$actionSettings = ActionSettings::fromArray(array(
				'double_opt_in' => array(
					'name' => 'double_opt_in',
					'type' => 'toggle',
					'label' => __('Require subscribers to confirm their subscription', 'ninja-forms-mail-chimp'),
					'group' => 'advanced',
					'width' => 'full',
					'value' => 0
				)
				));
		$this->actionEntity->setActionSettings($actionSettings);
	}
		/**
		 * Initialize an ActionEntity with standard values
		 */
	protected function instantiateActionEntity()
	{
		$array = [
			'name' => 'mailchimp', // Must match existing Mailchimp plugin action name
			'nicename' => 'Mailchimp',
			'tags' => ['newsletter'],
			'timing' => 'normal',
			'priority' => 10
		];

		$this->actionEntity = ActionEntity::fromArray($array);
	}

		/**
		 * Get the constructed Action Entity
		 * @return ActionEntity
		 */
	public function getActionEntity(): ActionEntity
	{

		return $this->actionEntity;
	}
}
