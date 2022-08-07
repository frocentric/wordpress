<?php
/*
Plugin Name: Query Standard Format Posts
Description: Allows you to query posts with the standard post format
Version: 0.1
Author: Mark Jaquith &amp; Andrew Nacin
Author URI: http://wordpress.org/
*/

class CWS_Query_Standard_Format_Posts {
	static $instance;

	function __construct() {
		self::$instance = $this; // So other plugins can remove our hooks
		add_action( 'init', array( &$this, 'init' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	function init() {
		add_filter( 'request', array( $this, 'request' ), 5 );
	}

	function activate() {
		if ( ! term_exists( 'post-format-standard', 'post_format' ) )
			wp_insert_term( 'post-format-standard', 'post_format' );
	}

	function request( $qvs ) {
		if ( ! isset( $qvs['post_format'] ) )
			return $qvs;
		$slugs = array_flip( get_post_format_slugs() );
		$by_raw_slug = get_post_format_slugs();
		$by_translated_slug = array_flip( $by_raw_slug );
		if ( 'standard' == $by_translated_slug[ $qvs['post_format'] ] ) {
			if ( isset( $slugs[ $qvs['post_format'] ] ) )
				$qvs['post_format'] = 'post-format-' . $slugs[ $qvs['post_format'] ];
			// If 'standard', then query every persistent format with a NOT IN instead.
			unset( $qvs['post_format'] );
			$formats = array();
			$raw_slugs = array_diff( array_keys( $by_raw_slug ), array( 'standard' ) );
			foreach ( $raw_slugs as $format ) {
				$formats[] = 'post-format-' . $format;
			}
			if ( ! isset( $qvs['tax_query'] ) )
				$qvs['tax_query'] = array();
			$qvs['tax_query'][] = array( 'taxonomy' => 'post_format', 'terms' => $formats, 'field' => 'slug', 'operator' => 'NOT IN' );
			$qvs['tax_query']['relation'] = 'AND';
			// Repair the query flags and queried object.
			add_action( 'parse_query', array( $this, 'parse_query' ) );
		}
		// Only post types that support formats should be queried.
		$tax = get_taxonomy( 'post_format' );
		$qvs['post_type'] = $tax->object_type;
		return $qvs;
	}

	function parse_query( $q ) {
		$q->is_tax = $q->is_archive = true;
		$q->is_home = false;
		$q->queried_object = get_term_by( 'slug', 'post-format-standard', 'post_format' );
		$q->queried_object_id = $q->queried_object->term_id;
	}

}

new CWS_Query_Standard_Format_Posts;
