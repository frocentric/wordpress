<?php
/**
 * Class for the date submitted conditional trigger type. 
 *
 * @package     Ninja Forms - Conditional Logic
 * @subpackage  Classes/Triggers
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.8
*/

class NF_CL_Date_Submitted_Trigger extends NF_CL_Trigger_Base {
	/**
	 * Get things rolling
	 */
	function __construct() {
		// Call our parent constructor. This ensures our $comparison_operators variable is set properly.
		parent::__construct();

		// Set our label and slug.
		$this->label = __( 'Date Submitted', 'ninja-forms-conditionals' );
		$this->slug = 'date_submitted';

		// Set our type to date. This will control our "value" output if a user selects this trigger.
		$this->type = 'date';

		// Unset the comparison operators we don't want to use for this trigger type.
		unset( $this->comparison_operators['=='] );
		unset( $this->comparison_operators['!='] );
		unset( $this->comparison_operators['<'] );
		unset( $this->comparison_operators['>'] );
		unset( $this->comparison_operators['contains'] );
		unset( $this->comparison_operators['notcontains'] );

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
		$plugin_settings = nf_get_settings();
		$date_format = $plugin_settings['date_format'];

		$now = date( $date_format, current_time( 'timestamp' ) );
		return ninja_forms_conditional_compare( $now, $value, $compare );
	}
}

return new NF_CL_Date_Submitted_Trigger();