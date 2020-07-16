<?php
/**
 * View: Photo View - Single Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event/title.php
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
 */
?>
<h3 class="tribe-events-pro-photo__event-title tribe-common-h6">
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		title="<?php the_title_attribute( $event->ID ); ?>"
		rel="bookmark"
		class="tribe-events-pro-photo__event-title-link tribe-common-anchor-thin"
	>
		<?php echo wp_kses_post( get_the_title( $event->ID ) ); ?>
	</a>
</h3>
