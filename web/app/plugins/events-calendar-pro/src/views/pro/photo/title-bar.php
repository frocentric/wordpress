<?php
/**
 * Photo View Title Template
 * The title template for the photo view of events.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/photo/title-bar.php
 *
 * @package TribeEventsCalendar
 * @version 4.4.28
 * @since   4.4.28
 *
 */
?>

<div class="tribe-events-title-bar">

	<!-- Photo View Title -->
	<?php do_action( 'tribe_events_before_the_title' ); ?>
	<h1 class="tribe-events-page-title"><?php echo tribe_get_events_title() ?></h1>
	<?php do_action( 'tribe_events_after_the_title' ); ?>

</div>
