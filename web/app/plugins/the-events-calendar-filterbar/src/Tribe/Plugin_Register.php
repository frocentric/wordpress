<?php
/**
 * Class Tribe__Events__Filterbar__Plugin_Register
 */
class Tribe__Events__Filterbar__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class   = 'Tribe__Events__Filterbar__View';
	protected $dependencies = array(
		'parent-dependencies' => array(
			'Tribe__Events__Main'       => '5.3.1-dev',
		),
	);

	public function __construct() {
		$this->base_dir = TRIBE_EVENTS_FILTERBAR_FILE;
		$this->version  = Tribe__Events__Filterbar__View::VERSION;

		$this->register_plugin();
	}
}
