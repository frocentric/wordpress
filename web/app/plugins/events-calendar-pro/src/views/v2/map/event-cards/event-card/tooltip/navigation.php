<?php
/**
 * View: Map View - Tooltip Navigation
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/tooltip/navigation.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */
?>
<nav class="tribe-events-pro-map__event-tooltip-navigation">
	<ul class="tribe-events-pro-map__event-tooltip-navigation-list tribe-common-g-row">
		<li class="tribe-events-pro-map__event-tooltip-navigation-list-item tribe-common-g-col">
			<button
				class="tribe-events-pro-map__event-tooltip-navigation-button tribe-events-pro-map__event-tooltip-navigation-button--prev tribe-common-b2 tribe-common-b3--min-medium"
				data-js="tribe-events-pro-map-event-tooltip-prev-button"
			>
				<?php $this->template( 'components/icons/arrow-right', [ 'classes' => [ 'tribe-events-pro-map__event-tooltip-navigation-button-icon-svg' ] ] ); ?>
				<?php esc_html_e( 'Prev', 'tribe-events-calendar-pro' ); ?>
			</button>
		</li>
		<li class="tribe-events-pro-map__event-tooltip-navigation-list-item tribe-common-g-col">
			<button
				class="tribe-events-pro-map__event-tooltip-navigation-button tribe-events-pro-map__event-tooltip-navigation-button--next tribe-common-b2 tribe-common-b3--min-medium"
				data-js="tribe-events-pro-map-event-tooltip-next-button"
			>
				<?php esc_html_e( 'Next', 'tribe-events-calendar-pro' ); ?>
				<?php $this->template( 'components/icons/arrow-right', [ 'classes' => [ 'tribe-events-pro-map__event-tooltip-navigation-button-icon-svg' ] ] ); ?>
			</button>
		</li>
	</ul>
</nav>
