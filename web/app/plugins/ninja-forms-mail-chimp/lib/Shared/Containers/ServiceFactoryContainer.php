<?php


namespace NFMailchimp\EmailCRM\Shared\Containers;

use NFMailchimp\EmailCRM\Shared\Contracts\ServiceFactoryContainerContract;
use NFMailchimp\EmailCRM\Shared\Contracts\ServiceFactoryContract;

/**
 * Service factory container
 *
 * This container can be used to group related factories
 *
 * SEE: docs/container.md
 */
class ServiceFactoryContainer implements ServiceFactoryContainerContract
{

	/**
	 * Factories
	 *
	 * @var array
	 */
	protected $factories;

	/** @inheritDoc */
	public function addFactory(string $service, ServiceFactoryContract $serviceFactory) : ServiceFactoryContainerContract
	{
			$this->factories[$service] = $serviceFactory;
			return  $this;
	}

	/** @inheritDoc */
	public function useFactory(string $service, $args = [])
	{
		if (! $this->hasFactory($service)) {
			throw new \Exception("Service {$service} not found", 500);
		}

		return call_user_func(
			[
				$this->factories[$service],
				'handle'
				],
			$args,
			$this
		);
	}

	/** @inheritDoc */
	public function hasFactory(string $service): bool
	{
		return  is_array($this->factories) && array_key_exists($service, $this->factories);
	}
}
