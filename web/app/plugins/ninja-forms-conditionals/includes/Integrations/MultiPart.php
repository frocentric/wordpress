<?php

final class NF_ConditionalLogic_Integrations_MultiPart
{
    public function __construct()
    {
        add_filter( 'ninja_forms_conditional_logic_triggers', array( $this, 'register_triggers' ), 10, 1 );
        add_filter( 'ninja_forms_conditional_logic_trigger_type_part', array( $this, 'get_part' ), 10, 2 );
    }

    public function register_triggers( $triggers )
    {
        $triggers[ 'hide_part' ] = array(
            'key'      => 'hide_part',
            'label'    => __( 'Hide Part', 'ninja-forms-conditional-logic' ),
            'instance' => new NF_ConditionalLogic_Triggers_HidePart()
        );

        $triggers[ 'show_part' ] = array(
            'key'      => 'show_part',
            'label'    => __( 'Show Part', 'ninja-forms-conditional-logic' ),
            'instance' => new NF_ConditionalLogic_Triggers_ShowPart()
        );

        return $triggers;
    }

    public function get_part( $key, $data )
    {
        $form = Ninja_Forms()->form( $data['id'] )->get();

        if( isset( $data[ 'settings' ][ 'is_preview' ] ) && $data[ 'settings' ][ 'is_preview' ] ){
            $form_settings = get_user_option( 'nf_form_preview_' . $data['form_id'] );
            $form->update_settings( $form_settings );
        }

        $formContentData =  $form->get_setting( 'formContentData' );

        if( ! $formContentData ) return false;

        $parts = array();
        foreach( $formContentData as $content ){
            if( 'part' != $content[ 'type' ] || $key != $content[ 'key' ] ) continue;
            array_push( $parts, $content );
        }

        if( ! is_array( $parts ) ) return false;

        return array_shift( $parts );
    }

    public static function extract_field_keys( $part )
    {
        if( ! isset( $part[ 'formContentData' ] ) ) return array();
        $field_keys = array();
        foreach( $part[ 'formContentData' ] as $content ){
            if( is_string( $content ) ){
                array_push( $field_keys, $content );
            } else {
                $field_keys = array_merge($field_keys, self::extract_field_keys_from_layout( $content ) );
            }
        }
        return $field_keys;
    }

    public static function extract_field_keys_from_layout( $content )
    {
        if( ! isset( $content[ 'cells' ] ) ) return array();

        $field_keys = array();
        foreach( $content[ 'cells' ] as $cell ){
            if( ! isset( $cell[ 'fields' ] ) ) continue;
            $field_keys = array_merge( $field_keys, $cell[ 'fields' ] );
        }

        return $field_keys;
    }


}
