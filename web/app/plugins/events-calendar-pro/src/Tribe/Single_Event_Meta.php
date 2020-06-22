<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


class Tribe__Events__Pro__Single_Event_Meta {

	public function __construct() {
		add_action( 'tribe_events_single_meta_before', array( $this, 'filter_fields' ) );
		add_action( 'tribe_events_single_event_meta_primary_section_end', array( $this, 'additional_fields' ) );
	}

	/**
	 * Setup filters to link the organizer/venue names to their respective single post pages.
	 */
	public function filter_fields() {
		add_filter( 'tribe_get_venue', array( $this, 'link_venue' ) );
	}

	/**
	 * Test to see if the venue name has already been formed as a link - if it has not,
	 * transform it into an HTML link.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public function link_venue( $name ) {
		// Ordinarily we only need this to happen once
		remove_filter( 'tribe_get_venue', array( $this, 'link_venue' ) );

		// If this already contains a link do not double wrap it!
		$contains_link = ( false !== strpos( $name, 'href="' ) );

		return $contains_link ? '' : '<a href="' . esc_url( tribe_get_venue_link( null, false ) ) . '">' . $name . '</a>';
	}

	/**
	 * Render additional field data within the single event view meta section.
	 */
	public function additional_fields() {
		// Don't render old additional fields template if the post has blocks
		if ( has_blocks( get_the_ID() ) ) {
			return;
		}

		tribe_get_template_part( 'pro/modules/meta/additional-fields', null, array(
			'fields' => tribe_get_custom_fields(),
		) );
	}

	/**
	 * Responsible for displaying a user's custom recurrence pattern description.
	 *
	 * @deprecated since 3.6
	 *
	 * @param string $meta_id The meta group this is in.
	 *
	 * @return string The custom description.
	 */
	public static function custom_recurrence_description( $meta_id ) {
		_deprecated_function( __METHOD__, '4.3' );

		global $_tribe_meta_factory;
		$post_id                = get_the_ID();
		$recurrence_meta        = Tribe__Events__Pro__Recurrence__Meta::getRecurrenceMeta( $post_id );
		$recurrence_description = ! empty( $recurrence_meta['recCustomRecurrenceDescription'] ) ? $recurrence_meta['recCustomRecurrenceDescription'] : tribe_get_recurrence_text( $post_id );
		$html                   = tribe_is_recurring_event( $post_id ) ? Tribe__Events__Meta_Factory::template(
			$_tribe_meta_factory->meta[ $meta_id ]['label'],
			$recurrence_description,
			$meta_id ) : '';

		return apply_filters( 'tribe_event_pro_meta_custom_recurrence_description', $html );
	}

	/**
	 * Render the name of the venue (with the link).
	 *
	 * @deprecated since 3.6
	 *
	 * @param string $html    The current venue name.
	 * @param string $meta_id The meta group this is in.
	 *
	 * @return string The modified/linked venue name.
	 */
	public static function venue_name( $html, $meta_id ) {
		_deprecated_function( __METHOD__, '4.3' );

		global $_tribe_meta_factory;
		$post_id = get_the_ID();
		$name    = tribe_get_venue( $post_id );
		$link    = ! empty( $name ) ? '<a href="' . esc_url( tribe_get_venue_link( $post_id, false ) ) . '">' . $name . '</a>' : '';
		$html    = empty( $link ) ? $html : Tribe__Events__Meta_Factory::template(
			$_tribe_meta_factory->meta[ $meta_id ]['label'],
			$link,
			$meta_id );

		return apply_filters( 'tribe_event_pro_meta_venue_name', $html, $meta_id );
	}

	/**
	 * Render the name of the organizer (with the link).
	 *
	 * @deprecated since 3.6
	 *
	 * @param string $html    The current organizer name.
	 * @param string $meta_id The meta group this is in.
	 *
	 * @return string The modified/linked organizer name.
	 */
	public static function organizer_name( $html, $meta_id ) {
		_deprecated_function( __METHOD__, '4.3' );

		global $_tribe_meta_factory;
		$post_id = get_the_ID();
		$name    = tribe_get_organizer_link( $post_id, true );
		$html    = empty( $name ) ? $html : Tribe__Events__Meta_Factory::template(
			$_tribe_meta_factory->meta[ $meta_id ]['label'],
			$name,
			$meta_id );

		return apply_filters( 'tribe_event_pro_meta_organizer_name', $html, $meta_id );
	}

	/**
	 * Returns custom meta.
	 *
	 * @deprecated since 3.6
	 *
	 * @param string $meta_id The meta group this is in.
	 *
	 * @return string The custom meta.
	 */
	public static function custom_meta( $meta_id ) {
		_deprecated_function( __METHOD__, '4.3' );

		$fields      = tribe_get_custom_fields( get_the_ID() );
		$custom_meta = '';
		foreach ( $fields as $label => $value ) {
			$custom_meta .= Tribe__Events__Meta_Factory::template(
				$label,
				$value,
				$meta_id );
		}

		return apply_filters( 'tribe_event_pro_meta_custom_meta', $custom_meta );
	}
}
