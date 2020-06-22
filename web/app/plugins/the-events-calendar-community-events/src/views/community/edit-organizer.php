<?php
/**
 * Edit Organizer Form (requires form-organizer.php)
 * This is used to edit an event organizer.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/edit-organizer.php
 *
 * @since 3.1
 *
 * @version 4.6.3
 *
 * @var int $organizer_id The ID of the Organizer being edited.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$organizer_label_singular = tribe_get_organizer_label_singular();
?>

<?php tribe_get_template_part( 'community/modules/header-links' ); ?>

<form method="post">

	<?php wp_nonce_field( 'ecp_organizer_submission' ); ?>

	<!-- Organizer Title -->
	<?php $organizer_name = esc_attr( tribe_get_organizer() ); ?>
	<div class="events-community-post-title">
		<label for="post_title" class="<?php echo ( $_POST && empty( $organizer_name ) ) ? 'error' : ''; ?>">
			<?php printf( __( '%s Name:', 'tribe-events-community' ), $organizer_label_singular ); ?>
			<small class="req"><?php esc_html_e( '(required)', 'tribe-events-community' ); ?></small>
		</label>
		<input type="text" name="post_title" value="<?php echo esc_attr( $organizer_name ); ?>"/>

	</div><!-- .events-community-post-title -->

	<!-- Organizer Description -->
	<div class="events-community-post-content">

		<label for="post_content">
			<?php printf( __( '%s Description:', 'tribe-events-community' ), $organizer_label_singular ); ?>
			<small class="req"></small>
		</label>

		<?php // if admin wants rich editor (and using WP 3.3+) show the WYSIWYG, otherwise default to a textarea
		$content = tribe_community_events_get_organizer_description();
		if ( tribe( 'community.main' )->useVisualEditor && function_exists( 'wp_editor' ) ) {
			$settings = [
				'wpautop'       => true,
				'media_buttons' => false,
				'editor_class'  => 'frontend',
				'textarea_rows' => 5,
			];
			echo wp_editor( $content, 'tcepostcontent', $settings );
		} else {
			echo '<textarea name="tcepostcontent">' . esc_textarea( $content ) . '</textarea>';
		} ?>

	</div><!-- .events-community-post-content -->

	<?php tribe_get_template_part( 'community/modules/organizer-fields' ); ?>

	<!-- Form Submit -->
	<div class="tribe-events-community-footer">

		<input type="submit" class="button submit events-community-submit" value="<?php
			echo esc_attr( $organizer_id ? sprintf( __( 'Update %s', 'tribe-events-community' ), $organizer_label_singular ) : sprintf( __( 'Submit %s', 'tribe-events-community' ), $organizer_label_singular ) );
		?>" name="community-event" />

	</div><!-- .tribe-events-community-footer -->

</form>
