<?php
/**
 * View: Organizer meta details - Phone
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details/phone.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.2.0
 *
 * @var WP_Post $organizer The organizer post object.
 *
 */

$phone = tribe_get_organizer_phone( $organizer->ID );

if ( empty( $phone ) ) {
	return;
}

?>
<div class="tribe-events-pro-organizer__meta-phone tribe-common-b1 tribe-common-b2--min-medium">
	<?php $this->template( 'components/icons/phone', [ 'classes' => [ 'tribe-events-pro-organizer__meta-phone-icon-svg' ] ] ); ?>
	<span class="tribe-events-pro-organizer__meta-phone-text"><?php echo esc_html( $phone ); ?></span>
</div>
