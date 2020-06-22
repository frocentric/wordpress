<?php


/**
 * Class Tribe__Events__Community__Shortcode__Abstract
 *
 * @since 4.6.2
 */
abstract class Tribe__Events__Community__Shortcode__Abstract {

	/**
	 * Shortcode View Type
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Shortcode Add Event URL
	 *
	 * @var string
	 */
	protected $new_event_url = '';

	/**
	 * Shortcode List URL
	 *
	 * @var string
	 */
	protected $event_list_url = '';

	public function hooks() {}

	public function enqueue_assets() {}

	public function do_shortcode() {}

	/**
	 * Verify if the provided ID (or the current post/page ID) corresponds to a valid event, organizer or venue
	 *
	 * @since 4.6.2
	 *
	 * @param array $attributes
	 *
	 * @return mixed the event, venue or organizer ID or false (if the ID is invalid)
	 */
	public function check_id( array $attributes ) {

		if ( empty( $attributes ) ) {
			return false;
		}

		if ( isset( $attributes['id'] ) ) {
			$id = absint( $attributes['id'] );
		} else {
			global $post;
			$id = isset( $post->ID ) ? $post->ID : false;
		}

		$view_exists = isset( $attributes['view'] );
		$views       = [
			'event',
			'venue',
			'organizer',
		];

		foreach ( $views as $view ) {
			$callback = "tribe_is_$view";
			if (
				$view_exists &&
				(
					$attributes['view'] === "edit_$view" ||
					$attributes['view'] === 'attendees_report' ||
					$attributes['view'] === 'sales_report' ||
					$attributes['view'] === 'split_payments'
				) &&
				function_exists( $callback ) &&
				call_user_func( $callback, $id )
			) {
				return $id;
			}
		}

		return false;
	}

	/**
	 * Verify if user has the correct permissions to access the Attendees and Sales reports via shortcode
	 *
	 * @since 4.6.2
	 *
	 * @param mixed $event_id The event ID or false
	 *
	 * @return mixed the login form or true (if user has the necessary permissions to submit new events)
	 */
	public function is_logged_in( $event_id = false ) {
		$events_label     = tribe_get_event_label_singular_lowercase();
		$edit             = false;

		if ( $event_id ) {
			$edit = true;
		}

		if ( ( ! tribe( 'community.main' )->allowAnonymousSubmissions && ! is_user_logged_in() ) || ( $edit && $event_id && ! is_user_logged_in() ) ) {
			do_action( 'tribe_events_community_event_submission_login_form' );
			do_action_deprecated(
				'tribe_ce_event_submission_login_form',
				[],
				'4.6.3',
				'tribe_events_community_event_submission_login_form',
				'The action "tribe_ce_event_submission_login_form" has been renamed to "tribe_events_community_event_submission_login_form" to match plugin namespacing.'
			);
			$view = tribe( 'community.main' )->login_form( __( 'Please log in first.', 'tribe-events-community' ) );

			return $view;
		}

		if ( $edit && $event_id && ! current_user_can( 'edit_post', $event_id ) ) {
			$view = '<p>' . sprintf( esc_html__( 'You do not have permission to edit this %s.', 'tribe-events-community' ), $events_label ) . '</p>';

			return $view;
		}

		return true;
	}

	/**
	 * Set the Shortcode View Type if Available
	 *
	 * @since 4.6.2
	 */
	public function set_shortcode_type_input() {

		if ( empty( $this->type ) ) {
			return;
		}
		?>

		<input
			type="hidden"
			id="community-shortcode-type"
			value="<?php echo esc_attr( $this->type ); ?>"
			name="community-shortcode-type"
		/>

		<?php
	}
}
