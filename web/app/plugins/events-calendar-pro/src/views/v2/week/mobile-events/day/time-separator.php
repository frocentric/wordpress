<?php
/**
 * View: Week View Time Separator
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day/time-separator.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var string $datetime The event datetime string, in the ISO 8601 format, e.g. `2019-01-01T00:00:00+00:00`.
 * @var string $formatted_time The time formatted according to the site settings.
 */
?>
<div class="tribe-events-pro-week-mobile-events__event-time-separator">
	<time
		class="tribe-events-pro-week-mobile-events__event-time-separator-text tribe-common-h7 tribe-common-h--alt"
		datetime="<?php echo esc_attr( $datetime ); ?>"
	>
		<?php echo esc_html( $time ); ?>
	</time>
</div>
