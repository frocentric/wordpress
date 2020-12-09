<?php
/**
 * View: Organizer meta details - Phone
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details/phone.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
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
	<em
		class="tribe-events-pro-organizer__meta-phone-icon tribe-common-svgicon"
		aria-label="<?php esc_attr_e( 'Phone', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Phone', 'tribe-events-calendar-pro' ); ?>"
	>
	</em>
	<span class="tribe-events-pro-organizer__meta-phone-text"><?php echo esc_html( $phone ); ?></span>
</div>

