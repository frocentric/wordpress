<?php

namespace NFMailchimp\NinjaForms\Mailchimp;

use NFMailchimp\NinjaForms\Mailchimp\Contracts\NinjaFormsMailchimpContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfBridgeContract;
use NFMailchimp\EmailCRM\Mailchimp\Interfaces\MailchimpApi;

use NFMailchimp\NinjaForms\Mailchimp\Handlers\CreateAutogenerateModal;

use NFMailchimp\NinjaForms\Mailchimp\Handlers\AutogenerateForm;

use NFMailchimp\NinjaForms\Mailchimp\Handlers\MailchimpOptIn;
use NFMailchimp\NinjaForms\Mailchimp\Endpoints\AutogenerateFormEndpoint;
use NFMailchimp\NinjaForms\Mailchimp\Handlers\OutputResponseDataMetabox;

use NFMailchimp\EmailCRM\WpBridge\RestApi\CreateWordPressEndpoints;

use NFMailchimp\EmailCRM\WpBridge\RestApi\AuthorizeRequestWithWordPressUser;

use NFMailchimp\NinjaForms\Mailchimp\Filters\NinjaFormsPluginSettings;
use NFMailchimp\NinjaForms\Mailchimp\Filters\NinjaFormsPluginSettingsGroups;
use NFMailchimp\NinjaForms\Mailchimp\Hooks\NinjaFormsRegisterActions;

use NFMailchimp\NinjaForms\Mailchimp\Handlers\GetNfStructuredLists;

/**
 * Exposes the top-level API of the package
 */
class NinjaFormsMailchimp implements NinjaFormsMailchimpContract
{
	const VERSION = '3.3.2';
	const SLUG = 'mail-chimp';
	const NAME = 'MailChimp';
	const AUTHOR = 'The WP Ninjas';
	const PREFIX = 'NF_MailChimp';
	/**
	 * Unique identifier for package
	 */
	const IDENTIFIER = 'nf_mailchimp';

	/**
	 * REST Route for endpoints
	 */
	const RESTROUTE = 'nf-mailchimp/v2';

	/** @var MailchimpApi */
	protected $mailchimpApi;


	/**
	 *
	 * @var NfBridgeContract
	 */
	protected $nfBridge;



	/** @inheritDoc */
	public function setNfBridge(NfBridgeContract $nfBridge): NinjaFormsMailchimpContract
	{
		$this->nfBridge = $nfBridge;
		return $this;
	}

	/** @inheritDoc */
	public function getNfBridge(): NfBridgeContract
	{
		return $this->nfBridge;
	}

	/** @inheritDoc */
	public function getIdentifier(): string
	{
		return self::IDENTIFIER;
	}

	/**
	 * Initialize the REST API endpoints
	 *
	 * @since 4.0.0
	 *
	 * @uses "rest_api_init" hook.
	 */
	public function initApi(): void
	{
		$api = new CreateWordPressEndpoints('register_rest_route', self::RESTROUTE);


		//Authorization for all REST API endpoints
		$authorizer = new AuthorizeRequestWithWordPressUser('manage_options');

		//Get Autogenerate Form Endpoint
		// AutogenerateFormEndpoint triggers form building and is not cached for that reason
		$endpoint = new AutogenerateFormEndpoint();
		$autogenerateForm = new AutogenerateForm($this->mailchimpApi);
		$endpoint->setAutogenerateForm($autogenerateForm);
		$endpoint->addAuthorizer($authorizer);
		$api->registerRouteWithWordPress(
			$endpoint
		);
	}

	/**
	 * Register Mailchimp Opt In Field
	 *
	 * Carryover from NF Mailchimp 3.0 version
	 *
	 * @param array $actions
	 * @return array $actions
	 */
	public function registerOptIn($actions)
	{
		$actions['mailchimp-optin'] = new MailchimpOptIn();

		return $actions;
	}

	/**
	 * Setup Admin
	 *
	 * Setup admin classes for Ninja Forms and WordPress.
	 */
	public function setupAdmin()
	{
		new OutputResponseDataMetabox();
	}


	/**
	 * Register the Subscribe action wtih Ninja Forms
	 */
	public function addSubscribeAction()
	{
		$apiKey = \Ninja_Forms()->get_setting('ninja_forms_mc_api', '');

		$this->mailchimpApi->setApiKey($apiKey);

		(new NinjaFormsRegisterActions())
			->setMailchimpApi($this->mailchimpApi)
			->registerHooks();
			
		(new NinjaFormsPluginSettingsGroups())->addFilter();
		(new NinjaFormsPluginSettings())->addFilter();

	}

	/**
	 * Craates modal with Add New form autogeneration buttons
	 * @param array $templates
	 * @return array
	 */
	public function registerAutogenerateModal($templates)
	{
		$lists = (new GetNfStructuredLists($this->mailchimpApi))->getLists();

		if (!empty($lists)) {
			$modal = (new CreateAutogenerateModal())->handle($lists);

			$templates[$modal->getId()] = $modal->toArray();
		}

		// Remove the Mailchimp ad if present
		if (isset($templates['mailchimp-signup'])) {
			unset($templates['mailchimp-signup']);
		}

		return $templates;
	}

	/**
	 * Set the value of mailchimpApi
	 *
	 * @return  NinjaFormsMailchimp
	 */
	public function setMailchimpApi($mailchimpApi): NinjaFormsMailchimp
	{
		$this->mailchimpApi = $mailchimpApi;
		return $this;
	}
}
