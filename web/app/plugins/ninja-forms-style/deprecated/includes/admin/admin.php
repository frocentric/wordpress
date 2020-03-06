<?php

//Add Styling to the admin menu
add_action( 'admin_menu', 'ninja_forms_add_style_menu', 99 );
function ninja_forms_add_style_menu(){

	$capabilities = 'administrator';
	$capabilities = apply_filters( 'ninja_forms_admin_menu_capabilities', $capabilities );

	$style = add_submenu_page( 'ninja-forms', __( 'Ninja Forms - Layout & Styles', 'ninja-forms-style' ), __( 'Styling', 'ninja-forms-style' ), $capabilities, 'ninja-forms-style', 'ninja_forms_admin' );
	add_action('admin_print_styles-' . $style, 'ninja_forms_admin_js');
	add_action('admin_print_styles-' . $style, 'ninja_forms_style_admin_js');
	add_action('admin_print_styles-' . $style, 'ninja_forms_admin_css');
	add_action('admin_print_styles-' . $style, 'ninja_forms_style_admin_css');
}