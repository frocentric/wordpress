<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox For Custom Fields
 * This is used to add a metabox to the event submission form to allow for custom
 * field input for user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  2.1
 * @since  4.6.3 Broke apart views into more customizable pieces and implemented late escaping.
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 */

// Makes sure we dont even try when Pro is inactive
if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
	return;
}

$fields = tribe_get_option( 'custom-fields' );

if ( empty( $fields ) || ! is_array( $fields ) ) {
	return;
}

$post_id = get_the_ID();
?>

<div class="tribe-section tribe-section-custom-fields">
	<div class="tribe-section-header">
		<h3><?php esc_html_e( 'Additional Fields', 'tribe-events-community' ); ?></h3>
	</div>

	<?php
		/**
		 * Allow developers to hook and add content to the beginning of this section.
		 *
		 * @param int $post_id The post ID of the event.
		 */
		do_action( 'tribe_events_community_section_before_custom_fields', $post_id );

		$data = compact( [
			'fields',
			'post_id',
		] );

		tribe_get_template_part( 'community/modules/custom/table', null, $data );

		/**
		 * Allow developers to hook and add content to the end of this section.
		 *
		 * @param int $post_id The post ID of the event.
		 */
		do_action( 'tribe_events_community_section_after_custom_fields', $post_id );
	?>
</div>
