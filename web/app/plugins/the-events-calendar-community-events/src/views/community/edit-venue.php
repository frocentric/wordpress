<?php
/**
 * Edit Venue Form
 * This is used to edit an event venue.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/edit-venue.php
 *
 * @since 3.1
 * @version 4.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$venue_label_singular = tribe_get_venue_label_singular();
?>

<?php tribe_get_template_part( 'community/modules/header-links' ); ?>

<form method="post">

	<?php wp_nonce_field( 'ecp_venue_submission' ); ?>

	<!-- Venue Title -->
	<div class="events-community-post-title">
		<label for="post_title">
			<?php printf( __( '%s Name:', 'tribe-events-community' ), $venue_label_singular ); ?>
			<small class="req"><?php esc_html_e( '(required)', 'tribe-events-community' ); ?></small>
		</label>
		<input type="text" name="post_title" required="required" value="<?php echo esc_attr( tribe_get_venue() ); ?>"/>

	</div><!-- .events-community-post-title -->

	<!-- Venue Description -->
	<div class="events-community-post-content">

		<label for="post_content">
			<?php printf( __( '%s Description:', 'tribe-events-community' ), $venue_label_singular ); ?>
		</label>

		<?php // if admin wants rich editor (and using WP 3.3+) show the WYSIWYG, otherwise default to a textarea
		$content = tribe_community_events_get_venue_description();
		if ( tribe( 'community.main' )->useVisualEditor && function_exists( 'wp_editor' ) ) {
			$settings = [
				'wpautop' => true,
				'media_buttons' => false,
				'editor_class' => 'frontend',
				'textarea_rows' => 5,
			];
			echo wp_editor( $content, 'tcepostcontent', $settings );
		} else {
			echo '<textarea name="tcepostcontent">' . esc_textarea( $content ) . '</textarea>';
		} ?>

	</div><!-- .events-community-post-content -->

	<?php tribe_get_template_part( 'community/modules/venue-fields' ); ?>

	<!-- Form Submit -->
	<div class="tribe-events-community-footer">

		<input type="submit" class="button submit events-community-submit" value="<?php
			echo esc_attr( sprintf( __( 'Update %s', 'tribe-events-community' ), $venue_label_singular ) );
		?>" name="community-event" />

	</div><!-- .tribe-events-community-footer -->

</form>
