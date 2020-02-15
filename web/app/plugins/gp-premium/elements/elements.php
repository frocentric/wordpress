<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

require plugin_dir_path( __FILE__ ) . 'class-elements-helper.php';
require plugin_dir_path( __FILE__ ) . 'class-hooks.php';
require plugin_dir_path( __FILE__ ) . 'class-hero.php';
require plugin_dir_path( __FILE__ ) . 'class-layout.php';
require plugin_dir_path( __FILE__ ) . 'class-conditions.php';
require plugin_dir_path( __FILE__ ) . 'class-post-type.php';

add_action( 'wp', 'generate_premium_do_elements' );
/**
 * Execute our Elements.
 *
 * @since 1.7
 */
function generate_premium_do_elements() {
	$args = array(
		'post_type'     	=> 'gp_elements',
		'no_found_rows' 	=> true,
		'post_status'   	=> 'publish',
		'numberposts'		=> 500,
		'fields'			=> 'ids',
		'order'				=> 'ASC',
		'suppress_filters'  => false,
	);

	// Prevent Polylang from altering the query.
	if ( function_exists( 'pll_get_post_language' ) ) {
		$args['lang'] = '';
	}

	$posts = get_posts( $args );

	foreach ( $posts as $post_id ) {
		$post_id = apply_filters( 'generate_element_post_id', $post_id );
		$type = get_post_meta( $post_id, '_generate_element_type', true );

		if ( 'hook' === $type ) {
			new GeneratePress_Hook( $post_id );
		}

		if ( 'header' === $type && ! GeneratePress_Hero::$instances ) {
			new GeneratePress_Hero( $post_id );
		}

		if ( 'layout' === $type ) {
			new GeneratePress_Site_Layout( $post_id );
		}
	}
}

add_filter( 'generate_dashboard_tabs', 'generate_elements_dashboard_tab' );
/**
 * Add the Sites tab to our Dashboard tabs.
 *
 * @since 1.6
 *
 * @param array $tabs Existing tabs.
 * @return array New tabs.
 */
function generate_elements_dashboard_tab( $tabs ) {
	$tabs['Elements'] = array(
		'name' => __( 'Elements', 'gp-premium' ),
		'url' => admin_url( 'edit.php?post_type=gp_elements' ),
		'class' => '',
	);

	return $tabs;
}

add_filter( 'generate_element_post_id', 'generate_elements_ignore_languages' );
/**
 * Disable Polylang elements if their language doesn't match.
 * We disable their automatic quering so Elements with no language display by default.
 *
 * @since 1.8
 *
 * @param int $post_id
 * @return bool|int
 */
function generate_elements_ignore_languages( $post_id ) {
	if ( function_exists( 'pll_get_post_language' ) && function_exists( 'pll_current_language' ) ) {
		$language = pll_get_post_language( $post_id, 'locale' );
		$disable = get_post_meta( $post_id, '_generate_element_ignore_languages', true );

		if ( $disable ) {
			return $post_id;
		}

		if ( $language && $language !== pll_current_language( 'locale' ) ) {
			return false;
		}
	}

	return $post_id;
}
