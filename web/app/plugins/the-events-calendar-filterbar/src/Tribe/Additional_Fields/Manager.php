<?php
/**
 * Generates additional field filters if appropriate.
 */
class Tribe__Events__Filterbar__Additional_Fields__Manager {
	/**
	 * Container for any additional field filters that have been created.
	 *
	 * @var array
	 */
	public static $filters = array();

	/**
	 * Sets up additional field filters, if possible.
	 */
	public static function init() {
		// If PRO is not active we cannot support additional fields
		if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
			return;
		}

		// Register our additional fields
		self::register_filters();
	}

	/**
	 * Registers additional field filters unless the additional field data is not ready/still
	 * in need of an update.
	 *
	 * @return bool
	 */
	protected static function register_filters() {
		foreach ( (array) tribe_get_option( 'custom-fields', array() ) as $field ) {
			/**
			 * Controls whether a filter is created or not for a particular additional field.
			 *
			 * @var bool  $create  create a filter object for this additional field
			 * @var array $field   additional field definition
			 */
			if ( ! apply_filters( 'tribe_events_filter_create_additional_field_filter', true, $field ) ) {
				continue;
			}

			/**
			 * Controls the title used for an additional field filter.
			 *
			 * @var string $label  default title for the additional field filter
			 * @var array  $field  additional field definition
			 */
			$title = apply_filters( 'tribe_events_filter_additional_field_title', $field[ 'label' ], $field );

			/**
			 * Controls the slug used for an additional field filter. This should generally be
			 * unique or else unexpected results could be returned when users apply the filter.
			 *
			 * @var string $slug   default slug for the additional field filter
			 * @var array  $field  additional field definition
			 */
			$slug = apply_filters( 'tribe_events_filter_additional_field_slug', $field[ 'name' ], $field );

			// For multichoice fields we need an extra leading underscore for our meta queries
			$meta_key = Tribe__Events__Pro__Custom_Meta::is_multichoice( $field )
				? '_' . $field[ 'name' ]
				: $field[ 'name' ];

			self::$filters[ $meta_key ] = new Tribe__Events__Filterbar__Filters__Additional_Field(
				$title, $slug, $meta_key
			);
		}

		return true;
	}
}