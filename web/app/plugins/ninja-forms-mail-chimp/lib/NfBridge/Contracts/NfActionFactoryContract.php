<?php

namespace NFMailchimp\EmailCRM\NfBridge\Contracts;

use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\FormProcessorsFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionProcessHandlerContract;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionEntity;
use NFMailchimp\EmailCRM\WpBridge\WpHooksApi;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NewsletterExtensionContract;

/**
 * Contract for creating an NF Action
 */
interface NfActionFactoryContract
{

	/**
	 * Create NfActionContract from our ActionEntity
	 *
	 * @param ActionEntity $actionEntity
	 * @param NfActionProcessHandlerContract $processHandler
	 * @param FormProcessorsFactoryContract $formProcessorsFactory
	 * @param WpHooksApi $wordpress
	 * @return NfActionContract
	 */
	public function constructNinjaFormsAction(
		ActionEntity $actionEntity,
		NfActionProcessHandlerContract $processHandler,
		FormProcessorsFactoryContract $formProcessorsFactory,
		WpHooksApi $wordpress
	): NfActionContract;

	/**
	 * Create Newsletter-extended NfActionContract from our ActionEntity
	 *
	 * @param ActionEntity $actionEntity
	 * @param NfActionProcessHandlerContract $processHandler
	 * @param FormProcessorsFactoryContract $formProcessorsFactory
	 * @param WpHooksApi $wordpress
	 * @param NewsletterExtensionContract $newsletterExtension
	 * @return NfActionContract
	 */
	public function constructNinjaFormsNewsletterAction(
		ActionEntity $actionEntity,
		NfActionProcessHandlerContract $processHandler,
		FormProcessorsFactoryContract $formProcessorsFactory,
		WpHooksApi $wordpress,
		NewsletterExtensionContract $newsletterExtension
	): NfActionContract;
}
