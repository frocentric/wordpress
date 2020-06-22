<?php
/**
 * An implementation of the Featured Events filter that applies to specific contexts.
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */

namespace Tribe\Events\Filterbar\Views\V2\Filters;

/**
 * Class Featured_Events
 *
 * @since   4.9.0
 *
 * @package Tribe\Events\Filterbar\Views\V2\Filters
 */
class Featured_Events extends \Tribe__Events__Filterbar__Filters__Featured_Events {
	use Context_Filter;
}
