<?php
namespace Tribe\Events\Pro\Integrations\Elementor;

use Elementor\Plugin as Elementor_Plugin;

/**
 * Class Manager_Abstract
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */
abstract class Manager_Abstract {
	/**
	 * @var string Type of object.
	 */
	protected $type;

	/**
	 * @var array Collection of objects to register.
	 */
	protected $objects;

	/**
	 * Returns an associative array of objects to be registered.
	 *
	 * @since  5.4.0
	 *
	 * @return array An array in the shape `[ <slug> => <class> ]`.
	 */
	public function get_registered_objects() {
		/**
		 * Filters the list of objects available and registered.
		 *
		 * Both classes and built objects can be associated with a slug; if bound in the container the classes
		 * will be built according to the binding rules; objects will be returned as they are.
		 *
		 * @since 5.4.0
		 *
		 * @param array $widgets An associative array of objects in the shape `[ <slug> => <class> ]`.
		 */
		return (array) apply_filters( "tribe_events_pro_elementor_registered_{$this->type}", $this->objects );
	}

	/**
	 * Registers the objects with Elementor.
	 *
	 * @since 5.4.0
	 */
	abstract public function register();
}
