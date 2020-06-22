<?php
// Don't load directly.
defined( 'WPINC' ) or die;

/**
 * Event Submission Form
 * The wrapper template for the event submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/title.php
 *
 * @since  4.5
 * @since  4.7.1 Added translator comments.
 *
 * @version  4.7.1
 */

$events_label_singular = tribe_get_event_label_singular();
?>

<div class="events-community-post-title">
	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 */
	do_action( 'tribe_events_community_section_before_title' );
	?>
	<?php // Translators: %s Events label. ?>
	<?php tribe_community_events_field_label( 'post_title', sprintf( __( '%s Title:', 'tribe-events-community' ), $events_label_singular ) ); ?>
	<?php tribe_community_events_form_title(); ?>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 */
	do_action( 'tribe_events_community_section_after_title' );
	?>
</div>
