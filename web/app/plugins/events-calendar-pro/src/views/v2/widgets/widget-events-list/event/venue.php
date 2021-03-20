<?php
/**
 * Widget: Events List Event Venue
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-events-list/event/venue.php
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

if ( ! $event->venues->count() ) {
	return;
}

if (
	empty( $display['venue'] )
	&& empty( $display['street'] )
	&& empty( $display['city'] )
	&& empty( $display['region'] )
	&& empty( $display['zip'] )
	&& empty( $display['country'] )
	&& empty( $display['phone'] )
) {
	return;
}

$venue = $event->venues[0];
?>
<div class="tribe-events-widget-events-list__event-venue tribe-common-b2">

	<?php if ( ! empty( $display['venue'] ) ) : ?>
		<a
			href="<?php echo esc_url( $venue->permalink ); ?>"
			class="tribe-events-widget-events-list__event-venue-name tribe-common-b2--bold tribe-common-anchor-thin"
		>
			<?php echo wp_kses_post( $venue->post_title ); ?>
		</a>
	<?php endif; ?>

	<?php
	if (
		! empty( $display['street'] )
		|| ! empty( $display['city'] )
		|| ! empty( $display['region'] )
		|| ! empty( $display['zip'] )
		|| ! empty( $display['country'] )
		|| ! empty( $display['phone'] )
	) :
	?>
		<address class="tribe-events-widget-events-list__event-venue-address">

			<?php if ( ! empty( $venue->address ) && ! empty( $display['street'] ) ) : ?>
				<div class="tribe-events-widget-events-list__event-venue-address-street">
					<?php echo esc_html( $venue->address ); ?>
				</div>
			<?php endif; ?>

			<?php
			if (
				( ! empty( $venue->city ) && ! empty( $display['city'] ) )
				|| ( ! empty( $venue->state_province ) && ! empty( $display['region'] ) )
				|| ( ! empty( $venue->zip ) && ! empty( $display['zip'] ) )
			) :
			?>
				<div class="tribe-events-widget-events-list__event-venue-address-larger-areas">
					<?php if ( ! empty( $venue->city ) && ! empty( $display['city'] ) ) : ?>
						<span class="tribe-events-widget-events-list__event-venue-address-city">
							<?php echo esc_html( $venue->city ); ?>
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $venue->state_province ) && ! empty( $display['region'] ) ) : ?>
						<span class="tribe-events-widget-events-list__event-venue-address-region">
							<?php echo esc_html( $venue->state_province ); ?>
						</span>
					<?php endif; ?>
					<?php if ( ! empty( $venue->zip ) && ! empty( $display['zip'] ) ) : ?>
						<span class="tribe-events-widget-events-list__event-venue-address-zip">
							<?php echo esc_html( $venue->zip ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $venue->country ) && ! empty( $display['country'] ) ) : ?>
				<div class="tribe-events-widget-events-list__event-venue-address-country">
					<?php echo esc_html( $venue->country ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $venue->phone ) && ! empty( $display['phone'] ) ) : ?>
				<div class="tribe-events-widget-events-list__event-venue-phone">
					<?php echo esc_html( $venue->phone ); ?>
				</div>
			<?php endif; ?>

		</address>
	<?php endif; ?>

</div>
