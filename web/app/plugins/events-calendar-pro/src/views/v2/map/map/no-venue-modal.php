<?php
/**
 * View: Map View - No Venue Modal
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/map/no-venue-modal.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 * @var object $map_provider Object with data of map provider.
 * @var array  $events       The array containing the events.
 */

// Gets the first event.
$event      = reset( $events );
$is_premium = $map_provider->is_premium;

$href      = '#';
$classes   = [ 'tribe-events-pro-map__no-venue-modal' ];
$classes[] = $is_premium ? 'tribe-events-pro-map__no-venue-modal--premium' : 'tribe-events-pro-map__no-venue-modal--default';

// Verifies that is premium, first event exists, or first event has a venue.
if ( $is_premium || empty( $event ) || ( isset( $event->venues ) && $event->venues->count() ) ) {
	$classes[] = 'tribe-common-a11y-hidden';
} else {
	$href = $event->permalink;
}
?>
<div
	<?php tribe_classes( $classes ); ?>
	data-js="tribe-events-pro-map-no-venue-modal"
>
	<?php if ( $is_premium ) : ?>
		<button
			class="tribe-events-pro-map__no-venue-modal-close"
			data-js="tribe-events-pro-map-no-venue-modal-close"
			title="<?php esc_html_e( 'Close modal', 'tribe-events-calendar-pro' ); ?>"
		>
			<span class="tribe-events-pro-map__no-venue-modal-close-text tribe-common-a11y-visual-hide">
				<?php esc_html_e( 'Close modal', 'tribe-events-calendar-pro' ); ?>
			</span>
			<span class="tribe-events-pro-map__no-venue-modal-close-icon">
				<?php $this->template( 'components/icons/close', [ 'classes' => [ 'tribe-events-pro-map__no-venue-modal-close-icon-svg' ] ] ); ?>
			</span>
		</button>
	<?php endif; ?>

	<div class="tribe-events-pro-map__no-venue-modal-content">
		<div class="tribe-events-pro-map__no-venue-modal-icon">
			<?php $this->template( 'components/icons/no-map', [ 'classes' => [ 'tribe-events-pro-map__no-venue-modal-icon-svg' ] ] ); ?>
		</div>

		<p class="tribe-events-pro-map__no-venue-modal-text tribe-common-h5 tribe-common-h--alt">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %1$s: event (singular) */
					__( 'This %1$s does not have a mappable address.', 'tribe-events-calendar-pro' ),
					tribe_get_event_label_singular_lowercase()
				)
			);
			?>
		</p>

		<a
			href="<?php echo esc_url( $href ); ?>"
			class="tribe-events-pro-map__no-venue-modal-link tribe-common-cta tribe-common-cta--thin-alt"
			data-js="tribe-events-pro-map-no-venue-modal-link"
		>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: Event (singular) */
					__( 'View %s Details', 'tribe-events-calendar-pro' ),
					tribe_get_event_label_singular()
				)
			);
			?>
		</a>
	</div>
</div>
