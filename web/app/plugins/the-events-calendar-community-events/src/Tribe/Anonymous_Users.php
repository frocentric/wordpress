<?php
/**
 * Provides support for anonymous users of Community Events or any logged in user.
 *
 * @property-read array $venue_org_creation_caps
 */
class Tribe__Events__Community__Anonymous_Users {
	/**
	 * Stores the values of any lazily generated properties.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Sets up any special handling required to let anonymous users submit
	 * events, etc.
	 *
	 * @param Tribe__Events__Community__Main $community
	 */
	public function __construct( Tribe__Events__Community__Main $community ) {

		//Filter Capabilities for both Anonymous and Logged in Users
		if ( is_user_logged_in() || $community->allowAnonymousSubmissions ) {
			$this->allow_venue_organizer_submissions();
			$this->allow_tribe_event_validation();
		}

	}

	/**
	 * Lazily returns certain properties that we may not be able to determine during
	 * instantiation (or may not require at all in many requests).
	 *
	 * @param  string $property
	 * @return array
	 */
	public function __get( $property ) {
		if ( isset( $this->data[ $property ] ) ) {
			return $this->data[ $property ];
		}

		if ( 'venue_org_creation_caps' === $property ) {
			$this->data[ $property ] = [
				get_post_type_object( Tribe__Events__Main::VENUE_POST_TYPE )->cap->create_posts => true,
				get_post_type_object( Tribe__Events__Main::ORGANIZER_POST_TYPE )->cap->create_posts => true,
			];
		}

		return $this->data[ $property ];
	}

	/**
	 * Provide anonymous users with the capabilities to create new venues and
	 * organizers, but only when required (ie in advance of rendering the
	 * event form or processing submissions).
	 */
	protected function allow_venue_organizer_submissions() {
		add_action( 'tribe_events_community_form', [ $this, 'add_venue_org_caps' ] );
		add_action( 'tribe_events_community_before_event_submission_page', [ $this, 'add_venue_org_caps' ] );
	}

	/**
	 * Sets up a capabilities filter so that users can submit venues and organizers.
	 * Intended for use with anonymous users.
	 */
	public function add_venue_org_caps() {
		add_filter( 'user_has_cap', [ $this, 'filter_venue_org_caps' ], 10, 3 );
	}

	/**
	 * Temporarily adds the venue and organizer create_posts capabilities to the list of
	 * those held by the current user.
	 *
	 * @param  array $current_capabilities
	 * @return array
	 */
	public function filter_venue_org_caps( array $current_capabilities ) {
		return $current_capabilities + $this->venue_org_creation_caps;
	}

	/**
	 * Various validation tests (if an organizer already exists, etc) run on a wp_ajax_*
	 * hook meaning they return a -1 result if we try to access them while the user is
	 * logged out.
	 *
	 * This hooks them up to the matching wp_ajax_nopriv_* hook so we get the expected
	 * result in the context of the frontend submission form.
	 */
	protected function allow_tribe_event_validation() {
		add_action(
			'wp_ajax_nopriv_tribe_event_validation',
			[
				Tribe__Events__Main::instance(),
				'ajax_form_validate',
			]
		);
	}
}
