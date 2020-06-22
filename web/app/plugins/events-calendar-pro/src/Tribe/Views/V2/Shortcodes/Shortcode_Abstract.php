<?php
/**
 * The interface all shortcodes should implement.
 *
 * @deprecated 5.1.1
 *
 * @since   4.7.5
 *
 * @package Tribe\Events\Pro\Views\V2\Shortcodes
 */
namespace Tribe\Events\Pro\Views\V2\Shortcodes;

use Tribe__Utils__Array as Arr;

/**
 * Interface Shortcode_Interface
 *
 * @since   4.7.5
 *
 * @deprecated  5.1.1  Use \Tribe\Shortcode\Shortcode_Abstract
 *
 * @package Tribe\Events\Pro\Views\V2\Shortcodes
 */
abstract class Shortcode_Abstract extends \Tribe\Shortcode\Shortcode_Abstract {
	/**
	 * Legacy PRO method for getting the validated arguments callback map.
	 *
	 * @since  4.7.5
	 *
	 * @deprecated 5.1.1
	 *
	 * @return array<string,mixed> A map of the shortcode arguments that have survived validation.
	 */
	public function get_validate_arguments_map() {
		return $this->get_validated_arguments_map();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_validated_arguments_map() {
		$validate_arguments_map = parent::get_validated_arguments_map();

		/**
		 * Applies a filter to instance arguments validation callbacks.
		 *
		 * @since  4.7.5
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  array  $validate_arguments_map   Current set of callbacks for arguments.
		 * @param  static $instance                 Which instance of shortcode we are dealing with.
		 */
		$validate_arguments_map = apply_filters_deprecated(
			'tribe_events_pro_shortcode_validate_arguments_map',
			[ $validate_arguments_map, $this ],
			'TBD',
			'tribe_shortcode_validate_arguments_map'
		);

		$registration_slug = $this->get_registration_slug();

		/**
		 * Applies a filter to instance arguments validation callbacks based on the registration slug of the shortcode.
		 *
		 * @since  4.7.5
		 * @deprecated 5.1.1
		 *
		 * @param  array  $validate_arguments_map   Current set of callbacks for arguments.
		 * @param  static $instance                 Which instance of shortcode we are dealing with.
		 */
		$validate_arguments_map = apply_filters_deprecated(
			"tribe_events_pro_shortcode_{$registration_slug}_validate_arguments_map",
			[ $validate_arguments_map, $this ],
			'TBD',
			"tribe_shortcode_{$registration_slug}_validate_arguments_map"
		);

		return $validate_arguments_map;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_arguments() {
		$arguments = parent::get_arguments();

		/**
		 * Applies a filter to instance arguments.
		 *
		 * @since  4.7.5
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  array  $arguments  Current set of arguments.
		 * @param  static $instance   Which instance of shortcode we are dealing with.
		 */
		$arguments = apply_filters_deprecated(
			'tribe_events_pro_shortcode_arguments',
			[ $arguments, $this ],
			'TBD',
			"tribe_shortcode_arguments"
		);

		$registration_slug = $this->get_registration_slug();

		/**
		 * Applies a filter to instance arguments based on the registration slug of the shortcode.
		 *
		 * @since  4.7.5
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  array  $arguments   Current set of arguments.
		 * @param  static $instance    Which instance of shortcode we are dealing with.
		 */
		$arguments = apply_filters_deprecated(
			"tribe_events_pro_shortcode_{$registration_slug}_arguments",
			[ $arguments, $this ],
			'TBD',
			"tribe_shortcode_{$registration_slug}_arguments"
		);

		return $arguments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_argument( $index, $default = null ) {
		$registration_slug = $this->get_registration_slug();
		$parent_value = parent::get_argument( $index, $default );

		// Check for backwards compatibility.
		if (
			! has_filter( 'tribe_events_pro_shortcode_argument' )
			&& has_filter( "tribe_events_pro_shortcode_{$registration_slug}_argument" )
		) {
			return $parent_value;
		}

		/**
		 * Applies a filter to a specific shortcode argument, catch all for all shortcodes..
		 *
		 * @since  4.7.5
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  mixed  $argument   The argument.
		 * @param  array  $index      Which index we indent to fetch from the arguments.
		 * @param  array  $default    Default value if it doesnt exist.
		 * @param  static $instance   Which instance of shortcode we are dealing with.
		 */
		$argument = apply_filters_deprecated(
			'tribe_events_pro_shortcode_argument',
			[ $parent_value, $index, $default, $this ],
			'TBD',
			"tribe_shortcode_argument"
		);

		/**
		 * Applies a filter to a specific shortcode argument, to a particular registration slug.
		 *
		 * @since  4.7.5
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  mixed  $argument   The argument value.
		 * @param  array  $index      Which index we indent to fetch from the arguments.
		 * @param  array  $default    Default value if it doesnt exist.
		 * @param  static $instance   Which instance of shortcode we are dealing with.
		 */
		$argument = apply_filters_deprecated(
			"tribe_events_pro_shortcode_{$registration_slug}_argument",
			[$argument, $index, $default, $this ],
			'TBD',
			"tribe_shortcode_{$registration_slug}_argument"
		);

		return $argument;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_default_arguments() {
		$default_arguments = parent::get_default_arguments();

		/**
		 * Applies a filter to instance default arguments.
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  array  $default_arguments  Current set of default arguments.
		 * @param  static $instance           Which instance of shortcode we are dealing with.
		 */
		$default_arguments = apply_filters_deprecated(
			'tribe_events_pro_shortcode_default_arguments',
			[ $default_arguments, $this ],
			'TBD',
			'tribe_shortcode_default_arguments'
		);

		$registration_slug = $this->get_registration_slug();

		/**
		 * Applies a filter to instance default arguments based on the registration slug of the shortcode.
		 *
		 * @deprecated 5.1.1
		 *
		 * @param  array  $default_arguments   Current set of default arguments.
		 * @param  static $instance            Which instance of shortcode we are dealing with.
		 */
		$default_arguments = apply_filters_deprecated(
			"tribe_events_pro_shortcode_{$registration_slug}_default_arguments",
			[ $default_arguments, $this ],
			'TBD',
			"tribe_shortcode_{$registration_slug}_default_arguments"
		);

		return $default_arguments;
	}

}
