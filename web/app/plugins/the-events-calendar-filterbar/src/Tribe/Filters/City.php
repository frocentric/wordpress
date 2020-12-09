<?php

/**
 * Class Tribe__Events__Filterbar__Filters__City
 */
class Tribe__Events__Filterbar__Filters__City extends Tribe__Events__Filterbar__Filters__Base_Meta {
	public $searched_meta = '_VenueCity';
	public $relation_meta = '_EventVenueID';
	public $join_name = 'city_filter';
	public static $cache_key_base_ids = 'tribe_filterbar_city_ids';

	public function get_searched_post_type() {
		return Tribe__Events__Main::VENUE_POST_TYPE;
	}

	public function get_related_post_type() {
		return Tribe__Events__Main::POSTTYPE;
	}
}
