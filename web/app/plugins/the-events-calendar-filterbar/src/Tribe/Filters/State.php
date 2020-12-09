<?php

/**
 * Class Tribe__Events__Filterbar__Filters__State
 */
class Tribe__Events__Filterbar__Filters__State extends Tribe__Events__Filterbar__Filters__Base_Meta {
	public $searched_meta = array( '_VenueState', '_VenueStateProvince' );
	public $relation_meta = '_EventVenueID';
	public $join_name = 'state_filter';
	public static $cache_key_base_ids = 'tribe_filterbar_state_ids';

	public function get_searched_post_type() {
		return Tribe__Events__Main::VENUE_POST_TYPE;
	}

	public function get_related_post_type() {
		return Tribe__Events__Main::POSTTYPE;
	}
}
