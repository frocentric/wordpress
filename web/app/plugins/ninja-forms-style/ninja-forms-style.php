<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Layout & Styles
 * Plugin URI: https://ninjaforms.com/extensions/layout-styles/
 * Description: Form layout and styling add-on for Ninja Forms.
 * Version: 3.0.29
 * Author: The WP Ninjas
 * Author URI: http://ninjaforms.com
 * Text Domain: ninja-forms-layout-styles
 *
 * Copyright 2016 The WP Ninjas.
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 */

if( ! class_exists( 'NF_Layouts_Conversion', false ) ) {
    require_once 'lib/conversion.php';
}

if( ! defined( 'NINJA_FORMS_STYLE_VERSION' ) ) {
    define("NINJA_FORMS_STYLE_VERSION", "3.0.29");
}

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    if( ! defined( 'NINJA_FORMS_STYLE_DIR' ) ) {
        define("NINJA_FORMS_STYLE_DIR", plugin_dir_path(__FILE__) . '/deprecated');
    }

    if( ! defined( 'NINJA_FORMS_STYLE_URL' ) ) {
        define("NINJA_FORMS_STYLE_URL", plugin_dir_url(__FILE__) . '/deprecated');
    }

    include 'deprecated/ninja-forms-style.php';

} else {

    include 'layouts/ninja-forms-layouts.php';
    if( ! function_exists( 'NF_Layouts' ) ) {
        function NF_Layouts() { return NF_Layouts::instance(); }
    }
    NF_Layouts();

    include 'styles/ninja-forms-styles.php';
    if( ! function_exists( 'NF_Styles' ) ) {
        function NF_Styles() { return NF_Styles::instance(); }
    }
    NF_Styles();

    add_action( 'admin_init', 'ninja_forms_layout_styles_setup_license' );
    if( ! function_exists( 'ninja_forms_layout_styles_setup_license' ) ) {
        function ninja_forms_layout_styles_setup_license()
        {
            if (!class_exists('NF_Extension_Updater')) return;

            new NF_Extension_Updater('Layout and Styles', NINJA_FORMS_STYLE_VERSION, 'WP Ninjas', __FILE__, 'style');
        }
    }
}

add_filter( 'ninja_forms_upgrade_settings', 'ninja_forms_styles_upgrade_form_settings' );
if( ! function_exists( 'ninja_forms_styles_upgrade_form_settings' ) ) {
    function ninja_forms_styles_upgrade_form_settings( $data ){

        if( ! isset( $data[ 'settings' ][ 'style' ][ 'groups' ] ) ) return $data;

        foreach( $data[ 'settings' ][ 'style' ][ 'groups' ] as $group => $settings ){

            if( 'field' == $group ) $group = 'element';

            foreach( $settings as $setting => $value ){
                $setting = $group . '_styles_' . $setting;
                $data[ 'settings' ][ $setting ] = $value;
            }
        }

        return $data;
    }
}

add_filter( 'ninja_forms_upgrade_settings', 'ninja_forms_styles_upgrade_plugin_settings' );
if( ! function_exists( 'ninja_forms_styles_upgrade_plugin_settings' ) ) {
    function ninja_forms_styles_upgrade_plugin_settings( $data ){
        return $data;
    }
}

add_filter( 'ninja_forms_upgrade_field', 'ninja_forms_styles_upgrade_field_settings' );
if( ! function_exists( 'ninja_forms_styles_upgrade_field_settings' ) ) {
    function ninja_forms_styles_upgrade_field_settings( $data ){

        if( ! isset( $data[ 'style' ][ 'groups' ] ) ) return $data;

        foreach( $data[ 'style' ][ 'groups' ] as $group => $settings ){

            if( 'field' == $group ) $group = 'element';

            foreach( $settings as $setting => $value ){
                $setting = $group . '_styles_' . $setting;
                $data[ $setting ] = $value;
            }
        }

        unset( $data[ 'style' ] );

        return $data;
    }
}
