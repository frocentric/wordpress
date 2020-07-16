<?php
/**
 * View: Map View Nav Today Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/nav/today.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @var string $today_url The URL to the today, current, version of the View.
 *
 * @version 5.0.1
 *
 */
?>
<li class="tribe-events-c-nav__list-item tribe-events-c-nav__list-item--today">
	<a
		href="<?php echo esc_url( $today_url ); ?>"
		class="tribe-events-c-nav__today tribe-common-b3"
		data-js="tribe-events-view-link"
		aria-label="<?php esc_attr_e( 'Click to select today\'s date', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Click to select today\'s date', 'tribe-events-calendar-pro' ); ?>"
	>
		<?php esc_html_e( 'Today', 'tribe-events-calendar-pro' ); ?>
	</a>
</li>
