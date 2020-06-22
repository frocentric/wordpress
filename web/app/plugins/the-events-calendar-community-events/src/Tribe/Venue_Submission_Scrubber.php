<?php

/**
 * Class Tribe__Events__Community__Venue_Submission_Scrubber
 */
class Tribe__Events__Community__Venue_Submission_Scrubber extends Tribe__Events__Community__Submission_Scrubber {
	 // filter these with 'tribe_events_community_allowed_event_fields'
	protected $allowed_fields = [
		'post_content',
		'post_title',
		'venue',
	];

	public function __construct( $submission ) {
		parent::__construct( $submission );
	}

	/**
	 * The following block of code is taken from the events calendar code that it uses to prepare the data of venue for saving.
	 */
	protected function set_venue() {
		$this->submission['venue'] = stripslashes_deep( $this->submission['venue'] );
		$this->submission['venue'] = $this->filter_venue_data( $this->submission['venue'] );
	}

	protected function set_post_type() {
		$this->submission['post_type'] = Tribe__Events__Main::VENUE_POST_TYPE;
	}

	protected function set_organizer() {
		return; // nothing to do here
	}
}
