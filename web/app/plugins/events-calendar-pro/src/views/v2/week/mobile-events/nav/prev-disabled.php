<?php
/**
 * View: Week View Nav Disabled Previous Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/nav/prev-disabled.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @version 5.0.1
 *
 */
?>
<li class="tribe-events-c-nav__list-item tribe-events-c-nav__list-item--prev">
	<button
		class="tribe-events-c-nav__prev tribe-common-b2"
		disabled
		aria-label="<?php esc_attr_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>"
	>
		<?php esc_html_e( 'Previous', 'tribe-events-calendar-pro' ); ?>
	</button>
</li>
