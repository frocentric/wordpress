<?php
/**
 * Map View Single Featured Event
 * This file contains one featured event in the map
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/map/single-featured.php
 *
 * @version 4.4.28
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $post;

$venue_details = tribe_get_venue_details();

// Venue microformats
$has_venue         = ( $venue_details ) ? ' vcard' : '';
$has_venue_address = ( ! empty( $venue_details['address'] ) ) ? ' location' : '';

echo tribe_event_featured_image( null, 'large' );
?>

<!-- Event Title -->
<?php do_action( 'tribe_events_before_the_event_title' ) ?>

<!-- Event Distance -->
<?php echo tribe_event_distance(); ?>

<h3 class="tribe-events-map-event-title">
	<a class="tribe-event-url" href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title() ?>" rel="bookmark">
		<?php the_title() ?>
	</a>
</h3>
<?php do_action( 'tribe_events_after_the_event_title' ) ?>

<!-- Event Meta -->
<?php do_action( 'tribe_events_before_the_meta' ) ?>
<div class="tribe-events-event-meta <?php echo esc_attr( $has_venue . $has_venue_address ); ?>">

	<!-- Schedule & Recurrence Details -->
	<div class="published time-details">
		<?php echo tribe_events_event_schedule_details() ?>
	</div>

	<?php if ( $venue_details ) : ?>
		<!-- Venue Display Info -->
		<div class="tribe-events-venue-details">
			<?php echo implode( ', ', $venue_details ); ?>
		</div> <!-- .tribe-events-venue-details -->
	<?php endif; ?>

</div><!-- .tribe-events-event-meta -->
<?php do_action( 'tribe_events_after_the_meta' ) ?>

<?php if ( tribe_get_cost() ) : ?>
	<div class="tribe-events-event-cost">
		<span class="ticket-cost"><?php echo tribe_get_cost( null, true ); ?></span>
		<?php
			/** This action is documented in the-events-calendar/src/views/list/single-event.php */
			do_action( 'tribe_events_inside_cost' )
		?>
	</div>
<?php endif; ?>

<!-- Event Content -->
<?php do_action( 'tribe_events_before_the_content' ) ?>
<div class="tribe-events-map-event-description tribe-events-content description entry-summary">
	<?php echo tribe_events_get_the_excerpt() ?>
	<a href="<?php echo esc_url( tribe_get_event_link() ); ?>" class="tribe-events-read-more" rel="bookmark"><?php esc_html_e( 'Find out more', 'tribe-events-calendar-pro' ) ?> &raquo;</a>
</div><!-- .tribe-events-map-event-description -->
<?php
do_action( 'tribe_events_after_the_content' );
