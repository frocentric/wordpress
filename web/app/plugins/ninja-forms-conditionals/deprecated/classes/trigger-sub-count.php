<?php
/**
 * Class for the sub count conditional trigger type. 
 *
 * @package     Ninja Forms - Conditional Logic
 * @subpackage  Classes/Triggers
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.8
*/

class NF_CL_Sub_Count_Trigger extends NF_CL_Trigger_Base {
	/**
	 * Get things rolling
	 */
	function __construct() {
		// Call our parent constructor. This ensures our $comparison_operators variable is set properly.
		parent::__construct();

		// Set our label and slug.
		$this->label = __( 'Number Of Submissions', 'ninja-forms-conditionals' );
		$this->slug = 'sub_count';

		// Unset the comparison operators we don't want to use for this trigger type.
		unset( $this->comparison_operators['before'] );
		unset( $this->comparison_operators['after'] );
		unset( $this->comparison_operators['contains'] );
		unset( $this->comparison_operators['notcontains'] );
		unset( $this->comparison_operators['on'] );

		$this->conditions = array( 'type' => 'text' );
	}

	/**
	 * Process our date submitted trigger.
	 * When this function is called, it will be passed the value of the parameter and will expect a bool return.
	 * 
	 * @since 1.2.8
	 * @return bool
	 */
	function compare( $value, $compare ) {
		global $ninja_forms_processing;

		$form_id = $ninja_forms_processing->get_form_ID();
		$sub_count = nf_get_sub_count( $form_id );

		return ninja_forms_conditional_compare( $sub_count, $value, $compare );
	}
}

return new NF_CL_Sub_Count_Trigger();