<?php
/**
 * View: Organizer meta details - Website
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details/website.php
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

$url = tribe_get_organizer_website_url( $organizer->ID );

if ( empty( $url ) ) {
	return;
}

?>
<div class="tribe-events-pro-organizer__meta-website tribe-common-b1 tribe-common-b2--min-medium">
	<em
		class="tribe-events-pro-organizer__meta-website-icon tribe-common-svgicon"
		aria-label="<?php esc_attr_e( 'Website', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Website', 'tribe-events-calendar-pro' ); ?>"
	>
	</em>
	<a
		href="<?php echo esc_url( $url ); ?>"
		class="tribe-events-pro-organizer__meta-website-link tribe-common-anchor"
	><?php echo esc_html( $url ); ?></a>
</div>

