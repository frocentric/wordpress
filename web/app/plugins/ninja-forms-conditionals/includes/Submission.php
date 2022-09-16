<?php

final class NF_ConditionalLogic_Submission
{
    private $fieldsCollection;

    public function __construct()
    {
        add_filter( 'ninja_forms_submit_data', array( $this, 'parse_fields' ) );
        add_filter( 'ninja_forms_pre_validate_field_settings', array( $this, 'before_validate_field' ) );

        add_filter( 'ninja_forms_submission_actions', array( $this, 'parse_actions' ), 10, 2 );
        add_filter( 'ninja_forms_submission_actions_preview', array( $this, 'parse_actions' ), 10, 2 );
    }

    public function parse_fields( $data )
    {
        // If we don't have a form ID, return early.
        if ( ! isset ( $data[ 'id' ] ) || empty( $data[ 'id' ] ) ) {
            return $data;
        }

        if ( ( isset( $data[ 'settings' ][ 'is_preview' ] ) && ! empty( $data[ 'settings' ][ 'is_preview' ] ) )
            && current_user_can( apply_filters( 'ninja_forms_admin_all_forms_capabilities', 'manage_options' ) ) 
        ) {
            $is_preview = true;
        } else {
            $is_preview = false;
        }

        // If the user is in Preview Mode, grab conditions from the submitted data. Otherwise, pull data from the DB.
        if ( $is_preview ) {
            $form_id = esc_html( $data[ 'id' ] );
            $form_settings = $data[ 'settings' ];
        } else {
            $form_id = absint( $data[ 'id' ] );
            // Grab conditions from form settings rather than relying on $data, as that's merged with user submitted data.
            $form_settings = Ninja_Forms()->form( $form_id )->get_settings();
        }

        // Make sure we build the fieldsCollection, in case there are conditionally triggered Actions.
        $this->fieldsCollection = new NF_ConditionalLogic_FieldsCollection( $data[ 'fields' ], $data[ 'id' ], $is_preview );

        // If we don't have any conditions set on this form, return $data.
        if( ! isset( $form_settings[ 'conditions' ] ) || empty( $form_settings[ 'conditions' ] ) ) {
            return $data;
        }

        foreach( $data[ 'settings' ][ 'conditions' ] as $condition ){
            $condition = new NF_ConditionalLogic_ConditionModel( $condition, $this->fieldsCollection, $data );
            $condition->process();
        }

        $this->fieldsCollection = apply_filters( 'ninja_forms_conditional_logic_parse_fields', $this->fieldsCollection );   
        $data[ 'fields' ] = $this->fieldsCollection->to_array();

        return $data;
    }

    public function before_validate_field( $field_settings )
    {
        if( ! isset( $field_settings[ 'conditionally_required' ] ) ) return $field_settings;

        $field_settings[ 'required' ] = $field_settings[ 'conditionally_required' ];

        unset( $field_settings[ 'conditionally_required' ] );

        return $field_settings;
    }

    public function parse_actions( $actions, $form_data )
    {
        // If we don't have a fieldsCollection (such as if this is a resume) skip this filter.
        if ( ! empty( $this->fieldsCollection ) ) {
            array_walk( $actions, array( $this, 'parse_action' ), $this->fieldsCollection );
        }

        return $actions;
    }

    public function parse_action( &$action, $key, $fieldsCollection )
    {
        if( ! isset( $action[ 'settings' ][ 'active' ] ) || ! $action[ 'settings' ][ 'active' ] ) return;
        
        // Checks to see if conditional setting is in the default position and returns early if it is. 
        if( 0 !== intval( $action[ 'settings' ][ 'conditions' ][ 'process' ] )
            && 1 !== intval( $action[ 'settings' ][ 'conditions' ][ 'process' ] ) ) return;
        if( empty( $action[ 'settings' ][ 'conditions' ][ 'when' ] ) ) return;

        $action_condition = ( is_object( $action ) ) ? $action->get_setting( 'conditions' ) : $action[ 'settings' ][ 'conditions' ];

        if( ! $action_condition ) return;

        unset( $action_condition[ 'then' ] );
        unset( $action_condition[ 'else' ] );

        $valid_conditions = array(); 

        $input_field_types = array(
            'address',
            'address2',
            'confirm',
            'date',
            'email',
            'firstname',
            'lastname',
            'number',
            'password',
            'passwordconfirm',
            'phone', 
            'textbox',
            'textarea',
            'hidden',
        );

        $field_collection_array = $this->fieldsCollection->to_array();

        foreach( $action_condition[ 'when' ] as &$when ){
            $when[ 'connector' ] = ( 'all' == $action_condition[ 'connector' ] ) ? 'AND' : 'OR';
            $field_type = $this->get_field_type( $when[ 'key' ], $field_collection_array  );
            
            // Input field conditions are valid, even with empty values
            $is_input_field = in_array( $field_type, $input_field_types );
            $is_valid = $this->validate_action_when_value( $when );
           
            if ( $is_input_field || $is_valid ) {
                $valid_conditions[] = $when; 
            }
        }

        // Merge our valid conditions back into our action conditions array. 
        $action_condition[ 'when' ] = $valid_conditions;

        $default = ( 'all' == $action_condition[ 'connector' ] );

        $condition = new NF_ConditionalLogic_ConditionModel( $action_condition, $fieldsCollection, array(), $default );
        $result = $condition->process();

        if( 1 != $action_condition[ 'process' ] ) {
            $result = ! $result;
        }

        if( is_object( $action ) ){
            $action->update_setting( 'active', $result );
        } else {
            $action[ 'settings' ][ 'active' ] = $result;
        }
    }

    /**
     * Validates if our when condition has a valid value. 
     * 
     * @return bool
     */
    public function validate_action_when_value( $when_condition ) 
    {
        if( empty( $when_condition[ 'value' ] ) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Compares our field key to our field model to get the field type.
     * 
     * @return string - field type. 
     */
    public function get_field_type( $field_key, $field_models ) 
    {
        foreach( $field_models as $field ) {
            if( $field_key == $field[ 'key' ] ) {
                return $field[ 'type' ];
            }
        }  
    }
}