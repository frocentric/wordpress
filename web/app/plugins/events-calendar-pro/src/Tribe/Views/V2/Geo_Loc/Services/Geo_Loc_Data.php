<?php
/**
 * A value object Geo Location Services should use to pass around gelocation information.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc\Services
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc\Services;

use Tribe__Utils__Array as Arr;

/**
 * Class Geo_Loc_Data
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc\Services
 */
class Geo_Loc_Data {
	/**
	 * The address to resolve, in its original form.
	 *
	 * @since 4.7.9
	 *
	 * @var string
	 */
	protected $address;
	/**
	 * The resolved latitude.
	 *
	 * @since 4.7.9
	 *
	 * @var float
	 */
	protected $lat = 0;
	/**
	 * The resolved longitude.
	 *
	 * @since 4.7.9
	 *
	 * @var float
	 */
	protected $lng = 0;
	/**
	 * The address as formatted from the service, if any.
	 *
	 * @since 4.7.9
	 *
	 * @var string
	 */
	protected $formatted_address;
	/**
	 * The address components, as provided from the service, if any.
	 *
	 * @since 4.7.9
	 *
	 * @var array
	 */
	protected $address_components;

	/**
	 * Geo_Loc_Data constructor.
	 *
	 * @param string $address The address to resolve.
	 * @param float $lat The resolved latitude.
	 * @param float $lng The resolved longitude.
	 * @param string $formatted_address The address, formatted by the API, if any.
	 * @param array $address_components The address components as provided by the API, if any.
	 */
	public function __construct( $address = '', $lat = 0, $lng = 0, $formatted_address = '', $address_components = [] ) {
		$this->address            = $address;
		$this->lat                = $lat;
		$this->lng                = $lng;
		$this->formatted_address  = $formatted_address;
		$this->address_components = $address_components;
	}

	/**
	 * Builds an instance of the value object from an array.
	 *
	 * @since 4.7.9
	 *
	 * @param array $array The array to build the instance from.
	 *
	 * @return static The built instance of this value object.
	 */
	public static function from_array( array $array ) {
		return new static(
			Arr::get( $array, 'address', '' ),
			(float) Arr::get( $array, 'lat', 0 ),
			(float) Arr::get( $array, 'lng', 0 ),
			Arr::get( $array, 'formatted_address', '' ),
			Arr::get( $array, 'address_components', [] )
		);
	}

	/**
	 * Returns the original addres the API is tasked to resolve.
	 *
	 * @since 4.7.9
	 *
	 * @return string The original addres the API is tasked to resolve.
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 * Returns the resolved latitude.
	 *
	 * @since 4.7.9
	 *
	 * @return float The resolved latitutde, or `0` to indicate latitude was not resolved.
	 */
	public function get_lat() {
		return $this->lat;
	}

	/**
	 * Returns the resolved longitude.
	 *
	 * @since 4.7.9
	 *
	 * @return float The resolved latitutde, or `0` to indicate longitude was not resolved.
	 */
	public function get_lng() {
		return $this->lng;
	}

	/**
	 * Returns the address, as formatted from the API.
	 *
	 * @since 4.7.9
	 *
	 * @return string The formatted address, if any, or an empty string.
	 */
	public function get_formatted_address() {
		return $this->formatted_address;
	}

	/**
	 * Returns the resolved address components, as provided from the API, if any.
	 *
	 * @since 4.7.9
	 *
	 * @return array The resolved address components, if any, or an empty array.
	 */
	public function get_address_components() {
		return $this->address_components;
	}

	/**
	 * Returns an array representation of the Geo Location data.
	 *
	 * @since 4.7.9
	 *
	 * @return array An array containing all the object properties.
	 */
	public function to_array() {
		return [
			'address'            => $this->get_address(),
			'lat'                => $this->get_lat(),
			'lng'                => $this->get_lng(),
			'formatted_address'  => $this->get_formatted_address(),
			'address_components' => $this->get_address_components(),
		];
	}
}