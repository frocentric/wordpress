<?php
/**
 * The base implementation for Geo Location resolvers provides common methods.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc;

use Tribe__Events__Pro__Geo_Loc as Fencer;

/**
 * Class Base_Handler
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */
abstract class Base_Handler {
	/**
	 * An instance of the Geo Loc class, that will be used to "fence" queries.
	 *
	 * @since 4.7.9
	 *
	 * @var Fencer
	 */
	protected $fencer;

	/**
	 * Resolved_Coords_Handler constructor.
	 *
	 * @param Fencer $fencer An instance of the Geo Loc class, that will be used to "fence" queries.
	 */
	public function __construct( Fencer $fencer ) {
		$this->fencer = $fencer;
	}
}