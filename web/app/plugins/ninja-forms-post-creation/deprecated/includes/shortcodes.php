<?php

function nf_post_link_shortcode( $atts ){
	global $ninja_forms_processing;

	$post_id = isset ( $atts['post_id'] ) ? $atts['post_id'] : 0;
	$method = isset ( $atts['method'] ) ? $atts['method'] : 'link';

	if ( $post_id == 0 ) {
		if ( isset ( $ninja_forms_processing ) ) {
			$post_id = $ninja_forms_processing->get_form_setting( 'post_id' );
		} else {
			return false;
		}
	}

	$url = get_permalink( $post_id );

	switch ( $method ) {
		case 'link':
			$title = get_the_title( $post_id );		
			$link_text = isset ( $atts['link_text'] ) ? $atts['link_text'] : $title;
			$return = '<a href="' . $url . '">' . $link_text . '</a>';
			break;
		default:
			$return = $url;
			break;
	}


	return $return;
}

add_shortcode( 'nf_post_link', 'nf_post_link_shortcode' );