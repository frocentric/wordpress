<?php
/**
 * Provides methods for fetching categories for use in Elementor.
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Traits
 */

namespace Tribe\Events\Pro\Integrations\Elementor\Traits;

use Tribe__Cache as Cache;

/**
 * Trait Categories
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Controls\Traits
 */
trait Categories {
	/**
	 * Adds an event category control.
	 *
	 * @since 5.4.0
	 *
	 * @return array
	 */
	protected function get_event_categories() {
		/** @var Cache $cache */
		$cache            = tribe( 'cache' );
		$cache_key        = 'tec_elementor_categories';
		$category_objects = $cache->get( $cache_key, 'save_post' );

		if ( false === $category_objects ) {
			$category_objects = get_terms( [
				'taxonomy' => tribe( 'tec.main' )->get_event_taxonomy(),
			] );

			if ( is_array( $category_objects ) ) {
				$cache->set( $cache_key, $category_objects, Cache::NON_PERSISTENT, 'save_post' );
			}
		}

		$categories = [];
		foreach ( $category_objects as $category ) {
			$categories[ $category->slug ] = $category->name;
		}

		return $categories;
	}
}
