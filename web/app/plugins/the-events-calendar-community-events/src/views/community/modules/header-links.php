<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Header links for edit forms.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/header-links.php
 *
 * @since  3.1
 * @version 4.5.7
 *
 */

$post_id = get_the_ID();
?>

<header class="my-events-header">
	<h2 class="my-events">
		<?php
		if ( $post_id && tribe_is_event( $post_id ) ) {
			esc_html_e( 'Edit Event', 'tribe-events-community' );
		} elseif ( $post_id && tribe_is_organizer( $post_id ) ) {
			esc_html_e( 'Edit Organizer', 'tribe-events-community' );
		} elseif ( $post_id && tribe_is_venue( $post_id ) ) {
			esc_html_e( 'Edit Venue', 'tribe-events-community' );
		} else {
			esc_html_e( 'Add New Event', 'tribe-events-community' );
		}
		?>
	</h2>

	<?php if ( is_user_logged_in() ) : ?>
	<a
		href="<?php echo esc_url( tribe_community_events_list_events_link() ); ?>"
		class="tribe-button tribe-button-secondary"
	>
		<?php esc_html_e( 'View Your Submitted Events', 'tribe-events-community' ); ?>
	</a>
	<?php endif; ?>
</header>

<?php echo tribe_community_events_get_messages();
