<?php

final class NF_ConditionalLogic_Conversion
{
	public $known_keys = array();
	
	public $fields = array();
	public $actions = array();
	public $field_conditions = array();

	public $current_id = 0;
	public $current_field;

    public function __construct()
    {
        add_filter( 'ninja_forms_after_upgrade_settings', array( $this, 'upgrade_field_settings' ) );
    }

    function upgrade_field_settings( $form_data )
    {
        if( isset( $form_data[ 'settings' ][ 'conditions' ] ) && ! empty( $form_data[ 'settings' ][ 'conditions' ] ) ) {
            return $form_data;
        }

	    $this->actions = $form_data[ 'actions' ];
	    $this->fields = $form_data[ 'fields' ];
	    
	    $all_actions = $this->extract_action_conditions( $this->actions, array() );
	    $all_fields = $this->extract_field_conditions( $this->fields, array() );
	    
	    $form_data[ 'actions' ] = $all_actions;
	    $form_data[ 'fields' ] = $all_fields;
	    $form_data[ 'settings' ][ 'conditions' ] = $this->field_conditions;

	    // echo "<pre>";
	    // print_r( $form_data[ 'actions' ] );
	    // echo "</pre>";
	    // die();

	    return $form_data;
    }

    function extract_action_conditions( $actions, $all_actions ) {
    	/*
	     * Pop the first action off of our array and check to see if it has any conditions.
	     */
	    $this->current_action = array_shift( $actions );

	    if ( isset ( $this->current_action[ 'conditions' ] ) && ! empty( $this->current_action[ 'conditions' ] ) && isset( $this->current_action[ 'conditions' ][ 0 ] ) ) {

	        /*
	         * If we have a condition, convert it for 3.0.
	         */
	        $old_condition = $this->current_action[ 'conditions' ][ 0 ];

	        $new_condition = array();
	        $new_condition[ 'process' ] = ( 'process' == $old_condition[ 'action' ] ) ? 1 : 0;
	        $new_condition[ 'connector' ] = ( 'and' == $old_condition[ 'connector' ] ) ? 'all' : 'any';
	        $connector = ( 'all' == $new_condition[ 'connector' ] ) ? 'AND' : 'OR';
	        $new_condition[ 'when' ] = $this->extract_when( $old_condition[ 'criteria' ], array(), $connector );

	        $this->current_action[ 'conditions' ] = $new_condition;
	    }

	    /*
	     * Add our action to our all actions var.
	     */
	    $all_actions[] = $this->current_action;
	    $this->current_action = array();

	    /*
	     * If there aren't any more actions, we are at the end of our array.
	     * return our conditions and all actions vars.
	     */
	    if ( 0 == count( $actions ) ) {
	        return $all_actions;
	    }

	    /*
	     * Recurse.
	     */
	    return $this->extract_action_conditions( $actions, $all_actions );
    }

    /**
	 * Rather than loop through our array, we'll use a recursive function to update everything.
	 * @since  3.0
	 * @param  array  $fields     fields array that gets modified as we recurse.
	 * @param  array  $conditions array of conditions.
	 * @param  array  $all_fields array of fields.
	 * @return array
	 */
	function extract_field_conditions( $fields, $all_fields )
	{
	    /*
	     * Pop the first field off of our array and check to see if it has any conditions.
	     */
	    $this->current_field = array_shift( $fields );

	    if ( isset ( $this->current_field[ 'conditional' ] ) && ! empty( $this->current_field[ 'conditional' ] ) ) {
	        /*
	         * If we have conditions, add them to the new condition array we are building.
	         */
	        array_walk( $this->current_field[ 'conditional' ], array( $this, 'update_field_conditions' ) );
	    }

	    /*
	     * Remove the conditional settings from this field.
	     */
	    unset( $this->current_field[ 'conditional' ] );

	    /*
	     * Add our field to our all fields var.
	     */
	    $all_fields[] = $this->current_field;
	    $this->current_field = array();

	    /*
	     * If there aren't any more fields, we are at the end of our array.
	     * return our conditions and all fields vars.
	     */
	    if ( 0 == count( $fields ) ) {
	        return $all_fields;
	    }

	    /*
	     * Recurse.
	     */
	    return $this->extract_field_conditions( $fields, $all_fields );
	}

	/**
	 * Step through each field condition and update our $this->field_conditions with each.
	 * 
	 * @since  3.0
	 * @param  array  	$field_condition  	2.9.x Field Condition
	 * @param  int  	$index              array index
	 * @return void
	 */
	function update_field_conditions( $field_condition )
	{
		/*
		 * Convert criteria to the 'when' statement of our condition.
		 */
		$when = $this->extract_when( $field_condition[ 'cr' ], array(), $field_condition[ 'connector' ] );
		/*
		 * Get our then and possibly else statements.
		 */
		$tmp = $this->extract_then_else( $field_condition );
		$then = $tmp[ 'then' ];
		$else = $tmp[ 'else' ];

		/*
		 * Check to see if this when statement exists already.
		 */
		$condition_index = $this->find_when( $when, $this->field_conditions );

		/*
		 * If it does, add this field's then/else.
		 *
		 * If it doesn't, add a new condition using this field's when/then/else.
		 */
		if ( false !== $condition_index ) {
			$this->field_conditions[ $condition_index ][ 'then' ][] = $then;
			if ( ! empty ( $else ) ) {
				$this->field_conditions[ $condition_index ][ 'else' ][] = $else;
			}
		} else {
			$this->field_conditions[] = array(
				'when' 			=> $when,
				'then' 			=> array( $then	),
				'else'			=> array( $else ),
			);
		}
		
	}

	/**
	 * Returns a 3.0 formatted array from 2.9.x criteria.
	 * 
	 * @since  3.0
	 * @param  array  	$cr_array  	2.9.x Criteria Array
	 * @param  array  	$when      	Used to create a mult-dimensional when array
	 * @param  string  	$connector 	2.9.x connector var
	 * @return array
	 */
	function extract_when( $cr_array, $when, $connector = '' )
	{
		if ( ! is_array( $cr_array ) ) return false;
		$cr = array_shift( $cr_array );
		/*
		 * Replace our field target with the appropriate key
		 *
		 * This criterion could contain either a 'field' key or a 'param' key.
		 */

		$field_id = ( isset ( $cr[ 'field' ] ) ) ? $cr[ 'field' ] : $cr[ 'param' ];
		$field_key = $this->get_key( $field_id );
		$comparator = ( isset ( $cr[ 'operator' ] ) ) ? $cr[ 'operator' ] : $cr[ 'compare' ];
		
		$when[] = array(
			'connector'		=> strtoupper( $connector ),
			'key'			=> $field_key,
			'comparator'	=> $this->convert_comparator( $comparator, $field_id ),
			'value'			=> $this->convert_value( $cr[ 'value' ] ),
            'type'          => $this->get_when_type( $field_id )
		);
	
		if ( 0 == count( $cr_array ) ) {
			/*
			 * Return our when array
			 */
			return $when;
		}

		/*
		 * Recurse
		 */
		return $this->extract_when( $cr_array, $when, $connector );
	}

	/**
	 * Return 3.0 formatted then/else arrays.
	 * 
	 * @since  3.0
	 * @param  array  	$condition 		2.9.x formatted condition array
	 * @return array             		3.0 formatted then/else
	 */
	function extract_then_else( $condition )
	{
		/*
		 * We have new names for some of our actions.
		 */
		switch( $condition[ 'action' ] ) {
			case 'show':
				$trigger = 'show_field';
				$else_trigger = 'hide_field';
				break;
			case 'hide':
				$trigger = 'hide_field';
				$else_trigger = 'show_field';
				break;
			case 'add_value':
				$trigger = $this->convert_trigger( $condition );
				$else_trigger = 'hide_option';
				break;
			case 'remove_value':
				$trigger = $this->convert_trigger( $Condition );
				$else_trigger = 'show_option';
			default:
				$trigger = $condition[ 'action' ];
				$else_trigger = false;
				break;
		}

		$value = $this->convert_value( $condition[ 'value' ] );

		$then = array( 'key' => $this->current_field[ 'key' ], 'trigger' => $trigger, 'value' => $value, 'type' => 'field' );

		if ( $else_trigger ) {
			$else = array( 'key' => $this->current_field[ 'key' ], 'trigger' => $else_trigger, 'value' => $value, 'type' => 'field' );
		} else {
			$else = array();
		}

		return array( 'then' => $then, 'else' => $else );
	
	}

	/**
	 * Search $conditions array for $when
	 * 
	 * @since  3.0
	 * @param  array  		$when       	Needle
	 * @param  array  		$conditions 	Haystack
	 * @return int/bool              		Index or Boolean
	 */
	function find_when( $when, $conditions )
	{
		foreach( $conditions as $index => $condition ) {
			if ( $condition[ 'when' ] == $when ) {
				return $index;
			}
		}
		
		return false;
	}

	/**
	 * Get our 3.0 field key from our 2.9.x field ID
	 * 
	 * @since  3.0
	 * @param  int  	$id 	2.9.x field ID
	 * @return string
	 */
	function get_key( $id )
	{
		return ( isset( $this->known_keys[ $id ] ) ) ? $this->known_keys[ $id ] : $this->find_key( $id );
	}

	/**
	 * Search for a field key by ID
	 * 
	 * @since  3.0
	 * @param  int  	$id 	2.9.x field ID
	 * @return string
	 */
	function find_key( $id )
	{
		$this->current_id = $id;
        $field = array_filter( $this->fields, array( $this, 'filter_by_id' ) );
		$field = array_shift( $field );
		$this->current_id = 0;
		$this->known_keys[ $id ] = $field[ 'key' ];
		return isset( $field[ 'key' ] ) ? $field[ 'key' ] : false;
	}

	/**
	 * Filter function used by the array_filter call inside find_key
	 * 
	 * @since  3.0
	 * @param  array  	$val 	field array
	 * @return bool
	 */
	function filter_by_id( $val )
	{
		return $val[ 'id' ] == $this->current_id;
	}

	/**
	 * Convert 2.9.x comparators to 3.0 format.
	 * 
	 * @since  3.0
	 * @param  string  	$comparator 		2.9.x format comparator
	 * @return string
	 */
	function convert_comparator( $comparator, $field_id )
	{
		$current_id = $this->current_id;
		$this->current_id = $field_id;
        $field = array_filter( $this->fields, array( $this, 'filter_by_id' ) );
		$field = array_shift( $field );
		$this->current_id = $current_id;

		switch ( $comparator ) {
			case '==':
				/*
				 * If we have a list field, we want to use "contains" and "notcontains" instead of "equal" and "notequal"
				 */
				if ( 'listselect' == $field[ 'type' ] || 'listradio' == $field[ 'type' ] || 'listcheckbox' == $field[ 'type' ] || 'listmultiselect' == $field[ 'type' ] ) {
					return 'contains';
				} else {
					return 'equal';
				}
			case '!=':
				/*
				 * If we have a list field, we want to use "contains" and "notcontains" instead of "equal" and "notequal"
				 */
				if ( 'listselect' == $field[ 'type' ] || 'listradio' == $field[ 'type' ] || 'listcheckbox' == $field[ 'type' ] || 'listmultiselect' == $field[ 'type' ] ) {
					return 'notcontains';
				} else {
					return 'notequal';
				}
			case '<':
				return 'less';
			case '>':
				return 'greater';
			default:
				return $comparator;
		}
	}

	/**
	 * Some of our values, like checkboxes, should be 1 or 0 instead of checked or unchecked.
	 * 
	 * @since  3.0
	 * @param  mixed  	$value
	 * @return mixed
	 */
	function convert_value( $value )
	{
		switch( $value ) {
			case 'checked':
				$value = 1;
				break;
			case 'unchecked':
				$value = 0;
				break;
		}

		return $value;
	}

	/**
	 * Some actions, like "change_value" for list fields need to be converted to new triggers.
	 * @since  3.0
	 * @param  string  $action 2.9.x action
	 * @return string          3.0 trigger
	 */
	function convert_trigger( &$condition ) {
		if (
			'listselect' 	!= $this->current_field[ 'type' ] &&
			'listradio'  	!= $this->current_field[ 'type' ] &&
			'listcheckbox'	!= $this->current_field[ 'type' ]
		) {
			return $condition[ 'action' ];
		}

		switch( $condition[ 'action' ] ) {
			case 'change_value':
				return 'select_option';
			case 'add_value':
				$this->current_field[ 'options' ][] = array(
					'label' 	=> $condition[ 'value' ][ 'label' ],
					'value'		=> $condition[ 'value' ][ 'label' ],
					'calc'		=> $condition[ 'value' ][ 'calc' ],
					'selected'	=> $condition[ 'value' ][ 'selected' ]
				);
				$condition[ 'value' ] = $condition[ 'value' ][ 'label' ];
				return 'show_option';
			case 'remove_value':
				return 'hide_option';
		}
	}

	function get_when_type( $field_id )
    {
        foreach( $this->fields as $field ){
            if( $field_id != $field[ 'id' ] ) continue;
            return ( 'calc' != $field[ 'type' ] ) ? 'field' : 'calc';
        }

        return 'field';
    }
}

new NF_ConditionalLogic_Conversion();