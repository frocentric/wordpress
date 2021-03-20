<?php
/**
 * Widget: Events List Event Organizer
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-events-list/event/organizer.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 * @var WP_Post            $event   The event post object with properties added by the `tribe_get_event` function.
 * @var array<string,bool> $display Associative array of display settings for event meta.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$count = $event->organizers->count();
if ( empty( $count ) || empty( $display['organizer'] ) ) {
	return;
}
?>
<div class="tribe-events-widget-events-list__event-organizers">

	<?php foreach ( $event->organizers as $index => $organizer ) : ?>
		<div class="tribe-events-widget-events-list__event-organizer tribe-common-b2">

			<div class="tribe-events-widget-events-list__event-organizer-title-wrapper">
				<?php if ( 0 === $index ) : ?>
					<span class="tribe-events-widget-events-list__event-organizer-label">
						<?php
						echo esc_html(
							sprintf(
								/* Translators: %1$s: Organizer label (singular) */
								_x( '%1$s: ', 'Organizer label for event in events list widget.', 'tribe-events-calendar-pro' ),
								tribe_get_organizer_label_singular()
							)
						);
						?>
					</span>
				<?php endif; ?>

				<?php if ( 0 !== $index && $index === $count - 1 ) : ?>
					<span class="tribe-events-widget-events-list__event-organizer-separator">
						<?php echo esc_html_x( 'and', 'Separator for event organizers in events list widget.', 'tribe-events-calendar-pro' ); ?>
					</span>
				<?php endif; ?>

				<a
					href="<?php echo esc_url( $organizer->permalink ); ?>"
					class="tribe-events-widget-events-list__event-organizer-title-link tribe-common-b2--bold tribe-common-anchor-thin"
				>
					<?php echo wp_kses_post( $organizer->post_title ); ?>
				</a>
			</div>

			<?php if ( ! empty( $organizer->phone ) ) : ?>
				<div class="tribe-events-widget-events-list__event-organizer-meta">
					<address class="tribe-events-widget-events-list__event-organizer-contact">
						<span class="tribe-events-widget-events-list__event-organizer-phone">
							<?php echo esc_html( $organizer->phone ); ?>
						</span>
					</address>
				</div>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>

</div>
