<?php
/**
 * View: Week View - Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */
use Tribe__Date_Utils as Dates;

$classes = [ 'tribe-events-pro-week-grid__event' ];

if ( ! empty( $event->is_past ) ) {
	$classes[] = 'tribe-events-pro-week-grid__event--past';
}

if ( ! empty( $event->featured ) ) {
	$classes[] = 'tribe-events-pro-week-grid__event--featured';
}

/*
 * Some CSS classes (i.e. vertical position, duration and sequence) have been calculated in the Week View.
 * Here we add them to the ones that should be applied to the event element.
 */
$classes = array_merge( array_values( $classes ), array_values( $event->classes ) );
$classes = get_post_class( $classes, $event->ID );
$data_js = [ 'tribe-events-pro-week-grid-event-link', 'tribe-events-tooltip' ];

/**
 * Get start time in seconds
 */
$start_time = Dates::time_between( $event->dates->start->format( 'Y-m-d 0:0:0' ), $event->dates->start->format( Dates::DBDATETIMEFORMAT ) );

?>
<article
	<?php tribe_classes( $classes ) ?>
	data-js="tribe-events-pro-week-grid-event"
	data-start-time="<?php echo esc_attr( $start_time ); ?>"
	data-event-id="<?php echo esc_attr( $event->ID ); ?>"
>
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		class="tribe-events-pro-week-grid__event-link"
		data-js="<?php echo esc_attr( implode( ' ', $data_js ) ); ?>"
		data-tooltip-content="#tribe-events-tooltip-content-<?php echo esc_attr( $event->ID ); ?>"
		aria-describedby="tribe-events-tooltip-content-<?php echo esc_attr( $event->ID ); ?>"
	>
		<div class="tribe-events-pro-week-grid__event-link-inner">

			<?php $this->template( 'week/grid-body/events-day/event/featured-image', [ 'event' => $event ] ); ?>
			<?php $this->template( 'week/grid-body/events-day/event/date', [ 'event' => $event ] ); ?>
			<?php $this->template( 'week/grid-body/events-day/event/title', [ 'event' => $event ] ); ?>

		</div>
	</a>
</article>

<?php $this->template( 'week/grid-body/events-day/event/tooltip', [ 'event' => $event ] ); ?>
