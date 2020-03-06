<?php if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NF_Abstracts_Submenu' ) ) return;

final class NF_Styles_Admin_Submenu extends NF_Abstracts_Submenu
{
    public $parent_slug = 'ninja-forms';

    public $page_title = 'Styling';

    public $priority = 11.5;

    public function __construct()
    {
        parent::__construct();

        if( isset( $_POST[ 'update_ninja_forms_style_settings' ] ) ){
            add_action( 'init', array( $this, 'update' ) );
        }

        add_filter( 'ninja_forms_style_field_type', array( $this, 'filter_field_type_name' ), 10, 1) ;
        add_filter( 'ninja_forms_styles_get_plugin_style', array( $this, 'filter_get_plugin_style' ), 10, 4 );
        add_filter( 'ninja_forms_styles_get_plugin_setting_name', array( $this, 'filter_get_plugin_setting_name' ), 10, 4 );

        if( isset( $_POST[ 'nuke_styles' ] ) && $_POST[ 'nuke_styles' ] ){
            add_action( 'admin_init', array( $this, 'developer_nuke_styles' ) );
        }
    }

    public function display()
    {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'nf-codemirror', Ninja_Forms::$url . 'assets/css/codemirror.css' );
        wp_enqueue_style( 'ninja_forms_styles_admin_css', NF_Styles::$url . 'assets/css/admin.css', array(), false );

        wp_enqueue_script( 'postbox' );
        wp_enqueue_script( 'nf-codemirror', Ninja_Forms::$url . 'assets/js/lib/codemirror.min.js' );
        wp_enqueue_script( 'ninja_forms_styles_admin_js', NF_Styles::$url . 'assets/js/admin.js', array( 'wp-color-picker', 'postbox', 'nf-codemirror' ), false, true );

        $tab = ( isset( $_GET[ 'tab' ] ) ) ? WPN_Helper::sanitize_text_field( $_GET[ 'tab' ] ) : 'form_settings';
        $groups = NF_Styles::config( 'PluginSettingGroups' );
        
        if( ! class_exists( 'NF_MultiPart', false ) ) {
            unset( $groups[ 'multipart_settings' ] );
        }

        if( 'field_type' == $tab ) {

            if( isset( $_GET[ 'field_type' ] ) ) {

                $field_type = WPN_Helper::sanitize_text_field($_GET['field_type']);

                if (isset(Ninja_Forms()->fields[ $field_type ])) {

                    $field = Ninja_Forms()->fields[ $field_type ];

                    $sections = NF_Styles::config( 'FieldTypeSections' );

                    foreach ($sections as $section) {

                        if( isset( $section[ 'except' ] ) && in_array( $field->get_name(), $section[ 'except' ] ) ) continue;
                        if( isset( $section[ 'only' ] ) && ! in_array( $field->get_name(), $section[ 'only' ] ) ) continue;

                        $name = $field->get_name() . "_" . $section[ 'name' ];

                        $groups[ 'field_type' ][ 'sections' ][ $name ] = array(
                            'name' => $name,
                            'field' => $field->get_name(),
                            'label' => $field->get_nicename() . ' ' . $section[ 'label' ]
                        );
                    }
                }
            }
        }

        $sections = $groups[ $tab ][ 'sections' ];
        $plugin_settings = Ninja_Forms()->get_setting( 'style' );

        foreach( $sections as $section_id => $section ){
            $settings = NF_Styles::config( 'CommonSettings' );
            unset( $settings[ 'show_advanced_css' ] );

            if( 'listselect_element' == $section_id && Ninja_Forms()->get_setting( 'opinionated_styles' ) ){
                unset( $settings[ 'float' ] );
                unset( $settings[ 'display' ] );
            }

            if( in_array( $section_id, array( 'checkbox_element', 'listcheckbox_element', 'listcheckbox_list-item-element' ) )
                && Ninja_Forms()->get_setting( 'opinionated_styles' ) ){
                unset( $settings[ 'float' ] );
                unset( $settings[ 'display' ] );
                unset( $settings[ 'height' ] );
                unset( $settings[ 'width' ] );
                unset( $settings[ 'padding' ] );
                unset( $settings[ 'margin' ] );
            }

            if( 'datepicker_container' == $section_id ){
                unset( $settings[ 'padding' ] );
            }

            if( in_array( $section_id, array( 'datepicker_container', 'datepicker_header', 'datepicker_week', 'datepicker_days', 'datepicker_prev', 'datepicker_next' ) ) ){
                unset( $settings[ 'margin' ] );
                unset( $settings[ 'float' ] );
            }

            foreach( $settings as $name => $setting ){
                $settings[ $name ][ 'section' ] = $section_id;
            }

            $sections[ $section_id ][ 'settings' ] = $settings;
        }

        $url = remove_query_arg( 'field_type' );

        $view = new NF_Styles_Admin_Views_PluginSettings( compact( 'tab', 'groups', 'sections', 'url', 'plugin_settings' ) );

        NF_Styles::template( 'PluginSettings/index.html.php', compact( 'view' ) );
    }

    public function update()
    {
        if( ! current_user_can( apply_filters( 'ninja_forms_styles_can_update_styles', 'manage_options' ) ) ) return;

        if( ! isset( $_POST[ 'style' ] ) ) return;

        $data = WPN_Helper::sanitize_text_field( $_POST[ 'style' ] );

        $group = WPN_Helper::get_query_string( 'tab', 'form_settings' );

        $settings = Ninja_Forms()->get_setting( 'style' );

        if( ! isset( $settings[ $group ] ) ) $settings[ $group ] = array();

        if( 'field_type' == $group ){
            $settings[ 'field_type' ] = array_merge( $settings[ 'field_type' ], $data[ $group ] );
        } elseif( 'error_settings' == $group || 'datepicker_settings' == $group ) {
            if( ! isset( $settings[ 'form_settings' ] ) ) $settings[ 'form_settings' ] = array();
            $settings[ 'form_settings' ] = array_merge( $settings[ 'form_settings' ], $data[ $group ] );
        } else {
            $settings[$group] = apply_filters('ninja_forms_styles_updates_' . $group, $data[$group]);
        }

        Ninja_Forms()->update_setting( 'style', $settings );
        do_action( 'ninja_forms_styles_update_styles', $settings );
    }

    public function filter_get_plugin_style( $value, $tab, $section, $name )
    {
        if( 'field_type' != $tab ) return $value;

        $plugin_settings = Ninja_Forms()->get_setting( 'style' );
        
        if( false !== strpos( $section, 'file_upload' ) ) {
            $stack = explode( '_', $section );
            $section = '_' . $stack[1];
            $subsection = $stack[3];
        } else {
            list( $section, $subsection ) = explode( '_', $section );
        }

        $section = apply_filters( 'ninja_forms_style_field_type', $section );

        // TODO: What is this block of code for?
        // Seems unnecessary.
        /*if( 'html' == $section ) {
            $section = '_desc';
            $subsection = 'desc_field';
        }*/

        if( isset( $plugin_settings[ $tab ][ $section ][ $subsection ][ $name ] ) ){
            $value = $plugin_settings[ $tab ][ $section ][ $subsection ][ $name ];
        }

        return $value;
    }
    
    public function filter_get_plugin_setting_name( $name, $tab, $section, $name_raw )
    {
        if( 'field_type' != $tab ) return $name;
        if( false !== strpos( $section, 'file_upload' ) ) {
            $stack = explode( '_', $section );
            $section = '_' . $stack[1];
            $subsection = $stack[3];
        } else {
            list( $section, $subsection ) = explode( '_', $section );
        }

        $section = apply_filters( 'ninja_forms_style_field_type', $section );

        if( 'desc' == $subsection ) $subsection = 'desc_field';

        return 'style[' . $tab . '][' . $section . '][' . $subsection . '][' . $name_raw . ']';
    }

    public function filter_field_type_name( $name, $flip = FALSE )
    {
        $lookup = NF_Styles::config( 'FieldTypeLookup' );

        if( $flip ) $lookup = array_flip( $lookup );

        return ( isset( $lookup[ $name ] ) ) ? $lookup[ $name ] : $name;
    }

    public function developer_nuke_styles()
    {
        Ninja_Forms()->update_setting( 'style', array() );
    }

}
