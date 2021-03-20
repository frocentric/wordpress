<?php

namespace Tribe\Events\Pro\Integrations\Elementor\Controls\Traits;

trait TEC_Control {
	/**
	 * {@inheritDoc}
	 */
	public function get_type() {
		return static::get_slug();
	}

	/**
	 * Gets the control slug.
	 *
	 * @since 5.4.0
	 *
	 * @return string
	 */
	public static function get_slug() {
		return static::$slug;
	}
}