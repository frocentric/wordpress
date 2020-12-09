<?php
/**
 * View: Organizer meta details - Email
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details/email.php
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

$email = tribe_get_organizer_email( $organizer->ID );

if ( empty( $email ) ) {
	return;
}

?>
<div class="tribe-events-pro-organizer__meta-email tribe-common-b1 tribe-common-b2--min-medium">
	<em
		class="tribe-events-pro-organizer__meta-email-icon tribe-common-svgicon"
		aria-label="<?php esc_attr_e( 'Email', 'tribe-events-calendar-pro' ); ?>"
		title="<?php esc_attr_e( 'Email', 'tribe-events-calendar-pro' ); ?>"
	>
	</em>
	<a
		href="mailto:<?php echo esc_attr( $email ); ?>"
		class="tribe-events-pro-organizer__meta-email-link tribe-common-anchor"
	><?php echo esc_html( $email ); ?></a>
</div>

