<?php


namespace NFMailchimp\NinjaForms\Mailchimp\Contracts;

// Mailchimp
use NFMailchimp\EmailCRM\Mailchimp\Contracts\MailchimpContract;
// NF Bridge
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfBridgeContract;

/**
 * Contract that describes the top-level API of the package
 */
interface NinjaFormsMailchimpContract
{

	/**
	 * Initialize the REST API endpoints
	 *
	 * @since 2.0.0
	 *
	 * @uses "rest_api_init" hook.
	 */
	public function initApi(): void;

	/**
		 * Set the NfBridge
	 * @param NfBridgeContract $nfBridge
	 * @return NinjaFormsMailchimpContract
	 */
	public function setNfBridge(NfBridgeContract $nfBridge) : NinjaFormsMailchimpContract;

	/**
		 * Get the NfBridge
	 * @return NfBridgeContract
	 */
	public function getNfBridge() : NfBridgeContract;
}
