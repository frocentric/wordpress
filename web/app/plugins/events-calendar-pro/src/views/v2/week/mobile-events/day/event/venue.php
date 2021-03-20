<?php
/**
 * View: Week View - Mobile Event Venue
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day/event/venue.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 */

if ( ! $event->venues->count() ) {
	return;
}

$separator            = esc_html_x( ', ', 'Address separator', 'tribe-events-calendar-pro' );
$venue                = $event->venues[0];
$append_after_address = array_filter( array_map( 'trim', [ $venue->city, $venue->state_province, $venue->state, $venue->province ] ) );
$address              = $venue->address . ( $venue->address && $append_after_address ? $separator : '' );
?>
<address class="tribe-events-pro-week-mobile-events__event-venue tribe-common-b2">
	<span class="tribe-events-pro-week-mobile-events__event-venue-title tribe-common-b2--bold">
		<?php echo wp_kses_post( $venue->post_title ); ?>
	</span>
	<span class="tribe-events-pro-week-mobile-events__event-venue-address">
		<?php echo esc_html( $address ); ?>
		<?php if ( $append_after_address ) : ?>
			<?php echo esc_html( reset( $append_after_address ) ); ?>
		<?php endif; ?>
	</span>
</address>
