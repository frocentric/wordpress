<?php


namespace NFMailchimp\EmailCRM\WpBridge\Contracts;

/**
 * Interface WpHooksContract
 *
 * Describes the WordPress plugins API or similar
 */
interface WpHooksContract
{

	/**
	 * @param string $filterName
	 * @param mixed $incoming
	 * @param int $priority
	 * @param int $acceptedArgs
	 * @return mixed
	 */
	public function addFilter(string $filterName, $incoming, $priority = 10, $acceptedArgs = 1);

	/**
	 * @param string $filterName
	 * @param mixed $incoming
	 * @return mixed
	 */
	public function applyFilters(string $filterName, $incoming);

	/**
	 *
	 * @param string $tag
	 * @param array|string|callable $hook
	 */
	public function addAction(string $tag, $hook);
}
