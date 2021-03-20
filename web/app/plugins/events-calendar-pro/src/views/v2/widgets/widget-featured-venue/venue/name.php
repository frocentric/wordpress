<?php
/**
 * Widget: Featured Venue - Venue - Name
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/venue/name.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var WP_Post $venue The venue post object with properties added by the `tribe_get_venue_object` function.
 *
 * @see tribe_get_venue_object() For the format of the venue object.
 */
if ( empty( $venue->post_title ) ) {
	return;
}
?>
<h3 class="tribe-common-h4 tribe-events-widget-featured-venue__venue-name">
	<a class="tribe-common-anchor-thin tribe-events-widget-featured-venue__venue-name-link" href="<?php echo esc_url( $venue->permalink ); ?>">
		<?php echo esc_html( $venue->post_title ); ?>
	</a>
</h3>
