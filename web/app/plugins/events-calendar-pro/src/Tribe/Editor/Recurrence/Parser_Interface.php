<?php

/**
 * Interface Tribe__Events__Pro__Editor__Recurrence__Parser_Interface
 *
 * @since 4.5
 */
interface Tribe__Events__Pro__Editor__Recurrence__Parser_Interface {

	/**
	 * Tribe__Events__Pro__Editor__Recurrence__Parser_Interface constructor.
	 *
	 * @param array $fields
	 */
	public function __construct( $fields = array() );

	/**
	 * Method used to setup the class
	 *
	 * @since 4.5
	 *
	 * @return boolean
	 */
	public function parse();

	/**
	 *
	 *
	 * @since 4.5
	 *
	 * @param string $type
	 *
	 * @return boolean
	 */
	public function set_type( $type = '' );

	/**
	 * Return the parsed data
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function get_parsed();
}
