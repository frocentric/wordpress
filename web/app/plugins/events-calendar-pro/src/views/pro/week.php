<?php
/**
 * Week View Template
 * The wrapper template for week view.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/week.php
 *
 * @package TribeEventsCalendar
 *
 * @version 4.4.28
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php do_action( 'tribe_events_before_template' ) ?>

	<!-- Title Bar -->
<?php tribe_get_template_part( 'pro/week/title-bar' ); ?>

	<!-- Tribe Bar -->
<?php tribe_get_template_part( 'modules/bar' ); ?>

	<!-- Main Events Content -->
<?php tribe_get_template_part( 'pro/week/content' ) ?>

<?php
do_action( 'tribe_events_after_template' );
