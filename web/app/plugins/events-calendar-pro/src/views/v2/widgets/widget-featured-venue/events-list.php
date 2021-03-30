<?php
/**
 * Widget: Featured Venue - Events List
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/events-list.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var array<\WP_Post> $events The array of events to display in the widget list.
 */

?>
<div class="tribe-events-widget-featured-venue__events-list">
	<?php foreach ( $events as $event ) : ?>
		<?php $this->template( 'widgets/widget-featured-venue/events-list/event', [ 'event' => $event ] ); ?>
	<?php endforeach; ?>
</div>
