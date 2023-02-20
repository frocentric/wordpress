<?php
// Don't load directly
defined( 'WPINC' ) && current_user_can( 'manage_options' ) || die;

/**
 * Event Submission Form Post Status Block
 * Renders the post status field in the submission form (for administrators only).
 */
$event = get_post();

if ( ! isset( $event ) ) {
	$event = null;
}

$current_option = empty( $event ) || empty( $event->post_status ) ? '' : $event->post_status;
$options = [
	['value' => 'publish', 'text' => __( 'Published' )],
	['value' => 'pending', 'text' => __( 'Pending Review' )],
	['value' => 'draft', 'text' => __( 'Draft' )],
];

?>
<div id="event_tribe_post_status" class="tribe-section tribe-section-event-status">
	<div class="tribe-section-header">
		<h3 class="<?php echo tribe_community_events_field_has_error( 'post_status' ) ? 'error' : ''; ?>">
			<?php
			echo __( 'Post Status', 'froware' );
			echo tribe_community_required_field_marker( 'post_status' );
			?>
		</h3>
	</div>
	<div class="tribe-section-content">
		<div class="tribe-section-content-field">
		<label for="PostStatus-status" );">
		<?php echo esc_html_x( 'Set status:', 'Event status label the select field', 'the-events-calendar' ); ?>
	</label>
	<div
		class="tribe-events-status tribe-events-status-select"
	>
		<label
			class="screen-reader-text tribe-events-status-label__text"
			for="PostStatus-status"
		>
			<?php echo esc_html( _x( 'Set status:', 'The label of the event status select.', 'the-events-calendar' ) ); ?>
		</label>
		<select
			id="PostStatus-status"
			name="admin_post_status"
			class="tribe-dropdown tribe-post-status__status-select"
			value="<?php echo esc_attr( $current_option ); ?>"
			data-hide-search
			data-prevent-clear
		>
			<?php foreach ( $options as $option ) : ?>
				<option value="<?php echo esc_attr( $option['value'] ); ?>" <?php selected( $option['value'], $current_option ); ?>><?php echo esc_html( $option['text'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
		</div>
	</div>
</div>
