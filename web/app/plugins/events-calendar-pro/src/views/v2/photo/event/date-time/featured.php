<?php
/**
 * View: Photo View - Single Event Featured Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event/date-time/featured.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @since 5.1.1
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

if ( empty( $event->featured ) ) {
	return;
}
?>
<em
	class="tribe-events-pro-photo__event-datetime-featured-icon tribe-common-svgicon tribe-common-svgicon--featured"
	aria-label="<?php esc_attr_e( 'Featured', 'tribe-events-calendar-pro' ); ?>"
	title="<?php esc_attr_e( 'Featured', 'tribe-events-calendar-pro' ); ?>"
>
</em>
<span class="tribe-events-pro-photo__event-datetime-featured-text">
	<?php esc_html_e( 'Featured', 'tribe-events-calendar-pro' ); ?>
</span>
