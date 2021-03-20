<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form
 * The wrapper template for the event submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/spam-control.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since    4.5
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 */

// We don't show anything on Spam Control when user is logged in
if ( is_user_logged_in() ) {
	return;
}
?>

<?php
/**
 * Allow developers to hook and add content to the beginning of this section
 */
do_action( 'tribe_events_community_section_before_honeypot' );
?>

<p class="aes">
	<input type="text" name="tribe-not-title" id="tribe-not-title" value="">
	<label for="tribe-not-title"><?php esc_html_e( 'Fake Title', 'tribe-events-community' ) ?></label>
</p>
<input type="hidden" name="render_timestamp" value="<?php echo esc_attr( time() ); ?>" />

<?php
/**
 * Allow developers to hook and add content to the end of this section
 */
do_action( 'tribe_events_community_section_after_honeypot' );
