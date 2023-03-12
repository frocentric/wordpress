<?php

namespace NFMailchimp\EmailCRM\Shared\Contracts;

/**
 * Used to replace WP method with standard PHP methods for testing
 *
 */
interface WPContract
{

	/**
	 * Apply filters to given property
	 * @param string $tag
	 * @param mixed $incoming
	 * @param int $priority
	 * @param int $acceptedArgs
	 */
	public function applyFilters(string $tag, $incoming, $priority = 10, $acceptedArgs = 1);

	/**
	 * Add filter to given tag
	 * @param string $tag
	 * @param type $hook
	 */
	public function addFilter(string $tag, $hook);

	/**
	 *
	 * @param string $tag
	 * @param type $hook
	 */
	public function addAction(string $tag, $hook);

	/**
	 * Wrap update_option
	 * @param string $key
	 * @param type $data
	 */
	public function updateOption(string $key, $data);
}
