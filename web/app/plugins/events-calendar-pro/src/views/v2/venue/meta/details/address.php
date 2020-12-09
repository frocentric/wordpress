<?php
/**
 * View: Venue meta details - Address
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta/details/address.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post $venue The venue post object.
 *
 */

if ( ! tribe_address_exists( $venue->ID ) ) {
	return;
}

$address = tribe_get_full_address( $venue->ID );

?>
<div class="tribe-events-pro-venue__meta-address tribe-common-b1 tribe-common-b2--min-medium">
	<em
		class="tribe-events-pro-venue__meta-address-icon tribe-common-svgicon"
		aria-label="<?php esc_attr_e( 'Address', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Address', 'tribe-events-calendar-pro' ); ?>"
	>
	</em>
	<div class="tribe-events-pro-venue__meta-address-details">
		<?php echo $address; ?>
		<a
			href="<?php echo esc_url( $venue->directions_link ) ;?>"
			class="tribe-events-pro-venue__meta-address-directions-link tribe-common-anchor"
		><?php esc_html_e( 'Get Directions', 'tribe-events-calendar-pro' ); ?></a>
	</div>
</div>

