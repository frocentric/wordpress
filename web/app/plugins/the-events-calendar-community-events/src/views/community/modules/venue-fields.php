<?php
/**
 * Venue Fields Template
 *
 * This is used to edit the details of individual venues (address, email, etc).
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/venue-fields.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since   4.5.5
 * @since   4.6.7 Minor comment update.
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Site-wide setting indicating whether or not to show Google Maps
$google_maps_enabled = tribe_get_option( 'embedGoogleMaps', true )
?>

<div class="tribe-events-community-details eventForm bubble" id="event_tribe_venue">

	<div class="tribe-community-event-info">

		<div class="tribe_sectionheader">
			<h4>
				<?php echo esc_html( sprintf( __( '%s Details', 'tribe-events-community' ), tribe_get_venue_label_singular() ) ); ?>
				<?php echo  tribe_community_required_field_marker( 'venue' ); ?>
			</h4>
		</div>

		<div class="venue">

			<label for="VenueAddress">
				<?php esc_html_e( 'Address', 'tribe-events-community' ); ?>:
			</label>
			<input
				type="text"
				id="VenueAddress"
				name="venue[Address]"
				size="25"
				value="<?php echo esc_attr( tribe_get_address() ); ?>"
			/>

		</div><!-- .venue -->

		<div class="venue">

			<label for="VenueCity">
				<?php esc_html_e( 'City', 'tribe-events-community' ); ?>:
			</label>
			<input
				type="text"
				id="VenueCity"
				name="venue[City]"
				size="25"
				value="<?php echo esc_attr( tribe_get_city() ); ?>"
			/>
		</div><!-- .venue -->

		<?php if ( ! tribe_community_events_single_geo_mode() ) : ?>
			<div class="venue">
				<label for="EventCountry">
					<?php esc_html_e( 'Country', 'tribe-events-community' ); ?>:
				</label>
				<select
					name="venue[Country]"
					id="EventCountry"
					class="tribe-dropdown"
				>
					<?php foreach ( Tribe__View_Helpers::constructCountries() as $abbr => $fullname ) : ?>
						<option
							value="<?php echo esc_attr( $fullname ) ?>"
							<?php selected( tribe_get_country() == $fullname ); ?>
						><?php echo esc_html( $fullname ); ?></option>
					<?php endforeach; ?>
				</select>
			</div><!-- .venue -->

			<div class="venue">
				<label for="StateProvinceText">
					<?php esc_html_e( 'State or Province', 'tribe-events-community' ); ?>:
				</label>
				<input
					id="StateProvinceText"
					name="venue[Province]"
					type="text"
					size="25"
					value="<?php echo esc_attr( tribe_get_province() ); ?>"
				/>
				<select
					id="StateProvinceSelect"
					name="venue[State]"
					class="tribe-dropdown"
				>
					<option value=""><?php esc_html_e( 'Select a State', 'tribe-events-community' ); ?></option>
					<?php foreach ( Tribe__View_Helpers::loadStates() as $abbr => $fullname ) : ?>
						<option
							value="<?php echo esc_attr( $abbr ); ?>"
							<?php selected( tribe_get_state() == $abbr ); ?>
						><?php echo esc_html( $fullname ) ?></option>
					<?php endforeach; ?>
				</select>

			</div><!-- .venue -->
		<?php endif; ?>

		<div class="venue">

			<label for="EventZip">
				<?php esc_html_e( 'Postal Code', 'tribe-events-community' ); ?>:
			</label>
			<input
				type="text"
				id="EventZip"
				name="venue[Zip]"
				size="6"
				value="<?php echo esc_attr( tribe_get_zip() ); ?>"
			/>

		</div><!-- .venue -->

		<div class="venue">

			<label for="EventPhone">
				<?php esc_html_e( 'Phone', 'tribe-events-community' ); ?>:
			</label>
			<input
				type="tel"
				id="EventPhone"
				name="venue[Phone]"
				size="14"
				value="<?php echo esc_attr( tribe_get_phone() ); ?>"
			/>

		</div><!-- .venue -->

		<div class="venue">

			<label for="EventWebsite">
				<?php esc_html_e( 'Website', 'tribe-events-community' ); ?>:
			</label>
			<input
				type="url"
				id="EventWebsite"
				name="venue[URL]"
				size="14"
				value="<?php echo esc_attr( tribe_get_venue_website_url() ); ?>"
			/>

		</div><!-- .venue -->

		<?php if ( $google_maps_enabled ) : ?>
			<div class="venue">

				<label for="VenueShowMap">
					<?php esc_html_e( 'Show Google Map', 'tribe-events-community' ); ?>:
				</label>
				<input
					type="checkbox"
					id="VenueShowMap"
					name="venue[ShowMap]"
					<?php checked( tribe_embed_google_map() ); ?>
				/>

			</div><!-- #google_map_toggle -->
		<?php endif; ?>

		<div class="venue">
			<label for="VenueShowMapLink">
				<?php esc_html_e( 'Show Google Maps Link', 'tribe-events-community' ); ?>:
			</label>
			<input
				type="checkbox"
				id="VenueShowMapLink"
				name="venue[ShowMapLink]"
				<?php checked( tribe_show_google_map_link() ); ?>
			/>
		</div><!-- #google_map_link_toggle -->

		<?php
		/**
		 * After venue editor's meta fields are output.
		 */
		do_action( 'tribe_events_community_after_venue_meta' )
		?>

	</div><!-- #event_tribe_venue -->

</div>