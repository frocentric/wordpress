<?php
/**
 * TEC Filterbar
 *
 * @package     Frocentric/Customizations/Tribe
 */

namespace Frocentric\Customizations\Tribe;

/**
 * Class Filterbar_Filter_Discipline
 */
class Filterbar_Filter_Discipline extends Filterbar_Filter_Taxonomy {

	protected function get_taxonomy() {
		return 'discipline';
	}
}
