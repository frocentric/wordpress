<?php
// Don't load directly.
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Image Uploader Block
 * Renders the image upload field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/image.php
 *
 * @since  3.1
 * @since  4.7.1 Now using new tribe_community_events_field_classes function to set up classes for the input.
 *
 * @version 4.7.1
 */

$upload_error = tribe( 'community.main' )->max_file_size_exceeded();
$size_format  = size_format( tribe( 'community.main' )->max_file_size_allowed() );
$image_label  = sprintf( __( '%s Image', 'tribe-events-community' ), tribe_get_event_label_singular() );
?>

<div class="tribe-section tribe-section-image-uploader">
	<div class="tribe-section-header">
		<h3><?php echo esc_html( $image_label ); ?></h3>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 */
	do_action( 'tribe_events_community_section_before_featured_image' );
	?>

	<div class="tribe-section-content">
		<?php
		$class = '';
		if ( get_post() && has_post_thumbnail() ) {
			$class = 'has-image';
		}
		?>
		<div class="tribe-image-upload-area <?php echo esc_attr( $class ); ?>">
			<input type="hidden" name="detach_thumbnail" id="tribe-events-community-detach-thumbnail" value="false">

			<div class="note">
				<p><?php echo esc_html( sprintf( __( 'Choose a .jpg, .png, or .gif file under %1$s in size.', 'tribe-events-community' ), $size_format ) ); ?></p>
			</div>

			<?php if ( get_post() && has_post_thumbnail() ) { ?>
				<div class="tribe-community-events-preview-image">
					<?php the_post_thumbnail( 'medium' ); ?>

					<div>
						<label for="uploadFile" class="uploaded-msg">
							<?php esc_html_e( 'Uploaded:', 'tribe-events-community' ); ?>
						</label>
						<span class="current-image"><?php echo esc_html( basename( get_attached_file( get_post_thumbnail_id() ) ) ); ?></span>
					</div>

					<?php tribe_community_events_form_image_delete(); ?>
				</div>
			<?php } ?>

			<div class="form-controls">

				<label for="uploadFile" class="selected-msg">
					<?php esc_html_e( 'Selected:', 'tribe-events-community' ); ?>
				</label>

				<input id="uploadFile" class="uploadFile" placeholder="" disabled="disabled"/>

				<label for="EventImage" class="screen-reader-text <?php echo esc_attr( $upload_error ? 'error' : '' ); ?>">
					<?php esc_html_e( 'Event Image', 'tribe-events-community' ); ?>
				</label>

				<div class="choose-file tribe-button tribe-button-secondary"><?php esc_html_e( 'Choose Image', 'tribe-events-community' ); ?></div>

				<label for="uploadFile" class="screen-reader-text">
					<?php esc_html_e( 'Upload File', 'tribe-events-community' ); ?>
				</label>

				<input
					id="EventImage"
					class="EventImage"
					type="file"
					name="event_image"
					class="<?php tribe_community_events_field_classes( 'EventImage', [] ); ?>"
				>

			</div>

			<div class="tribe-remove-upload"><a href="#"><?php esc_html_e( 'Remove image', 'tribe-events-community' ); ?></a></div>
		</div>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 */
	do_action( 'tribe_events_community_section_after_featured_image' );
	?>
</div>
