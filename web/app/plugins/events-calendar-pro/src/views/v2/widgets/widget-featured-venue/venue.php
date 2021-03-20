<?php
/**
 * Widget: Featured Venue - Venue
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/widgets/widget-featured-venue/venue.php
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

?>
<div class="tribe-events-widget-featured-venue__venue">

	<?php $this->template( 'widgets/widget-featured-venue/venue/name' ); ?>

	<address class="tribe-events-widget-featured-venue__venue-address">

		<?php $this->template( 'widgets/widget-featured-venue/venue/address' ); ?>

		<?php $this->template( 'widgets/widget-featured-venue/venue/phone' ); ?>

		<?php $this->template( 'widgets/widget-featured-venue/venue/website' ); ?>

	</address>
</div>
