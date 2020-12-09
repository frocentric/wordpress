<?php
class Tribe__Events__Pro__Editor__Configuration implements Tribe__Editor__Configuration_Interface {
	/**
	 * Add actions / filters into WP
	 *
	 * @since 4.5
	 */
	public function hook() {
		add_filter( 'tribe_editor_config', array( $this, 'editor_config' ) );
		add_filter( 'tribe_block_block_data_event-datetime', [ $this, 'block_data_event_datetime' ], 2, 10 );
	}

	/**
	 * Attach variables into the localized variables via the filter 'tribe_editor_config'
	 *
	 *
	 * @since 4.5
	 *
	 * @param $editor_config
	 *
	 * @return array
	 */
	public function editor_config( $editor_config ) {
		$editor_config = $this->set_defaults( $editor_config );
		$pro = empty( $editor_config['eventsPRO'] ) ? array() : $editor_config['eventsPRO'];

		$editor_config['common']['rest']['nonce'] = array_merge(
			$editor_config['common']['rest']['nonce'],
			array(
				'queue_status_nonce' => tribe( 'events-pro.editor.recurrence.queue-status' )->get_ajax_nonce(),
			)
		);

		$editor_config['eventsPRO'] = array_merge(
			(array) $pro,
			$this->localize(),
			array(
				'additional_fields_tab' => sprintf(
					'%s%s',
					trailingslashit( $editor_config['common']['admin_url'] ),
					'edit.php?page=tribe-common&tab=additional-fields&post_type=tribe_events'
				),
			)
		);

		return $editor_config;
	}


	/**
	 * Prevent the accessing of not defined variables by setting some default values
	 *
	 * @since 4.5
	 *
	 * @param array $editor_config
	 *
	 * @return array
	 */
	public function set_defaults( $editor_config ) {
		if ( empty( $editor_config['common']['admin_url'] ) ) {
			$editor_config['common']['admin_url'] = admin_url();
		}

		if ( empty( $editor_config['common']['rest'] ) ) {
			$editor_config['common']['rest'] = array();
		}

		if ( empty( $editor_config['common']['rest']['nonce'] ) ) {
			$editor_config['common']['rest']['nonce'] = array();
		}

		return $editor_config;
	}

	/**
	 * Variables localized by the plugin
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function localize() {
		return array(
			'defaults'              => $this->get_editor_defaults(),
			'additional_fields'     => tribe( 'events-pro.editor.fields' )->get_fields(),
		);
	}

	/**
	 * return default values for the editor localization
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function get_editor_defaults() {
		$defaults = array();

		// If defined set the default Venue
		$default_venue = (int) tribe_get_option( 'eventsDefaultVenueID', 0 );
		if ( $default_venue ) {
			$defaults['venue'] = $default_venue;
		}

		// If defined, set the default Venue Address
		$venue_address = tribe_get_option( 'eventsDefaultAddress', '' );
		if ( '' !== $venue_address ) {
			$defaults['venueAddress'] = $venue_address;
		}

		$venue_city = tribe_get_option( 'eventsDefaultCity', '' );
		if ( '' !== $venue_city ) {
			$defaults['venueCity'] = $venue_city;
		}

		$venue_state = tribe_get_option( 'eventsDefaultState', '' );
		if ( '' !== $venue_state ) {
			$defaults['venueState'] = $venue_state;
		}

		$venue_province = tribe_get_option( 'eventsDefaultProvince', '' );
		if ( '' !== $venue_province ) {
			$defaults['venueProvince'] = $venue_province;
		}

		$venue_zip = tribe_get_option( 'eventsDefaultZip', '' );
		if ( '' !== $venue_zip ) {
			$defaults['venueZip'] = $venue_zip;
		}

		$venue_phone = tribe_get_option( 'eventsDefaultPhone', '' );
		if ( '' !== $venue_phone ) {
			$defaults['venuePhone'] = $venue_phone;
		}

		$venue_country = tribe_get_option( 'defaultCountry', null );
		if ( $venue_country ) {
			$defaults['venueCountry'] = $venue_country;
		}

		// If defined, set the default Organizer
		$default_organizer = (int) tribe_get_option( 'eventsDefaultOrganizerID', 0 );
		if ( $default_organizer ) {
			$defaults['organizer'] = $default_organizer;
		}

		return $defaults;
	}

	/**
	 * Add Events Pro block data to the datetime block.
	 *
	 * @since 5.1.0
	 *
	 * @param array                                         $block_data The block data.
	 * @param Tribe__Events__Editor__Blocks__Event_Datetime $block      The datetime block class instance.
	 */
	public function block_data_event_datetime( $block_data, $block ) {
		$block_data['attributes'] = array_merge(
			$block_data['attributes'],
			[
				'exceptions'  => [
					'type'   => 'string',
					'source' => 'meta',
					'meta'   => '_tribe_blocks_recurrence_exclusions',
				],
				'rules'       => [
					'type'   => 'string',
					'source' => 'meta',
					'meta'   => '_tribe_blocks_recurrence_rules',
				],
				'description' => [
					'type'   => 'string',
					'source' => 'meta',
					'meta'   => '_tribe_blocks_recurrence_description',
				],
			]
		);

		return $block_data;
	}
}
