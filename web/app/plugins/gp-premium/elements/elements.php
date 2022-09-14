<?php
/**
 * This file sets up our Elements module.
 *
 * @since 1.7.0
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

$elements_dir = plugin_dir_path( __FILE__ );

require $elements_dir . 'class-conditions.php';
require $elements_dir . 'class-elements-helper.php';
require $elements_dir . 'class-hooks.php';
require $elements_dir . 'class-hero.php';
require $elements_dir . 'class-layout.php';
require $elements_dir . 'class-block.php';
require $elements_dir . 'class-block-elements.php';
require $elements_dir . 'class-post-type.php';

add_action( 'wp', 'generate_premium_do_elements' );
add_action( 'current_screen', 'generate_premium_do_elements' );
/**
 * Execute our Elements.
 *
 * @since 1.7
 */
function generate_premium_do_elements() {
	$args = array(
		'post_type'        => 'gp_elements',
		'no_found_rows'    => true,
		'post_status'      => 'publish',
		'numberposts'      => 500, // phpcs:ignore
		'fields'           => 'ids',
		'suppress_filters' => false,
	);

	$custom_args = apply_filters(
		'generate_elements_custom_args',
		array(
			'order' => 'ASC',
		)
	);

	$args = array_merge( $args, $custom_args );

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

		if ( 'block' === $type ) {
			new GeneratePress_Block_Element( $post_id );
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
	$screen = get_current_screen();

	$tabs['Elements'] = array(
		'name' => __( 'Elements', 'gp-premium' ),
		'url' => admin_url( 'edit.php?post_type=gp_elements' ),
		'class' => 'edit-gp_elements' === $screen->id ? 'active' : '',
	);

	return $tabs;
}

add_filter( 'generate_dashboard_screens', 'generate_elements_dashboard_screen' );
/**
 * Add the Sites tab to our Dashboard screens.
 *
 * @since 2.1.0
 *
 * @param array $screens Existing screens.
 * @return array New screens.
 */
function generate_elements_dashboard_screen( $screens ) {
	$screens[] = 'edit-gp_elements';

	return $screens;
}

add_filter( 'generate_element_post_id', 'generate_elements_ignore_languages' );
/**
 * Disable Polylang elements if their language doesn't match.
 * We disable their automatic quering so Elements with no language display by default.
 *
 * @since 1.8
 *
 * @param int $post_id The current post ID.
 * @return bool|int
 */
function generate_elements_ignore_languages( $post_id ) {
	if ( function_exists( 'pll_get_post_language' ) && function_exists( 'pll_current_language' ) ) {
		$language = pll_get_post_language( $post_id, 'locale' );
		$disable = get_post_meta( $post_id, '_generate_element_ignore_languages', true );

		if ( $disable ) {
			return $post_id;
		}

		if ( $language && $language !== pll_current_language( 'locale' ) ) { // phpcs:ignore -- Using Yoda check I am.
			return false;
		}
	}

	return $post_id;
}

add_action( 'save_post_wp_block', 'generate_elements_wp_block_update', 10, 2 );
/**
 * Regenerate the GenerateBlocks CSS file when a re-usable block is saved.
 *
 * @since 1.11.0
 * @param int    $post_id The post ID.
 * @param object $post The post object.
 */
function generate_elements_wp_block_update( $post_id, $post ) {
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );

	if ( $is_autosave || $is_revision || ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( isset( $post->post_content ) ) {
		if ( strpos( $post->post_content, 'wp:generateblocks' ) !== false ) {
			global $wpdb;

			$option = get_option( 'generateblocks_dynamic_css_posts', array() );

			$posts = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_generateblocks_reusable_blocks'" );

			foreach ( (array) $posts as $id ) {
				$display_conditions = get_post_meta( $id, '_generate_element_display_conditions', true );

				if ( $display_conditions ) {
					foreach ( (array) $display_conditions as $condition ) {
						if ( 'general:site' === $condition['rule'] ) {
							$option = array();
							break;
						}

						if ( $condition['object'] && isset( $option[ $condition['object'] ] ) ) {
							unset( $option[ $condition['object'] ] );
						}
					}
				}
			}

			update_option( 'generateblocks_dynamic_css_posts', $option );
		}
	}

}

add_filter( 'generate_do_block_element_content', 'generate_add_block_element_content_filters' );
/**
 * Apply content filters to our block elements.
 *
 * @since 1.11.0
 * @param string $content The block element content.
 */
function generate_add_block_element_content_filters( $content ) {
	$content = shortcode_unautop( $content );
	$content = do_shortcode( $content );

	if ( function_exists( 'wp_filter_content_tags' ) ) {
		$content = wp_filter_content_tags( $content );
	} elseif ( function_exists( 'wp_make_content_images_responsive' ) ) {
		$content = wp_make_content_images_responsive( $content );
	}

	return $content;
}

add_action( 'admin_bar_menu', 'generate_add_elements_admin_bar', 100 );
/**
 * Add the Elementd admin bar item.
 *
 * @since 2.0.0
 */
function generate_add_elements_admin_bar() {
	$current_user_can = 'manage_options';

	if ( apply_filters( 'generate_elements_metabox_ajax_allow_editors', false ) ) {
		$current_user_can = 'edit_posts';
	}

	if ( ! current_user_can( $current_user_can ) ) {
		return;
	}

	global $wp_admin_bar;
	global $generate_elements;

	$title = __( 'Elements', 'gp-premium' );
	$count = ! empty( $generate_elements ) ? count( $generate_elements ) : 0;

	// Prevent "Entire Site" Elements from being counted on non-edit pages in the admin.
	if ( is_admin() && function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();

		if ( ! isset( $screen->is_block_editor ) || ! $screen->is_block_editor ) {
			$count = 0;
		}

		if ( 'edit' !== $screen->parent_base ) {
			$count = 0;
		}
	}

	if ( $count > 0 ) {
		$title = sprintf(
			/* translators: Active Element count. */
			__( 'Elements (%s)', 'gp-premium' ),
			$count
		);
	}

	$wp_admin_bar->add_menu(
		array(
			'id' => 'gp_elements-menu',
			'title' => $title,
			'href' => esc_url( admin_url( 'edit.php?post_type=gp_elements' ) ),
		)
	);

	if ( ! empty( $generate_elements ) ) {
		// Prevent "Entire Site" Elements from being counted on non-edit pages in the admin.
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( ! isset( $screen->is_block_editor ) || ! $screen->is_block_editor ) {
				return;
			}

			if ( 'edit' !== $screen->parent_base ) {
				return;
			}
		}

		foreach ( (array) $generate_elements as $key => $data ) {
			$label = GeneratePress_Elements_Helper::get_element_type_label( $data['type'] );

			$wp_admin_bar->add_menu(
				array(
					'id' => 'element-' . absint( $data['id'] ),
					'parent' => 'gp_elements-menu',
					'title' => get_the_title( $data['id'] ) . ' (' . $label . ')',
					'href' => get_edit_post_link( $data['id'] ),
				)
			);
		}
	}
}
