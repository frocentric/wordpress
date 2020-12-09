<?php
/**
 * The settings text that accompanies the "Fix venues data" option in the Maps Settings.
 *
 * @since 4.4.34
 *
 * @var object $venues A WP_Query containing the results of looking up venues with a missing _VenueGeoAddress meta field.
 */
?>

<a name="geoloc_fix"></a>
<fieldset class="tribe-field tribe-field-html">
	<legend><?php esc_html_e( 'Fix geolocation data', 'tribe-events-calendar-pro' ); ?></legend>
	<div class="tribe-field-wrap">
		<?php echo $this->fix_geoloc_data_button(); ?>
		<p class="tribe-field-indent description">
			<?php esc_html_e( 'You have venues for which we don\'t have geolocation data.', 'tribe-events-calendar-pro' ); ?>
			<?php if ( ! tribe_is_using_basic_gmaps_api() ) : ?>
				<?php esc_html_e( 'We will use the Google Maps API to get that information. Doing this may take a while (approximately 1 minute for every 200 venues).', 'tribe-events-calendar-pro' ); ?>
			<?php endif; ?>
		</p>
	</div>
</fieldset>