<?php
/**
 * View: Week View - Events Day
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/events-day.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post[] $events The day events post objects.
 *
 * @see tribe_get_event() for the additional properties added to the event post object.
 */

?>
<div class="tribe-events-pro-week-grid__events-day" role="gridcell">
	<?php foreach ( $events as $event ) : ?>
		<?php $this->setup_postdata( $event ); ?>
		<?php $this->template( 'week/grid-body/events-day/event', [ 'event' => $event ] ); ?>
	<?php endforeach; ?>
</div>
