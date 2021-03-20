<?php
/**
 * Widget: Featured Venue - Venue - Address
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/venue/address.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var WP_Post $venue The venue post object with properties added by the `tribe_get_venue_object` function.
 *
 * @see tribe_get_venue_object() For the format of the venue object.
 */
if (
	empty( $venue->address )
	&& empty( $venue->city )
	&& empty( $venue->state_province )
	&& empty( $venue->zip )
	&& empty( $venue->country )
) {
	return;
}

?>
<div class="tribe-common-h7 tribe-common-h--alt tribe-events-widget-featured-venue__venue-info-group tribe-events-widget-featured-venue__venue-info-group--address">
	<em
		class="tribe-events-widget-featured-venue__venue-icon"
		aria-label="<?php esc_attr_e( 'Address', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Address', 'tribe-events-calendar-pro' ); ?>"
	>
		<?php
		$this->template(
			'components/icons/map-pin',
			[
				'classes' => [
					'tribe-events-widget-featured-venue__venue-icon-svg',
					'tribe-events-widget-featured-venue__venue-icon-svg--address',
				],
			]
		);
		?>
	</em>

	<div class="tribe-events-widget-featured-venue__venue-content tribe-events-widget-featured-venue__venue-address-info">

		<?php if ( ! empty( $venue->address ) ) : ?>
			<div class="tribe-events-widget-featured-venue__venue-address-info-street">
				<?php echo esc_html( $venue->address ); ?>
			</div>
		<?php endif; ?>

		<?php
		if (
			! empty( $venue->city )
			|| ! empty( $venue->state_province )
			|| ! empty( $venue->zip )
		) :
		?>
			<div class="tribe-events-widget-featured-venue__venue-address-info-larger-areas">

				<?php if ( ! empty( $venue->city ) ) : ?>
					<span class="tribe-events-widget-featured-venue__venue-address-info-city">
						<?php echo esc_html( $venue->city ); ?>,
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $venue->state_province ) ) : ?>
					<span class="tribe-events-widget-featured-venue__venue-address-info-region">
						<?php echo esc_html( $venue->state_province ); ?>
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $venue->zip ) ) : ?>
					<span class="tribe-events-widget-featured-venue__venue-address-info-zip">
						<?php echo esc_html( $venue->zip ); ?>
					</span>
				<?php endif; ?>

			</div>
		<?php endif; ?>

		<?php if ( ! empty( $venue->country ) ) : ?>
			<div class="tribe-events-widget-featured-venue__venue-address-info-country">
				<?php echo esc_html( $venue->country ); ?>
			</div>
		<?php endif; ?>

	</div>
</div>
