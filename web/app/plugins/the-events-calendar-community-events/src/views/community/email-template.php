<?php
/**
 * Email Template
 * The template for the Event Submission Notification Email
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/email-template.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since   3.6
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Action hook before loading the email template.
 *
 * Useful if you want to insert your own additional custom linked post types.
 *
 * @since 4.5.14
 *
 * @param int|string $tribe_event_id The Event ID.
 */
do_action( 'tribe_events_community_before_email_template', $tribe_event_id );

$events_label_singular = tribe_get_event_label_singular();
$venue_id              = tribe_get_venue_id( $tribe_event_id );
$organizer_ids         = tribe_get_organizer_ids( $tribe_event_id );
$organizer_count       = count( $organizer_ids );

if ( 0 == $organizer_count ) {
	$organizer_label = '';
} else {
	$organizer_label = '%s ' . _n( 'Organizer', 'Organizers', $organizer_count );
}
?>
<html>
	<body>
		<h2><?php echo wp_kses_post( $post->post_title ); ?></h2>

		<h4><?php echo esc_html( tribe_get_start_date( $tribe_event_id ) ); ?> - <?php echo esc_html( tribe_get_end_date( $tribe_event_id ) );
			if ( function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $tribe_event_id ) ) {
				echo ' | ' . sprintf( esc_html__( 'Recurring %s', 'tribe-events-community' ), esc_html( $events_label_singular ) );
			}
			?></h4>

		<hr />

		<h3><?php printf( esc_html__( '%s Venue', 'tribe-events-community' ), esc_html__( $events_label_singular ) ); ?></h3>
		<?php echo '<p><a href="' . esc_url( get_edit_post_link( $venue_id ) ) . '">' . esc_html( tribe_get_venue( $tribe_event_id ) ) . '</a></p>'; ?>

		<h3><?php printf( esc_html__( $organizer_label, 'tribe-events-community' ), esc_html__( $events_label_singular ) ); ?></h3>
		<?php
		foreach ( $organizer_ids as $organizer_id ) {
			echo '<p><a href="' . esc_url( get_edit_post_link( $organizer_id ) ) . '">' . esc_html( tribe_get_organizer( $organizer_id ) ) . '</a></p>';
		}
		?>

		<h3><?php esc_html_e( 'Description', 'tribe-events-community' ); ?></h3>
		<?php echo wp_kses_post( $post->post_content ); ?>

		<hr />
		<?php
		$query = [
			'action' => 'edit',
			'post'   => $tribe_event_id,
		];
		?>
		<h4><?php echo '<a href="' . esc_url( add_query_arg( $query, get_admin_url( null, 'post.php' ) ) ) . '">' . sprintf( esc_html__( 'Review %s', 'tribe-events-community' ), esc_html( $events_label_singular ) ) . '</a>';
			if ( 'publish' == get_post_status( $tribe_event_id ) ) { ?>
				| <a href="<?php echo esc_url( get_permalink( $tribe_event_id ) ); ?>"><?php printf( __( 'View %s', 'tribe-events-community' ), esc_html( $events_label_singular ) ); ?></a>
			<?php } ?>
		</h4>
	</body>
</html>
<?php
/**
 * Action hook after loading the email template.
 *
 * Useful if you want to insert your own additional custom linked post types.
 *
 * @since 4.5.14
 *
 * @param int|string $tribe_event_id The Event ID.
 */
do_action( 'tribe_events_community_after_email_template', $tribe_event_id );
