<?php
/**
 * Class for conditional trigger types. 
 * This is the parent class. it should be extended by specific trigger types
 *
 * @package     Ninja Forms - Conditional Logic
 * @subpackage  Classes/Triggers
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.8
*/

abstract class NF_CL_Trigger_Base {

	/**
	 * @var label - Store our trigger nicename.
	 */
	var $label = '';

	/**
	 * @var slug - Store our trigger slug.
	 */
	var $slug = '';

	/**
	 * @var comparison_operators - Store our comparison operators
	 */
	var $comparison_operators = array();

	/**
	 * @var conditions - Store our conditional output. Legacy, will be deprecated in the future.
	 */
	var $conditions = array( 'type' => 'text' );

	/**
	 * @var type - What type of trigger? (Currently, only options are '' and 'date' )
	 */
	var $type = '';

	/**
	 * Get things rolling
	 */
	function __construct() {
		$this->comparison_operators = array( 
			'==' 			=> __( 'Equal To', 'ninja-forms-conditionals' ),
			'!=' 			=> __( 'Not Equal To', 'ninja-forms-conditionals' ),
			'<' 			=> __( 'Less Than', 'ninja-forms-conditionals' ),
			'>'				=> __( 'Greater Than', 'ninja-forms-conditionals' ),
			'contains'		=> __( 'Contains', 'ninja-forms-conditionals' ),
			'notcontains'	=> __( 'Does Not Contain', 'ninja-forms-conditionals' ),
			'on'			=> __( 'On', 'ninja-forms-conditionals' ),
			'before'		=> __( 'Before', 'ninja-forms-conditionals' ),
			'after'			=> __( 'After', 'ninja-forms-conditionals' ),
		);
	}

	/**
	 * Process our conditional trigger
	 * 
	 * @since 1.2.8
	 * @return bool
	 */
	function compare( $value, $compare ) {
		// This space left intentionally blank.
	}


}