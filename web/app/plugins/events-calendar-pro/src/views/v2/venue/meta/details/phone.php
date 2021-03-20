<?php
/**
 * View: Venue meta details - Phone
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta/details/phone.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 * @var WP_Post $venue The venue post object.
 *
 */

$phone = tribe_get_phone( $venue->ID );

if ( empty( $phone ) ) {
	return;
}

?>
<div class="tribe-events-pro-venue__meta-phone tribe-common-b1 tribe-common-b2--min-medium">
	<?php $this->template( 'components/icons/phone', [ 'classes' => [ 'tribe-events-pro-venue__meta-phone-icon-svg' ] ] ); ?>
	<span class="tribe-events-pro-venue__meta-phone-number"><?php echo esc_html( $phone ); ?></span>
</div>
