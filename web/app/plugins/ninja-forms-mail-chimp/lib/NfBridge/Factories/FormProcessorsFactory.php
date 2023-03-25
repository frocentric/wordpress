<?php

namespace NFMailchimp\EmailCRM\NfBridge\Factories;

use NFMailchimp\EmailCRM\NfBridge\Contracts\FormProcessorsFactoryContract;
use NFMailchimp\EmailCRM\Shared\Contracts\WPContract;
use NFMailchimp\EmailCRM\Shared\Contracts\FormActionFieldCollection;
use NFMailchimp\EmailCRM\Shared\Contracts\SubmissionDataContract;
use NFMailchimp\EmailCRM\Shared\Contracts\FormContract;
use NFMailchimp\EmailCRM\NfBridge\Actions\ConfigureApiSettings;
use NFMailchimp\EmailCRM\NfBridge\Actions\GetApiSettingsValues;
use NFMailchimp\EmailCRM\NfBridge\Entities\SubmissionData;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;
use NFMailchimp\EmailCRM\NfBridge\Entities\Form;
use NF_Abstracts_ModelFactory;
use NFMailchimp\EmailCRM\WpBridge\Contracts\WpHooksContract;
use NFMailchimp\EmailCRM\WpBridge\WpHooksApi;

/**
 * Provides classes for NF form processing
 */
class FormProcessorsFactory implements FormProcessorsFactoryContract
{

	/**
	 * WordPress hooks
	 *
	 * @var WpHooksContract
	 */
	protected $wpHooks;

	/**
	 *
	 * @param WpHooksApi $wpHooks
	 */
	public function __construct(WpHooksContract $wpHooks)
	{
		$this->wpHooks = $wpHooks;
	}

	/**
	 * @inheritdoc
	 */
	public function getWpHooks(): WpHooksContract
	{
		return $this->wpHooks;
	}

	/** @inheritDoc */
	public function getSubmissionData(
		array $formActionSubmissionArray,
		FormActionFieldCollection $actionSettings,
		ApiSettings $apiSettings
	): SubmissionDataContract {

		$getApiSettingsValues = $this->getGetApiSettingsValues($apiSettings);
		$keyValuePairs = $getApiSettingsValues->getApiSettingsValues();
		$combinedKeyValuePairs = array_merge($formActionSubmissionArray, $keyValuePairs);
		$submissionData = new SubmissionData($combinedKeyValuePairs, $actionSettings);

		return $submissionData;
	}

	/** @inheritDoc */
	public function getConfigureApiSettings(ApiSettings $apiSettings): ConfigureApiSettings
	{

		return new ConfigureApiSettings($apiSettings);
	}

	/** @inheritdoc */
	public function getForm(NF_Abstracts_ModelFactory $nfFormModelFactory): FormContract
	{
			$settings = $nfFormModelFactory->get_settings();
		   
			$array=[
				'name'=>$settings['title'],
				'id'=>$nfFormModelFactory->get_id()
			];
			
			return new Form($array);
	}


	/** @inheritdoc */
	public function getGetApiSettingsValues(ApiSettings $apiSettings): GetApiSettingsValues
	{

		return new GetApiSettingsValues($apiSettings);
	}
}
