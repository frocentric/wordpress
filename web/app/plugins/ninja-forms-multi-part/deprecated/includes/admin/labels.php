<?php

/**
 * Add label options for the next and previous buttons
 *
 */

function nf_mp_labels(){

	$args = array(
		'page' => 'ninja-forms-settings',
		'tab' => 'label_settings',
		'slug' => 'mp_labels',
		'title' => __( 'Multi-Part Labels', 'ninja-forms-mp' ),
		'settings' => array(
			array(
				'name' => 'mp_previous',
				'type' => 'text',
				'label' => __( 'Previous Button Label', 'ninja-forms-mp' ),
				'desc' => '',
				'default_value' => __( 'Previous', 'ninja-forms-mp' ),
			),			
			array(
				'name' => 'mp_next',
				'type' => 'text',
				'label' => __( 'Next Button Label', 'ninja-forms-mp' ),
				'desc' => '',
				'default_value' => __( 'Next', 'ninja-forms-mp' ),
			),
		),
	);

	if ( function_exists( 'ninja_forms_register_tab_metabox' ) ) {
		ninja_forms_register_tab_metabox( $args );
	}
}

add_action( 'init', 'nf_mp_labels', 11 );

/**
 * Add a filter to our nf_get_settings function to give the previous and next buttons a default value.
 *
 */

function nf_mp_labels_filter( $settings ) {
	$settings['mp_previous'] = isset ( $settings['mp_previous'] ) ? $settings['mp_previous'] : __( 'Previous', 'ninja-forms-mp' );
	$settings['mp_next'] = isset ( $settings['mp_next'] ) ? $settings['mp_next'] : __( 'Next', 'ninja-forms-mp' );
	return $settings;
}

add_filter( 'ninja_forms_settings', 'nf_mp_labels_filter' );