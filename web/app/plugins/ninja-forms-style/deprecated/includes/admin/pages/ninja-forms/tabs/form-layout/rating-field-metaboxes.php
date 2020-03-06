<?php

add_action( 'init', 'ninja_forms_style_rating_metaboxes', 9 );
function ninja_forms_style_rating_metaboxes(){
	add_action( 'ninja_forms_style_field_metaboxes', 'ninja_forms_style_modify_rating_metaboxes' );
	//if( is_admin() ){
		ninja_forms_style_add_rating_metaboxes();
	//}
}

function ninja_forms_style_modify_rating_metaboxes( $field_id ){
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	if( $field_type == '_rating' ){
		$args = array( 'page' => 'field' );
		// ninja_forms_unregister_style_metabox( 'field', $args );
		ninja_forms_style_add_rating_metaboxes();
	}

}

function ninja_forms_style_add_rating_metaboxes(){

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'rating_field_item',
		'field_type' => '_rating',
		'title' => __( 'Rating Item', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap div.ninja-forms-star-rating a',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'rating_field_item', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'rating_field_hover',
		'field_type' => '_rating',
		'title' => __( 'Rating Item Hover', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap div.ninja-forms-star-rating-hover a',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'rating_field_hover', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'rating_field_selected',
		'field_type' => '_rating',
		'title' => __( 'Rating Item Selected', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap div.ninja-forms-star-rating-on a',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'rating_field_selected', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'rating_field_cancel',
		'field_type' => '_rating',
		'title' => __( 'Cancel Ratings', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap div.rating-cancel a',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'rating_field_cancel', $args );

	$args = array(
		'page' => 'field',
		'tab' => 'form_layout',
		'slug' => 'rating_field_cancel_hover',
		'field_type' => '_rating',
		'title' => __( 'Cancel Ratings Hover', 'ninja-forms-style' ),
		'state' => 'closed',
		'display_function' => 'ninja_forms_style_field_metabox_output',
		'save_page' => 'field',
		'css_selector' => '#ninja_forms_field_[field_id]_div_wrap div.rating-cancel a:hover',
		//'css_exclude' => array( 'float', 'padding', 'margin' ),
	);

	ninja_forms_register_style_metabox( 'rating_field_cancel_hover', $args );

}