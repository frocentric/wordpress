<?php
/**
 * TEC Filterbar
 *
 * @package     Frocentric/Customizations/Tribe
 */

namespace Frocentric\Customizations\Tribe;

/**
 * Class Filterbar_Filter_Audience
 */
class Filterbar_Filter_Audience extends Filterbar_Filter_Taxonomy {
	protected function get_taxonomy() {
		return 'audience';
	}
}
