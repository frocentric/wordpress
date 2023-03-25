<?php


namespace NFMailchimp\EmailCRM\Shared\Contracts;

interface ServiceFactoryContainerContract
{
	/**
	 * Add a factory to the container
	 *
	 * @param string $service Name of service factory
	 * @param ServiceFactoryContract $serviceFactory
	 * @return ServiceFactoryContainerContract
	 */
	public function addFactory(string $service, ServiceFactoryContract $serviceFactory) : ServiceFactoryContainerContract;

	/**
	 * Use a factory from the container
	 *
	 * @param string $service Name of service factory
	 * @param array $args
	 * @return mixed
	 */
	public function useFactory(string $service, $args = []);

	/**
	 * Does container contain factory?
	 *
	 * @param string $service Name of service factory
	 * @return bool
	 */
	public function hasFactory(string  $service): bool;
}
