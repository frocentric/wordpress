<?php

final class NF_ConditionalLogic_FieldsCollection
{
    private $fields;

    public function __construct( $submitted_fields = array(), $form_id, $is_preview = false )
    {

        // If we're in preview mode, trust the field data passed in, because it isn't saved to the DB.
        if ( $is_preview ) {
            $field_list = $submitted_fields;
        } else {
            // Grab our fields from our db.
            $field_list = Ninja_Forms()->form( $form_id )->get_fields();
        }

        $user_supplied_values = apply_filters( 'ninja_forms_user_submitted_data_types', array(
            'value',
            'files'
        ) );

        // loop over our DB fields and update the "value" setting for our submitted values.
        foreach( $field_list as $field ){
            // If we don't already have a field object, fetch one.
            if ( is_object( $field ) ) {
                $fieldModel = $field;
            } else {
                $fieldModel = Ninja_Forms()->form( $form_id )->get_field( $field[ 'id' ] );
            }
            $field_settings = $fieldModel->get_settings(); // Initialized field settings from the database, if needed.
            $field_id = $fieldModel->get_id();

            foreach( $user_supplied_values as $key ) {

                // Check for a submitted value. If that doesn't exist, fallback to default value. Otherwise, set to NULL.
                if( isset( $submitted_fields[ $field_id ][ $key ] ) ){
                    $field_value = $submitted_fields[ $field_id ][ $key ];
                } elseif( isset( $field_settings[ $key ] ) ){
                    $field_value = $field_settings[ $key ];
                } else {
                    $field_value =  null;
                }

                $fieldModel->update_setting( $key, $field_value );
            }

            // If we are in preview mode, trust the data sent by the user.
            // This allows us to preview changes before they are saved to the DB.
            if ( $is_preview ) {
                if( $fieldModel->get_tmp_id() && isset( $field[ 'key' ] ) ){
                    $fieldModel->update_setting( 'key', $field[ 'key' ] );
                }

                if( isset( $field[ 'settings' ] ) ) {
                    $settings = array_merge( $field[ 'settings' ], $fieldModel->get_settings());
                } else {
                    $settings = array_merge( $field, $fieldModel->get_settings());
                }

                $fieldModel->update_settings( $settings );                
            }

            $this->fields[] = $fieldModel;
        }
    }

    public function get_field( $key_or_id )
    {
        $property = ( is_numeric( $key_or_id ) ) ? 'id' : 'key';
        foreach( $this->fields as $field ){
            $setting = $field->get_setting( $property );
            if( $key_or_id == $setting ) return $field;
        }
        return Ninja_Forms()->form()->field()->get();
    }

    public function to_array()
    {
        $fields = array();
        foreach( $this->fields as $field ){
            $settings = $field->get_settings();
            $settings[ 'id' ] = ( $field->get_tmp_id() ) ? $field->get_tmp_id() : $field->get_id();
            $fields[ $field->get_id() ] = $settings;
        }
        return $fields;
    }
}
