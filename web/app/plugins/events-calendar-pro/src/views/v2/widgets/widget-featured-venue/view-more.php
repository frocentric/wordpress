<?php
/**
 * Widget: Featured Venue - View More Link
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/view-more.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var string $view_more_link  The URL to view all events.
 * @var string $view_more_text  The text for the "view more" link.
 * @var string $view_more_title The widget "view more" link title attribute. Adds some context to the link for screen readers.
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( empty( $view_more_link ) ) {
	return;
}
?>
<div class="tribe-common-b1 tribe-common-b2--min-medium tribe-events-widget-featured-venue__view-more">
	<a
		href="<?php echo esc_url( $view_more_link ); ?>"
		class="tribe-common-anchor-thin tribe-events-widget-featured-venue__view-more-link"
		title="<?php echo esc_attr( $view_more_title ); ?>"
	>
		<?php echo esc_html( $view_more_text ); ?>
	</a>
</div>
