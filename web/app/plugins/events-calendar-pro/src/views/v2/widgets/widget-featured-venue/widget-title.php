<?php
/**
 * Widget: Featured Venue - Widget Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/widget-title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var string $widget_title The User-supplied widget title.
 */
if ( empty( $widget_title ) ) {
	return;
}
?>
<header class="tribe-events-widget-featured-venue__header">
	<h2 class="tribe-common-h6 tribe-common-h--alt tribe-events-widget-featured-venue__header-title">
		<?php echo esc_html( $widget_title ); ?>
	</h2>
</header>
