<?php
/**
 * View: Map View - Single Event Distance
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/event/distance.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
 * @var bool  $show_distance Whether the distance measure should show or not.
 * @var string  $geoloc_unit The localized name of the unit, either `miles` or `kms`, to display distances.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */

if ( ! $show_distance ) {
	return;
}
?>

<div class="tribe-events-pro-map__event-distance tribe-common-b3 tribe-common-a11y-hidden">
	<?php echo esc_html( round( (float) $event->distance, 2 ) ) . ' ' . esc_html( strtolower( $geoloc_unit ) ); ?>
</div>
