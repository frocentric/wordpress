<?php

final class NF_Styles_Admin_Views_PluginSettings
{
    private $dir = 'PluginSettings';

    private $data;

    public function __construct( $data = array() )
    {
        $this->data = $data;
    }

    public function get_var( $var )
    {
        if( ! isset( $this->data[ $var ] ) ) return FALSE;

        return $this->data[ $var ];
    }

    public function get_part( $part = '', $data = array() )
    {
        if( ! isset( $part ) ) return FALSE;

        NF_Styles::template( trailingslashit( $this->dir ) . $part . '.html.php', array( 'view' => $this, 'data' => $data ) );
    }

    public function get_field_name( $data )
    {
        $name = 'style[' . $this->get_var( 'tab' ) . '][' . $data[ 'section' ] . '][' . $data[ 'name' ] . ']';
        $name = apply_filters( 'ninja_forms_styles_get_plugin_setting_name', $name, $this->data[ 'tab' ], $data[ 'section' ], $data[ 'name' ] );
        return $name;
    }

    public function get_field_id( $data )
    {
        return 'style_' . $this->get_var( 'tab' ) . '_' . $data[ 'section' ] . '_' . $data[ 'name' ];
    }

    public function get_field_value( $data )
    {
        extract( $data );

        $tab = $this->data[ 'tab' ];

        if( 'error_settings' == $tab ) $tab = 'form_settings';
        if( 'datepicker_settings' == $tab ) $tab = 'form_settings';

        if( isset( $this->data[ 'plugin_settings' ][ $tab ][ $section ][ $name ] ) ){
            $value = $this->data[ 'plugin_settings' ][ $tab ][ $section ][ $name ];
        }

        $value = apply_filters( 'ninja_forms_styles_get_plugin_style', $value, $tab, $section, $name );

        return $value;
    }

}
