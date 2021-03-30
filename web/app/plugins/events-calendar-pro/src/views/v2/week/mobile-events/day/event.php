<?php
/**
 * View: Week View Mobile Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 *
 * @var WP_Post $event The event post object, decorated with additional properties by the `tribe_get_event` function.
 *
 * @see tribe_get_event() for the additional properties added to the event post object.
 */

$classes = [ 'tribe-common-g-row', 'tribe-common-g-row--gutters', 'tribe-events-pro-week-mobile-events__event' ];
$classes = get_post_class( $classes, $event->ID );

if ( ! empty( $event->featured ) ) {
	$classes[] = 'tribe-events-pro-week-mobile-events__event--featured';
}
?>
<article <?php tribe_classes( $classes ) ?>>

	<?php $this->template( 'week/mobile-events/day/event/featured-image', [ 'event' => $event ] ); ?>

	<div class="tribe-events-pro-week-mobile-events__event-details tribe-common-g-col">

		<?php $this->template( 'week/mobile-events/day/event/date', [ 'event' => $event ] ); ?>
		<?php $this->template( 'week/mobile-events/day/event/title', [ 'event' => $event ] ); ?>
		<?php $this->template( 'week/mobile-events/day/event/venue', [ 'event' => $event ] ); ?>
		<?php $this->template( 'week/mobile-events/day/event/cost', [ 'event' => $event ] ); ?>

	</div>

</article>
