<?php

namespace NFMailchimp\EmailCRM\NfBridge;

use NFMailchimp\EmailCRM\NfBridge\Contracts\NfBridgeContract;
use NFMailchimp\EmailCRM\Shared\Containers\ServiceContainer;
use NFMailchimp\EmailCRM\Shared\Contracts\Module;

use NFMailchimp\EmailCRM\NfBridge\Factories\FormFactory;
use NFMailchimp\EmailCRM\NfBridge\Factories\FormFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Factories\NfActionFactory;
use NFMailchimp\EmailCRM\NfBridge\Factories\NfActionFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Factories\SubmissionDataFactory;
use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Contracts\FormProcessorsFactoryContract;
use NFMailchimp\EmailCRM\NfBridge\Factories\FormProcessorsFactory;
use NFMailchimp\EmailCRM\NfBridge\Actions\RegisterAction;
use NFMailchimp\EmailCRM\NfBridge\Actions\CreateAddNewModal;
use NFMailchimp\EmailCRM\Shared\Contracts\ServiceContainerContract;
use NFMailchimp\EmailCRM\WpBridge\WpHooksApi;
use NFMailchimp\EmailCRM\WpBridge\Contracts\WpHooksContract;

/**
 * Exposes the top-level API of the package
 */
class NfBridge extends ServiceContainer implements NfBridgeContract
{

	/**
	 * Unique identifier for package
	 */
	const IDENTIFIER = 'nf-bridge';

		/**
		 *
		 * @var WpHooksContract
		 */
		protected $wpHooks;

		/**
		 * Set WpHooks to provide WP action hooks and filters
		 * @param WpHooksContract $wpHooks
		 */
	public function setWpHooks(WpHooksContract $wpHooks)
	{
		$this->wpHooks = $wpHooks;
	}

	/**
	 * @inheritDoc
	 */
	public function getIdentifier(): string
	{
		return self::IDENTIFIER;
	}

	/** @inheritDoc */
	public function getContainer(): ServiceContainerContract
	{
		return $this;
	}

	/**
	 * Register the module's services
	 *
	 * @return Module
	 */
	public function registerServices(): Module
	{
				// Bind FormFactory service to container
		$this->bind(FormFactoryContract::class, function () {
			return new FormFactory();
		});

		// Bind NFActionFactory to the container
		$this->bind(NfActionFactoryContract::class, function () {
			return new NfActionFactory();
		});

				// Bind SubmissionDataFactory to the container
				$this->bind(SubmissionDataFactoryContract::class, function () {

					return new SubmissionDataFactory();
				});

				// Bind FormProcessorsFactory to the container
				$this->bind(FormProcessorsFactoryContract::class, function () {
					if (is_null($this->wpHooks)) {
						$this->wpHooks= new WpHooksApi();
					}
					return new FormProcessorsFactory($this->wpHooks);
				});

				// Bind RegisterAction to the container
				$this->bind(RegisterAction::class, function () {
					if (is_null($this->wpHooks)) {
						$this->wpHooks= new WpHooksApi();
					}
					return new RegisterAction($this->wpHooks);
				});

								// Bind CreateAddNewModal to the container
				$this->bind('CreateAddNewModal', function () {

					return new CreateAddNewModal();
				});
			return $this;
	}
}
