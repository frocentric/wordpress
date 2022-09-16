<?php


namespace NFMailchimp\EmailCRM\WpBridge;

use NFMailchimp\EmailCRM\WpBridge\Contracts\WpHooksContract;

class WpHooksApi implements WpHooksContract
{
	/**
	 * @inheritDoc
	 */
	public function applyFilters(string $filterName, $incoming, ...$args)
	{

		if (function_exists('apply_filters')) {
			return apply_filters($filterName, $incoming, $args);
		}

		return $incoming;
	}

	/**
	 * @inheritDoc
	 */
	public function addFilter(string $filterName, $callback, $priority = 10, $accepted_args = 1)
	{

		if (function_exists('add_filter')) {
			add_filter($filterName, $callback, $priority, $accepted_args);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function addAction(string $tag, $hook)
	{

		if (function_exists('add_action')) {
			add_action($tag, $hook);
		}
	}
}
