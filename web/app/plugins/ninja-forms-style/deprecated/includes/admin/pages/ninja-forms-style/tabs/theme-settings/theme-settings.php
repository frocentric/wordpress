<?php

add_action( 'init', 'ninja_forms_register_tab_style_theme_settings' );
function ninja_forms_register_tab_style_theme_settings(){
    $args = array(
        'name' => 'Themes',
        'page' => 'ninja-forms-style',
        //'display_function' => 'ninja_forms_style_theme_display',
        'save_function' => 'ninja_forms_save_style_theme_settings',
    );
    if( function_exists( 'ninja_forms_register_tab' ) ){
        ninja_forms_register_tab( 'theme_settings', $args );
    }
}

add_action( 'init', 'ninja_forms_register_theme_settings_metabox');
function ninja_forms_register_theme_settings_metabox(){

    $plugin_settings = get_option( 'ninja_forms_settings' );
    if( isset( $plugin_settings['style'] ) ){
        $style_settings = $plugin_settings['style'];
    }else{
        $style_settings = '';
    }

    if( isset( $style_settings['default_theme'] ) ){
        $default_theme = $style_settings['default_theme'];
    }else{
        $default_theme = 'none';
    }

    $args = array(
        'page' => 'ninja-forms-style',
        'tab' => 'theme_settings',
        'slug' => 'set_theme',
        'title' => __('Default Theme', 'ninja-forms'),
        'settings' => array(
            array(
                'name' => 'default_theme',
                'type' => 'select',
                'options' => $themes,
                'default_value' => $default_theme,
                'label' => __( 'Default Theme', 'ninja-forms' ),
                'desc' => '',
            ),
        ),
    );
    if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
        ninja_forms_register_tab_metabox($args);
    }

    $args = array(
        'page' => 'ninja-forms-style',
        'tab' => 'theme_settings',
        'slug' => 'edit_themes',
        'title' => __('Edit Themes', 'ninja-forms'),
        'settings' => array(
            array(
                'name' => 'select_theme',
                'type' => 'select',
                'options' => $themes,
                'default_value' => $selected_theme,
                'label' => __( 'Select a Theme', 'ninja-forms' ),
                'desc' => '',
            ),
            array(
                'name' => 'theme_name',
                'type' => 'text',
                'label' => __( 'Theme Name (Save Theme As)', 'ninja-forms' ),
                'desc' => 'Save this theme and changes with a new name',
            ),
            array(
                'name' => 'theme_css',
                'type' => 'textarea',
                'label' => __( 'Theme CSS', 'ninja-forms' ),
                'desc' => '',
            ),
        ),
    );
    if( function_exists( 'ninja_forms_register_tab_metabox' ) ){
        ninja_forms_register_tab_metabox($args);
    }
}


function ninja_forms_style_theme_display() {
    ?>
    <select>
        <option>Theme One</option>
        <option>Theme Two</option>
        <option>Theme Three </option>
    </select>
    <?php
}