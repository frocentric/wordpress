<?php
/**
 * View: Venue meta details
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta/details.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $venue The venue post object.
 *
 */

$address = tribe_address_exists( $venue->ID );
$phone   = tribe_get_phone( $venue->ID );
$url     = tribe_get_venue_website_url( $venue->ID );

if (
	empty( $address)
	&& empty( $phone )
	&& empty( $url )
) {
	return;
}

?>
<div class="tribe-events-pro-venue__meta-details">

	<?php $this->template( 'venue/meta/details/address', [ 'venue' => $venue ] ); ?>

	<?php $this->template( 'venue/meta/details/phone', [ 'venue' => $venue ] ); ?>

	<?php $this->template( 'venue/meta/details/website', [ 'venue' => $venue ] ); ?>

</div>
