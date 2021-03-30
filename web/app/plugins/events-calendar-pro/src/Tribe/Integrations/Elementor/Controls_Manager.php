<?php
namespace Tribe\Events\Pro\Integrations\Elementor;

use Elementor\Group_Control_Base;
use Elementor\Plugin as Elementor_Plugin;

/**
 * Class Controls_Manager
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */
class Controls_Manager extends Manager_Abstract {
	/**
	 * {@inheritdoc}
	 */
	protected $type = 'controls';

	/**
	 * Constructor
	 *
	 * @since 5.4.0
	 */
	public function __construct() {
		$this->objects = [
			Controls\Groups\Event_Query::get_type() => Controls\Groups\Event_Query::class,
		];
	}

	/**
	 * Registers the controls with Elementor.
	 *
	 * @since 5.4.0
	 */
	public function register() {
		$objects = $this->get_registered_objects();

		$controls_manager = Elementor_Plugin::instance()->controls_manager;

		foreach ( $objects as $slug => $object_class ) {
			$control = tribe( $object_class );
			if ( $control instanceof Group_Control_Base ) {
				$controls_manager->add_group_control( $control->get_type(), $control );
			} else {
				$controls_manager->register_control( $control->get_type(), $control );
			}
		}
	}
}