<?php
/**
 * Block: Related Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/blocks/related-events/event-info.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1ajx
 *
 * @version 4.6.1
 *
 */
?>
<div class="tribe-related-event-info">
	<h3 class="tribe-related-events-title">
		<a href="<?php echo tribe_get_event_link( $event ); ?>" class="tribe-event-url" rel="bookmark">
			<?php echo get_the_title( $event->ID ); ?>
		</a>
	</h3>
	<?php
		if ( $event->post_type == Tribe__Events__Main::POSTTYPE ) :
			echo tribe_events_event_schedule_details( $event );
		endif;
	?>
</div>
