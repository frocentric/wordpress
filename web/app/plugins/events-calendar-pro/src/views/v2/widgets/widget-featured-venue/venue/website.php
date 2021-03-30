<?php
/**
 * Widget: Featured Venue - Venue - Website
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/venue/website.php
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

if ( empty( $venue->website ) ) {
	return;
}
?>
<div class="tribe-common-h7 tribe-common-h--alt tribe-events-widget-featured-venue__venue-info-group tribe-events-widget-featured-venue__venue-info-group--website">
	<em
		class="tribe-events-widget-featured-venue__venue-icon"
		aria-label="<?php esc_attr_e( 'Website', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Website', 'tribe-events-calendar-pro' ); ?>"
	>
		<?php
		$this->template(
			'components/icons/website',
			[
				'classes' => [
					'tribe-events-widget-featured-venue__venue-icon-svg',
					'tribe-events-widget-featured-venue__venue-icon-svg--website',
				],
			]
		);
		?>
	</em>

	<div class="tribe-events-widget-featured-venue__venue-content tribe-events-widget-featured-venue__venue-website">
		<a
			class="tribe-common-anchor-thin tribe-events-widget-featured-venue__venue-website-link"
			href="<?php echo esc_url( $venue->website ); ?>"
			rel="noreferrer noopener"
			target="_blank"
		>
			<?php echo esc_url( $venue->website ); ?>
		</a>
	</div>
</div>
