<?php
/**
 * View: Week View Nav Previous Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/mobile-events/nav/prev.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @var string $link The URL to the previous page, if any, or an empty string.
 *
 * @version 5.0.1
 *
 */
?>
<li class="tribe-events-c-nav__list-item tribe-events-c-nav__list-item--prev">
	<a
		href="<?php echo esc_url( $link ); ?>"
		rel="prev"
		class="tribe-events-c-nav__prev tribe-common-b2"
		data-js="tribe-events-view-link"
		aria-label="<?php esc_attr_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Previous week', 'tribe-events-calendar-pro' ); ?>"
	>
		<?php esc_html_e( 'Previous', 'tribe-events-calendar-pro' ); ?>
	</a>
</li>
