<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

use NFMailchimp\EmailCRM\Shared\Contracts\FormActionFieldCollection;
use NFMailchimp\EmailCRM\Shared\Contracts\SubmissionDataContract;
use NFMailchimp\EmailCRM\Shared\Contracts\FormContract;
use NFMailchimp\EmailCRM\NfBridge\Actions\ConfigureApiSettings;
use NFMailchimp\EmailCRM\NfBridge\Actions\getApiSettingsValues;
use NFMailchimp\EmailCRM\NfBridge\Entities\ApiSettings;
use NFMailchimp\EmailCRM\WpBridge\Contracts\WpHooksContract;
use NF_Abstracts_ModelFactory;

/**
 * Contract for a NF Form Processors Contract
 */
interface FormProcessorsFactoryContract
{

	/**
	 * Return a WPContract object
	 *
	 * Provide access to Wordpress methods or mocked version
	 * @return WPContract
	 */
	public function getWpHooks(): WpHooksContract;

	/**
	 * Create submission data from a NF form submission
		 *
		 * @param array $formActionSubmissionArray
		 * @param FormActionFieldCollection $actionSettings
		 * @param ApiSettings $apiSettings
		 * @return SubmissionDataContract
		 */
	public function getSubmissionData(
		array $formActionSubmissionArray,
		FormActionFieldCollection $actionSettings,
		ApiSettings $apiSettings
	): SubmissionDataContract;

	/**
	 * Construct NF settings configuration from ApiSettings
	 * @param ApiSettings $apiSettings
	 */
	public function getConfigureApiSettings(ApiSettings $apiSettings): ConfigureApiSettings;

	/**
	 * Return a form contract from a given NF form model factory
	 * @param NF_Abstracts_ModelFactory $nfFormModelFactory
	 * @return FormContract
	 */
	public function getForm(NF_Abstracts_ModelFactory $nfFormModelFactory): FormContract;


	/**
	 *
	 * @param ApiSettings $apiSettings
	 * @return getApiSettingsValues
	 */
	public function getGetApiSettingsValues(ApiSettings $apiSettings): getApiSettingsValues;
}
