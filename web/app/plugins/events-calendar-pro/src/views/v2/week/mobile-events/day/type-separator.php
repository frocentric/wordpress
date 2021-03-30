<?php
/**
 * View: Week View Type Separator
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/day/type-separator.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var string $type The type of separator, one of `ongoing` or `all_day`.
 */

if ( empty( $type ) ) {
	return;
}

$separator_text = 'all_day' === $type
	? __( 'All Day' , 'tribe-events-calendar-pro' )
	: __( 'Ongoing', 'tribe-events-calendar-pro' );
?>
<div class="tribe-events-pro-week-mobile-events__event-type-separator">
	<span class="tribe-events-pro-week-mobile-events__event-type-separator-text tribe-common-h7 tribe-common-h--alt">
		<?php echo esc_html( $separator_text ); ?>
	</span>
</div>
