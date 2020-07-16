<?php

/**
 * Class Tribe__Events__Community__Organizer_Submission_Scrubber
 */
class Tribe__Events__Community__Organizer_Submission_Scrubber extends Tribe__Events__Community__Submission_Scrubber {
	 // filter these with 'tribe_events_community_allowed_event_fields'
	protected $allowed_fields = [
		'post_content',
		'post_title',
		'organizer',
	];

	public function __construct( $submission ) {
		parent::__construct( $submission );
	}

	/**
	 * The following block of code is taken from the events calendar code that it uses to prepare the data of venue for saving.
	 */
	protected function set_venue() {
		return; // nothing to do here
	}

	protected function set_post_type() {
		$this->submission['post_type'] = Tribe__Events__Main::ORGANIZER_POST_TYPE;
	}

	protected function set_organizer() {
		$this->submission['organizer'] = stripslashes_deep( $this->submission['organizer'] );
		$this->submission['organizer'] = $this->filter_organizer_data( $this->submission['organizer'] );
	}

	protected function filter_organizer_data( $organizer_data ) {
		if ( ! empty( $organizer_data['OrganizerID'] ) ) {
			$organizer_data['OrganizerID'] = array_map( 'intval', $organizer_data['OrganizerID'] );
		}

		$fields = [
			'Phone',
			'Website',
			'Email',
		];

		foreach ( $fields as $field ) {
			if ( isset( $organizer_data[ $field ] ) ) {
				$organizer_data[ $field ] = $this->filter_string( $organizer_data[ $field ] );
			}
		}

		return $organizer_data;
	}
}
