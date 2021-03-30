<?php
/**
 * View: Map View Nav Disabled Previous Button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/nav/prev-disabled.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 */
?>
<li class="tribe-events-c-nav__list-item tribe-events-c-nav__list-item--prev">
	<button
		class="tribe-events-c-nav__prev tribe-common-b3"
		aria-label="<?php echo esc_attr( sprintf( __( 'Previous %1$s', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural() ) ); ?>"
		title="<?php echo esc_attr( sprintf( __( 'Previous %1$s', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural() ) ); ?>"
		disabled
	>
		<?php $this->template( 'components/icons/caret-left', [ 'classes' => [ 'tribe-events-c-nav__prev-icon-svg' ] ] ); ?>
		<span class="tribe-events-c-nav__prev-label">
			<?php
				$events_label = '<span class="tribe-events-c-nav__prev-label-plural tribe-common-a11y-visual-hide">' . tribe_get_event_label_plural() . '</span>';
				echo wp_kses(
					/* translators: %s: Event (plural or singular). */
					sprintf( __( 'Previous %1$s', 'tribe-events-calendar-pro' ), $events_label ),
					[ 'span' => [ 'class' => [] ] ]
				);
			?>
		</span>
	</button>
</li>
