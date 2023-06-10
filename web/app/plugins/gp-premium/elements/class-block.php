<?php
/**
 * This file displays our block elements on the site.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Build our Block Elements.
 */
class GeneratePress_Block_Element {

	/**
	 * The element ID.
	 *
	 * @since 1.11.0
	 * @var int ID of the element.
	 */
	protected $post_id = '';

	/**
	 * The element type.
	 *
	 * @since 1.11.0
	 * @var string Type of element.
	 */
	protected $type = '';

	/**
	 * Has post ancestors.
	 *
	 * @since 2.0.0
	 * @var boolean If this post has a parent.
	 */
	protected $has_parent = false;

	/**
	 * Kicks it all off.
	 *
	 * @since 1.11.0
	 *
	 * @param int $post_id The element post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
		$this->type = get_post_meta( $post_id, '_generate_block_type', true );
		$has_content_template_condition = get_post_meta( $post_id, '_generate_post_loop_item_display', true );

		// Take over the $post_id temporarily if this is a child block.
		// This allows us to inherit the parent block Display Rules.
		if ( 'content-template' === $this->type && $has_content_template_condition ) {
			$parent_block = wp_get_post_parent_id( $post_id );

			if ( ! empty( $parent_block ) ) {
				$this->has_parent = true;
				$post_id = $parent_block;
			}
		}

		$display_conditions = get_post_meta( $post_id, '_generate_element_display_conditions', true ) ? get_post_meta( $post_id, '_generate_element_display_conditions', true ) : array();
		$exclude_conditions = get_post_meta( $post_id, '_generate_element_exclude_conditions', true ) ? get_post_meta( $post_id, '_generate_element_exclude_conditions', true ) : array();
		$user_conditions = get_post_meta( $post_id, '_generate_element_user_conditions', true ) ? get_post_meta( $post_id, '_generate_element_user_conditions', true ) : array();

		$display = apply_filters(
			'generate_block_element_display',
			GeneratePress_Conditions::show_data(
				$display_conditions,
				$exclude_conditions,
				$user_conditions
			),
			$post_id
		);

		/**
		 * Simplify filter name.
		 *
		 * @since 2.0.0
		 */
		$display = apply_filters(
			'generate_element_display',
			$display,
			$post_id
		);

		// Restore our actual post ID if it's been changed.
		if ( 'content-template' === $this->type && $has_content_template_condition ) {
			$post_id = $this->post_id;
		}

		if ( $display ) {
			global $generate_elements;

			$generate_elements[ $post_id ] = array(
				'is_block_element' => true,
				'type' => $this->type,
				'id' => $post_id,
			);

			$hook = get_post_meta( $post_id, '_generate_hook', true );
			$custom_hook = get_post_meta( $post_id, '_generate_custom_hook', true );
			$priority = get_post_meta( $post_id, '_generate_hook_priority', true );

			if ( '' === $priority ) {
				$priority = 10;
			}

			switch ( $this->type ) {
				case 'site-header':
					$hook = 'generate_header';
					break;

				case 'site-footer':
					$hook = 'generate_footer';
					break;

				case 'right-sidebar':
					$hook = 'generate_before_right_sidebar_content';
					break;

				case 'left-sidebar':
					$hook = 'generate_before_left_sidebar_content';
					break;

				case 'content-template':
					$hook = 'generate_before_do_template_part';
					break;

				case 'loop-template':
					$hook = 'generate_before_main_content';
					break;

				case 'search-modal':
					$hook = 'generate_inside_search_modal';
					break;
			}

			if ( 'custom' === $hook && $custom_hook ) {
				$hook = $custom_hook;
			}

			if ( 'post-meta-template' === $this->type ) {
				$post_meta_location = get_post_meta( $post_id, '_generate_post_meta_location', true );

				if ( '' === $post_meta_location || 'after-post-title' === $post_meta_location ) {
					$hook = 'generate_after_entry_title';

					if ( is_page() ) {
						$hook = 'generate_after_page_title';
					}
				} elseif ( 'before-post-title' === $post_meta_location ) {
					$hook = 'generate_before_entry_title';

					if ( is_page() ) {
						$hook = 'generate_before_page_title';
					}
				} elseif ( 'after-content' === $post_meta_location ) {
					$hook = 'generate_after_content';
				}
			}

			if ( ! $hook ) {
				return;
			}

			if ( 'generate_header' === $hook ) {
				remove_action( 'generate_header', 'generate_construct_header' );
			}

			if ( 'generate_footer' === $hook ) {
				remove_action( 'generate_footer', 'generate_construct_footer' );
			}

			if ( 'content-template' === $this->type && ! $this->has_parent ) {
				add_filter( 'generate_do_template_part', array( $this, 'do_template_part' ) );
			}

			if ( 'loop-template' === $this->type ) {
				add_filter( 'generate_has_default_loop', '__return_false' );
				add_filter( 'generate_blog_columns', '__return_false' );
				add_filter( 'option_generate_blog_settings', array( $this, 'filter_blog_settings' ) );
				add_filter( 'post_class', array( $this, 'post_classes' ) );
			}

			if ( 'search-modal' === $this->type ) {
				remove_action( 'generate_inside_search_modal', 'generate_do_search_fields' );
			}

			add_action( 'wp', array( $this, 'remove_elements' ), 100 );
			add_action( esc_attr( $hook ), array( $this, 'build_hook' ), absint( $priority ) );
			add_filter( 'generateblocks_do_content', array( $this, 'do_block_content' ) );
		}
	}

	/**
	 * Disable our post loop items if needed.
	 *
	 * @param boolean $do Whether to display the default post loop item or not.
	 */
	public function do_template_part( $do ) {
		if ( GeneratePress_Elements_Helper::should_render_content_template( $this->post_id ) ) {
			return false;
		}

		return $do;
	}

	/**
	 * Tell GenerateBlocks about our block element content so it can build CSS.
	 *
	 * @since 1.11.0
	 * @param string $content The existing content.
	 */
	public function do_block_content( $content ) {
		if ( has_blocks( $this->post_id ) ) {
			$block_element = get_post( $this->post_id );

			if ( ! $block_element || 'gp_elements' !== $block_element->post_type ) {
				return $content;
			}

			if ( 'publish' !== $block_element->post_status || ! empty( $block_element->post_password ) ) {
				return $content;
			}

			$content .= $block_element->post_content;
		}

		return $content;
	}

	/**
	 * Remove existing sidebar widgets.
	 *
	 * @since 1.11.0
	 * @param array $widgets The existing widgets.
	 */
	public function remove_sidebar_widgets( $widgets ) {
		if ( 'right-sidebar' === $this->type ) {
			unset( $widgets['sidebar-1'] );
		}

		if ( 'left-sidebar' === $this->type ) {
			unset( $widgets['sidebar-2'] );
		}

		return $widgets;
	}

	/**
	 * Filter some of our blog settings.
	 *
	 * @param array $settings Existing blog settings.
	 */
	public function filter_blog_settings( $settings ) {
		if ( 'loop-template' === $this->type ) {
			$settings['infinite_scroll'] = false;
			$settings['read_more_button'] = false;
		}

		return $settings;
	}

	/**
	 * Add class to our loop template item posts.
	 *
	 * @param array $classes Post classes.
	 */
	public function post_classes( $classes ) {
		if ( 'loop-template' === $this->type && is_main_query() ) {
			$classes[] = 'is-loop-template-item';
		}

		return $classes;
	}

	/**
	 * Remove existing elements.
	 *
	 * @since 2.0.0
	 */
	public function remove_elements() {
		if ( 'right-sidebar' === $this->type || 'left-sidebar' === $this->type ) {
			add_filter( 'sidebars_widgets', array( $this, 'remove_sidebar_widgets' ) );
			add_filter( 'generate_show_default_sidebar_widgets', '__return_false' );
		}

		if ( 'page-hero' === $this->type ) {
			$disable_title = get_post_meta( $this->post_id, '_generate_disable_title', true );
			$disable_featured_image = get_post_meta( $this->post_id, '_generate_disable_featured_image', true );
			$disable_primary_post_meta = get_post_meta( $this->post_id, '_generate_disable_primary_post_meta', true );

			if ( $disable_title ) {
				if ( is_singular() ) {
					add_filter( 'generate_show_title', '__return_false' );
				}

				remove_action( 'generate_archive_title', 'generate_archive_title' );
				remove_filter( 'get_the_archive_title', 'generate_filter_the_archive_title' );

				// WooCommerce removal.
				if ( class_exists( 'WooCommerce' ) ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
					add_filter( 'woocommerce_show_page_title', '__return_false' );
					remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description' );
					remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description' );
				}
			}

			if ( $disable_primary_post_meta ) {
				remove_action( 'generate_after_entry_title', 'generate_post_meta' );
			}

			if ( $disable_featured_image && is_singular() ) {
				remove_action( 'generate_after_entry_header', 'generate_blog_single_featured_image' );
				remove_action( 'generate_before_content', 'generate_blog_single_featured_image' );
				remove_action( 'generate_after_header', 'generate_blog_single_featured_image' );
				remove_action( 'generate_before_content', 'generate_featured_page_header_inside_single' );
				remove_action( 'generate_after_header', 'generate_featured_page_header' );
			}
		}

		if ( 'post-meta-template' === $this->type ) {
			$post_meta_location = get_post_meta( $this->post_id, '_generate_post_meta_location', true );
			$disable_primary_post_meta = get_post_meta( $this->post_id, '_generate_disable_primary_post_meta', true );
			$disable_secondary_post_meta = get_post_meta( $this->post_id, '_generate_disable_secondary_post_meta', true );

			if ( '' === $post_meta_location || 'after-post-title' === $post_meta_location || 'custom' === $post_meta_location ) {
				if ( $disable_primary_post_meta ) {
					remove_action( 'generate_after_entry_title', 'generate_post_meta' );
				}
			} elseif ( 'before-post-title' === $post_meta_location || 'custom' === $post_meta_location ) {
				if ( $disable_primary_post_meta ) {
					remove_action( 'generate_after_entry_title', 'generate_post_meta' );
				}
			} elseif ( 'after-content' === $post_meta_location || 'custom' === $post_meta_location ) {
				if ( $disable_secondary_post_meta ) {
					remove_action( 'generate_after_entry_content', 'generate_footer_meta' );
				}
			}
		}

		if ( 'post-navigation-template' === $this->type ) {
			$disable_post_navigation = get_post_meta( $this->post_id, '_generate_disable_post_navigation', true );

			if ( $disable_post_navigation ) {
				add_filter( 'generate_footer_entry_meta_items', array( $this, 'disable_post_navigation' ) );
			}
		}

		if ( 'archive-navigation-template' === $this->type ) {
			$disable_archive_navigation = get_post_meta( $this->post_id, '_generate_disable_archive_navigation', true );

			if ( $disable_archive_navigation ) {
				remove_action( 'generate_after_loop', 'generate_do_post_navigation' );
			}
		}
	}

	/**
	 * Disable post navigation.
	 *
	 * @param array $items The post meta items.
	 */
	public function disable_post_navigation( $items ) {
		return array_diff( $items, array( 'post-navigation' ) );
	}

	/**
	 * Builds the HTML structure for Page Headers.
	 *
	 * @since 1.11.0
	 */
	public function build_hook() {
		$post_id = $this->post_id;

		if ( 'content-template' === $this->type ) {
			// Check for child templates if this isn't already one.
			if ( ! $this->has_parent ) {
				$children = get_posts(
					array(
						'post_type'     => 'gp_elements',
						'post_parent'   => $post_id,
						'order'         => 'ASC',
						'orderby'       => 'menu_order',
						'no_found_rows' => true,
						'post_status'   => 'publish',
						'numberposts'   => 20,
						'fields'        => 'ids',
					)
				);

				if ( ! empty( $children ) ) {
					// Loop through any child templates and overwrite $post_id if applicable.
					foreach ( (array) $children as $child_id ) {
						if ( GeneratePress_Elements_Helper::should_render_content_template( $child_id ) ) {
							$post_id = $child_id;
							break;
						}
					}
				} else {
					// No children, check if parent should render.
					if ( ! GeneratePress_Elements_Helper::should_render_content_template( $post_id ) ) {
						return;
					}
				}
			} else {
				// No children, check if template should render.
				if ( ! GeneratePress_Elements_Helper::should_render_content_template( $post_id ) ) {
					return;
				}
			}

			// Don't display child elements - they will replace the parent element if applicable.
			if ( $this->has_parent ) {
				return;
			}

			$tag_name_value = get_post_meta( $post_id, '_generate_post_loop_item_tagname', true );
			$use_theme_container = get_post_meta( $post_id, '_generate_use_theme_post_container', true );

			if ( $tag_name_value ) {
				$tag_name = $tag_name_value;
			} else {
				$tag_name = 'article';
			}

			printf(
				'<%s id="%s" class="%s">',
				esc_attr( $tag_name ),
				'post-' . get_the_ID(),
				implode( ' ', get_post_class( 'dynamic-content-template' ) ) // phpcs:ignore -- No escaping needed.
			);

			if ( $use_theme_container ) {
				echo '<div class="inside-article">';
			}
		}

		if ( 'archive-navigation-template' === $this->type || 'post-navigation-template' === $this->type ) {
			$use_theme_pagination_container = get_post_meta( $post_id, '_generate_use_archive_navigation_container', true );

			if ( $use_theme_pagination_container ) {
				echo '<div class="paging-navigation">';
			}
		}

		echo GeneratePress_Elements_Helper::build_content( $post_id ); // phpcs:ignore -- No escaping needed.

		if ( 'content-template' === $this->type ) {
			if ( $use_theme_container ) {
				echo '</div>';
			}

			echo '</' . esc_attr( $tag_name ) . '>';
		}

		if ( 'archive-navigation-template' === $this->type || 'post-navigation-template' === $this->type ) {
			$use_theme_pagination_container = get_post_meta( $post_id, '_generate_use_archive_navigation_container', true );

			if ( $use_theme_pagination_container ) {
				echo '</div>';
			}
		}
	}
}
