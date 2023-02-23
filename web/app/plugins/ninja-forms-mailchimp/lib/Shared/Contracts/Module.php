<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Contract that the main class of all modules/ packages implement
 */
interface Module
{
	/**
	 * Register all services
	 *
	 * @return Module
	 */
	public function registerServices(): Module;

	/**
	 * Get a module's identifier
	 *
	 * @return string
	 */
	public function getIdentifier(): string;
}
