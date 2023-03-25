<?php

namespace NFMailchimp\EmailCRM\WpBridge;

use NFMailchimp\EmailCRM\WpBridge\Contracts\WpBridgeContract;
use NFMailchimp\EmailCRM\Shared\Contracts\Module;
use NFMailchimp\EmailCRM\Shared\Containers\ServiceContainer;

/**
 * Exposes the top-level API of the package
 */
class WpBridge extends ServiceContainer implements WpBridgeContract
{

	/**
	 * Unique identifier for package
	 */
	const IDENTIFIER = 'wp-bridge';

	/**
	 * @inheritDoc
	 */
	public function getIdentifier(): string
	{
		return self::IDENTIFIER;
	}


	/**
	 * Register the module's services
	 *
	 * @return Module
	 */
	public function registerServices(): Module
	{
		return $this;
	}
}
