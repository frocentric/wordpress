<?php
/**
 * Class Tribe__Events__Pro__Plugin_Register
 */
class  Tribe__Events__Pro__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class   = 'Tribe__Events__Pro__Main';
	protected $dependencies = array(
		'parent-dependencies' => array(
			'Tribe__Events__Main'       => '5.1.0-dev',
		),
	);

	public function __construct() {
		$this->base_dir = EVENTS_CALENDAR_PRO_FILE;
		$this->version  = Tribe__Events__Pro__Main::VERSION;

		$this->register_plugin();
	}
}