<?php
/**
 * View: Week View - Day Selector
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/day-selector.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var bool $hide_weekends Boolean on whether to hide weekends.
 */
$classes = [ 'tribe-events-pro-week-day-selector' ];
if ( $hide_weekends ) {
	$classes[] = 'tribe-events-pro-week-day-selector--hide-weekends';
}
?>
<section <?php tribe_classes( $classes ); ?>>

	<?php $this->template( 'week/day-selector/days' ); ?>

	<?php $this->template( 'week/day-selector/nav' ); ?>

</section>
