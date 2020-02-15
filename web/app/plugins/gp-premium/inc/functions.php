<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

function generate_premium_get_media_query( $name ) {
	if ( function_exists( 'generate_get_media_query' ) ) {
		return generate_get_media_query( $name );
	}

	// If the theme function doesn't exist, build our own queries.
	$desktop = apply_filters( 'generate_desktop_media_query', '(min-width:1025px)' );
	$tablet = apply_filters( 'generate_tablet_media_query', '(min-width: 769px) and (max-width: 1024px)' );
	$mobile = apply_filters( 'generate_mobile_media_query', '(max-width:768px)' );
	$mobile_menu = apply_filters( 'generate_mobile_menu_media_query', $mobile );

	$queries = apply_filters( 'generate_media_queries', array(
		'desktop' 		=> $desktop,
		'tablet' 		=> $tablet,
		'mobile' 		=> $mobile,
		'mobile-menu' 	=> $mobile_menu,
	) );

	return $queries[ $name ];
}