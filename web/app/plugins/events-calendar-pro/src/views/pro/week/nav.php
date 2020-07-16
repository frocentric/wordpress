<?php
/**
 * Week View Nav
 * This file loads the week view navigation.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/pro/week/nav.php
 *
 * @package TribeEventsCalendar
 * @version 4.4.28
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<nav class="tribe-events-nav-pagination" aria-label="<?php esc_html_e( 'Week Navigation', 'tribe-events-calendar-pro' ); ?>">
	<ul class="tribe-events-sub-nav">
		<li class="tribe-events-nav-previous">
			<?php echo tribe_events_week_previous_link() ?>
		</li><!-- .tribe-events-nav-previous -->
		<li class="tribe-events-nav-next">
			<?php echo tribe_events_week_next_link() ?>
		</li><!-- .tribe-events-nav-next -->
	</ul><!-- .tribe-events-sub-nav -->
</nav>