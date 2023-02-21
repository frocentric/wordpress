<?php

namespace NFMailchimp\EmailCRM\NfBridge\Factories;

use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\FormProcessorsFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NfActionProcessHandlerContract;
use NFMailchimp\EmailCRM\NfBridge\Entities\ActionEntity;
use NFMailchimp\EmailCRM\NfBridge\Actions\NfAction;
use NFMailchimp\EmailCRM\NfBridge\Actions\NfNewsletterAction;
use NFMailchimp\EmailCRM\WpBridge\WpHooksApi;
use NFMailchimp\EmailCRM\NfBridge\Contracts\NewsletterExtensionContract;

/**
 * Factory for creating an NF Action
 */
class NfActionFactory implements NfActionFactoryContract
{

	/** @inheritdoc */
	public function constructNinjaFormsAction(
		ActionEntity $actionEntity,
		NfActionProcessHandlerContract $processHandler,
		FormProcessorsFactoryContract $formProcessorsFactory,
		WpHooksApi $wordpress
	): NfActionContract {

		$action = new NfAction($actionEntity, $processHandler, $formProcessorsFactory, $wordpress);

		return $action;
	}
		
			/** @inheritdoc */
	public function constructNinjaFormsNewsletterAction(
		ActionEntity $actionEntity,
		NfActionProcessHandlerContract $processHandler,
		FormProcessorsFactoryContract $formProcessorsFactory,
		WpHooksApi $wordpress,
		NewsletterExtensionContract $newsletterExtension
	): NfActionContract {

		$action = new NfNewsletterAction($actionEntity, $processHandler, $formProcessorsFactory, $wordpress, $newsletterExtension);

		return $action;
	}
}
