<?php
/**
 * Class Tribe__Events__Community__Plugin_Register
 *
 * @since 4.6
 */
class  Tribe__Events__Community__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class   = 'Tribe__Events__Community__Main';
	protected $dependencies = [
		'parent-dependencies' => [
			'Tribe__Events__Main' => '5.1.0-dev',
		],
	];

	/**
	 * Constructor method.
	 *
	 * @since 4.6
	 */
	public function __construct() {
		$this->base_dir = EVENTS_COMMUNITY_FILE;
		$this->version  = Tribe__Events__Community__Main::VERSION;

		$this->register_plugin();
	}
}
