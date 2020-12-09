<?php
/**
 * The interface that Geo Location Search handlers should implement to interact with our code.
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */

namespace Tribe\Events\Pro\Views\V2\Geo_Loc;

use Tribe__Context as Context;

/**
 * Interface Handler_Interface
 *
 * @since   4.7.9
 *
 * @package Tribe\Events\Pro\Views\V2\Geo_Loc
 */
interface Handler_Interface {

	/**
	 * Applies geographic coordinates fencing to the repository arguments by altering them in any way.
	 *
	 * @since 4.7.9
	 *
	 * @param array|null           $repository_args An associative array of the current repository arguments.
	 * @param \Tribe__Context|null $context         The context that should be used to fetch the geolocation
	 *                                              information.
	 *
	 * @return array An array of altered repository arguments.
	 */
	public function filter_repository_args( array $repository_args = [], Context $context = null );
}