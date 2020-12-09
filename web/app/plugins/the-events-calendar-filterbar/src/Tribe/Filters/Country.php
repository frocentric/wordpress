<?php

/**
 * Class Tribe__Events__Filterbar__Filters__Country
 */
class Tribe__Events__Filterbar__Filters__Country extends Tribe__Events__Filterbar__Filters__Base_Meta {
	public $searched_meta = '_VenueCountry';
	public $relation_meta = '_EventVenueID';
	public $join_name = 'country_filter';
	public static $cache_key_base_ids = 'tribe_filterbar_country_ids';

	public function get_searched_post_type() {
		return Tribe__Events__Main::VENUE_POST_TYPE;
	}

	public function get_related_post_type() {
		return Tribe__Events__Main::POSTTYPE;
	}

	public function filter_related_name( $data ) {

		$name = $this->get_country_name( $data->meta_value );
		$code = $this->get_country_code( $data->meta_value );

		// Try to fill Name based on a valid code
		if ( false === $name && false !== $code ) {
			$name = $this->get_country_name( $code );
		}

		return $name;
	}

	public function is_valid_data( $name, $value ) {
		return ! empty( $name ) && is_numeric( $value );
	}

	/**
	 * get a country code from it's name
	 *
	 * @author bordoni
	 * @param  string $country Country Name
	 *
	 * @return string $code the country code
	 */
	public function get_country_code( $country ) {
		$countries = Tribe__View_Helpers::constructCountries();
		$codes     = array_flip( $countries );

		if ( ! isset( $codes[ $country ] ) ) {
			return false;
		}

		return $codes[ $country ];
	}

	/**
	 * get a country code from an event id
	 *
	 * @author bordoni
	 * @param  string $code the country code
	 *
	 * @return string $name the country name based on code
	 */
	public function get_country_name( $code ) {
		$countries = Tribe__View_Helpers::constructCountries();

		if ( ! isset( $countries[ $code ] ) ) {
			return false;
		}

		return $countries[ $code ];
	}
}
