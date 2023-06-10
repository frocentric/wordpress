<?php
/**
 * This file sets up our Elements post type.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Start our Elements post type class.
 */
class GeneratePress_Elements_Post_Type {
	/**
	 * Instance.
	 *
	 * @since 1.7
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 *
	 * @since 1.7
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Build it.
	 *
	 * @since 1.7
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'post_type' ) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'menu_item' ), 100 );
			add_action( 'admin_head', array( $this, 'fix_current_item' ) );
			add_filter( 'manage_gp_elements_posts_columns', array( $this, 'register_columns' ) );
			add_action( 'manage_gp_elements_posts_custom_column', array( $this, 'add_columns' ), 10, 2 );
			add_action( 'restrict_manage_posts', array( $this, 'build_element_type_filter' ) );
			add_filter( 'pre_get_posts', array( $this, 'filter_element_types' ) );
			add_filter( 'register_post_type_args', array( $this, 'set_standard_element' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_footer', array( $this, 'element_modal' ) );

			self::setup_metabox();
		}
	}

	/**
	 * Load the metabox.
	 *
	 * @since 1.7
	 */
	public function setup_metabox() {
		require plugin_dir_path( __FILE__ ) . 'class-metabox.php';
	}

	/**
	 * Set up our custom post type.
	 *
	 * @since 1.7
	 */
	public function post_type() {
		$labels = array(
			'name'                   => _x( 'Elements', 'Post Type General Name', 'gp-premium' ),
			'singular_name'          => _x( 'Element', 'Post Type Singular Name', 'gp-premium' ),
			'menu_name'              => __( 'Elements', 'gp-premium' ),
			'all_items'              => __( 'All Elements', 'gp-premium' ),
			'add_new'                => __( 'Add New Element', 'gp-premium' ),
			'add_new_item'           => __( 'Add New Element', 'gp-premium' ),
			'new_item'               => __( 'New Element', 'gp-premium' ),
			'edit_item'              => __( 'Edit Element', 'gp-premium' ),
			'update_item'            => __( 'Update Element', 'gp-premium' ),
			'search_items'           => __( 'Search Element', 'gp-premium' ),
			'featured_image'         => __( 'Background Image', 'gp-premium' ),
			'set_featured_image'     => __( 'Set background image', 'gp-premium' ),
			'remove_featured_image'  => __( 'Remove background image', 'gp-premium' ),
			'item_published'         => __( 'Element published.', 'gp-premium' ),
			'item_updated'           => __( 'Element updated.', 'gp-premium' ),
			'item_scheduled'         => __( 'Element scheduled.', 'gp-premium' ),
			'item_reverted_to_draft' => __( 'Element reverted to draft.', 'gp-premium' ),
		);

		$args = array(
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'revisions' ),
			'hierarchical'          => true,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'show_in_rest'          => true,
		);

		register_post_type( 'gp_elements', $args );
	}

	/**
	 * Disable editor and show_in_rest support for non-Block Elements.
	 *
	 * @since 1.11.0
	 * @param array  $args The existing args.
	 * @param string $post_type The current post type.
	 */
	public function set_standard_element( $args, $post_type ) {
		if ( 'gp_elements' === $post_type ) {
			$post_id = false;
			$type = false;

			if ( isset( $_GET['post'] ) ) { // phpcs:ignore -- No processing happening.
				$post_id = absint( $_GET['post'] ); // phpcs:ignore -- No processing happening.
			}

			if ( $post_id ) {
				$type = get_post_meta( $post_id, '_generate_element_type', true );
			} elseif ( isset( $_GET['element_type'] ) ) { // phpcs:ignore -- No processing happening.
				$type = esc_html( $_GET['element_type'] ); // phpcs:ignore -- No processing happening.
			}

			if ( ! $type ) {
				return $args;
			}

			if ( 'block' !== $type ) {
				$args['supports'] = array( 'title', 'thumbnail' );
				$args['show_in_rest'] = false;
				$args['hierarchical'] = false;
			}

			if ( 'block' === $type ) {
				$args['supports'] = array( 'title', 'editor', 'custom-fields', 'page-attributes', 'revisions' );
			}

			if ( 'layout' === $type ) {
				$args['labels']['add_new_item'] = __( 'Add New Layout', 'gp-premium' );
				$args['labels']['edit_item'] = __( 'Edit Layout', 'gp-premium' );
			}

			if ( 'hook' === $type ) {
				$args['labels']['add_new_item'] = __( 'Add New Hook', 'gp-premium' );
				$args['labels']['edit_item'] = __( 'Edit Hook', 'gp-premium' );
			}

			if ( 'header' === $type ) {
				$args['labels']['add_new_item'] = __( 'Add New Header', 'gp-premium' );
				$args['labels']['edit_item'] = __( 'Edit Header', 'gp-premium' );
			}
		}

		return $args;
	}

	/**
	 * Register custom post type columns.
	 *
	 * @since 1.7
	 *
	 * @param array $columns Existing CPT columns.
	 * @return array All our CPT columns.
	 */
	public function register_columns( $columns ) {
		$columns['element_type'] = esc_html__( 'Type', 'gp-premium' );
		$columns['location'] = esc_html__( 'Location', 'gp-premium' );
		$columns['exclusions'] = esc_html__( 'Exclusions', 'gp-premium' );
		$columns['users'] = esc_html__( 'Users', 'gp-premium' );

		$new_columns = array();

		// Need to do some funky stuff to display these columns before the date.
		foreach ( $columns as $key => $value ) {
			if ( 'date' === $key ) {
				$new_columns['element_type'] = esc_html__( 'Type', 'gp-premium' );
				$new_columns['location'] = esc_html__( 'Location', 'gp-premium' );
				$new_columns['exclusions'] = esc_html__( 'Exclusions', 'gp-premium' );
				$new_columns['users'] = esc_html__( 'Users', 'gp-premium' );
			}

			$new_columns[ $key ] = $value;
		}

		return $new_columns;
	}

	/**
	 * Add a filter select input to the admin list.
	 *
	 * @since 1.7
	 */
	public function build_element_type_filter() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		if ( ! $screen ) {
			return;
		}

		if ( ! isset( $screen->post_type ) || 'gp_elements' !== $screen->post_type ) {
			return;
		}

		$values = array(
			'block' => esc_html__( 'Blocks', 'gp-premium' ),
			'header' => esc_html__( 'Headers', 'gp-premium' ),
			'hook' => esc_html__( 'Hooks', 'gp-premium' ),
			'layout' => esc_html__( 'Layouts', 'gp-premium' ),
		);

		$current_element_type = isset( $_GET['gp_element_type_filter'] ) ? esc_html( $_GET['gp_element_type_filter'] ) : '';  // phpcs:ignore -- No processing happening.
		$current_block_type = isset( $_GET['gp_elements_block_type_filter'] ) ? esc_html( $_GET['gp_elements_block_type_filter'] ) : '';  // phpcs:ignore -- No processing happening.
		?>
		<select name="gp_element_type_filter">
			<option value=""><?php esc_html_e( 'All types', 'gp-premium' ); ?></option>
			<?php
			foreach ( $values as $value => $label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_html( $value ),
					$value === $current_element_type ? 'selected="selected"' : '',
					esc_html( $label )
				);
			}
			?>
		</select>
		<?php
		if ( 'block' === $current_element_type ) {
			$block_types = array(
				'hook',
				'site-header',
				'page-hero',
				'content-template',
				'loop-template',
				'post-meta-template',
				'post-navigation-template',
				'archive-navigation-template',
				'right-sidebar',
				'left-sidebar',
				'site-footer',
			);
			?>
				<select name="gp_elements_block_type_filter">
					<option value=""><?php esc_html_e( 'All block types', 'gp-premium' ); ?></option>
					<?php
					foreach ( $block_types as $value ) {
						printf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_html( $value ),
							$value === $current_block_type ? 'selected="selected"' : '',
							esc_html( GeneratePress_Elements_Helper::get_element_type_label( $value ) )
						);
					}
					?>
				</select>
			<?php
		}
	}

	/**
	 * Filter the shown elements in the admin list if our filter is set.
	 *
	 * @since 1.7
	 *
	 * @param object $query Existing query.
	 */
	public function filter_element_types( $query ) {
		// phpcs:ignore -- No processing happening.
		if ( ! isset( $_GET['post_type'] ) || 'gp_elements' != $_GET['post_type'] ) {
			return;
		}

		global $pagenow;

		$type = isset( $_GET['gp_element_type_filter'] ) ? $_GET['gp_element_type_filter'] : '';  // phpcs:ignore -- No processing happening.
		$meta_query = array();

		if ( 'edit.php' === $pagenow && $query->is_main_query() && '' !== $type ) {
			$meta_query[] = array(
				'key' => '_generate_element_type',
				'value' => esc_attr( $type ),
				'compare' => '=',
			);

			$block_type = isset( $_GET['gp_elements_block_type_filter'] ) ? $_GET['gp_elements_block_type_filter'] : '';  // phpcs:ignore -- No processing happening.

			if ( 'block' === $type && '' !== $block_type ) {
				$meta_query['relation'] = 'AND';

				$meta_query[] = array(
					'key' => '_generate_block_type',
					'value' => esc_attr( $block_type ),
					'compare' => '=',
				);
			}

			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Add content to our custom post type columns.
	 *
	 * @since 1.7
	 *
	 * @param string $column The name of the column.
	 * @param int    $post_id The ID of the post row.
	 */
	public function add_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'element_type':
				$type = get_post_meta( $post_id, '_generate_element_type', true );
				$hook_location = get_post_meta( $post_id, '_generate_hook', true );

				if ( 'block' === $type ) {
					echo esc_html__( 'Block', 'gp-premium' );

					$block_type = get_post_meta( $post_id, '_generate_block_type', true );

					if ( $block_type ) {
						echo ' - ' . esc_html( GeneratePress_Elements_Helper::get_element_type_label( $block_type ) );

						if ( 'hook' === $block_type && $hook_location ) {
							echo '<br />';

							if ( 'custom' === $hook_location ) {
								$custom_hook = get_post_meta( $post_id, '_generate_custom_hook', true );
								echo '<span class="hook-location">' . esc_html( $custom_hook ) . '</span>';
							} else {
								echo '<span class="hook-location">' . esc_html( $hook_location ) . '</span>';
							}
						}
					}
				}

				if ( 'header' === $type ) {
					echo esc_html__( 'Header', 'gp-premium' );
				}

				if ( 'hook' === $type ) {
					echo esc_html__( 'Hook', 'gp-premium' );

					if ( $hook_location ) {
						echo '<br />';

						if ( 'custom' === $hook_location ) {
							$custom_hook = get_post_meta( $post_id, '_generate_custom_hook', true );
							echo '<span class="hook-location">' . esc_html( $custom_hook ) . '</span>';
						} else {
							echo '<span class="hook-location">' . esc_html( $hook_location ) . '</span>';
						}
					}
				}

				if ( 'layout' === $type ) {
					echo esc_html__( 'Layout', 'gp-premium' );
				}
				break;

			case 'location':
				$location = get_post_meta( $post_id, '_generate_element_display_conditions', true );
				$parent_block = wp_get_post_parent_id( $post_id );

				if ( $location ) {
					foreach ( (array) $location as $data ) {
						echo esc_html( GeneratePress_Conditions::get_saved_label( $data ) );
						echo '<br />';
					}
				} elseif ( ! empty( $parent_block ) ) {
					echo esc_html__( 'Inherit from parent', 'gp-premium' );
				} else {
					echo esc_html__( 'Not set', 'gp-premium' );
				}
				break;

			case 'exclusions':
				$location = get_post_meta( $post_id, '_generate_element_exclude_conditions', true );

				if ( $location ) {
					foreach ( (array) $location as $data ) {
						echo esc_html( GeneratePress_Conditions::get_saved_label( $data ) );
						echo '<br />';
					}
				}
				break;

			case 'users':
				$users = get_post_meta( $post_id, '_generate_element_user_conditions', true );

				if ( $users ) {
					foreach ( (array) $users as $data ) {
						if ( strpos( $data, ':' ) !== false ) {
							$data = substr( $data, strpos( $data, ':' ) + 1 );
						}

						$return = ucwords( str_replace( '_', ' ', $data ) );

						echo esc_html( $return ) . '<br />';
					}
				}
				break;
		}
	}

	/**
	 * Create our admin menu item.
	 *
	 * @since 1.7
	 */
	public function menu_item() {
		add_submenu_page(
			'themes.php',
			esc_html__( 'Elements', 'gp-premium' ),
			esc_html__( 'Elements', 'gp-premium' ),
			apply_filters( 'generate_elements_admin_menu_capability', 'manage_options' ),
			'edit.php?post_type=gp_elements'
		);
	}

	/**
	 * Make sure our admin menu item is highlighted.
	 *
	 * @since 1.7
	 */
	public function fix_current_item() {
		global $parent_file, $submenu_file, $post_type;

		if ( 'gp_elements' === $post_type ) {
			$parent_file = 'themes.php'; // phpcs:ignore
			$submenu_file = 'edit.php?post_type=gp_elements'; // phpcs:ignore
		}
	}

	/**
	 * Add scripts to the edit/post area of Elements.
	 *
	 * @since 1.11.0
	 * @param string $hook The current hook for the page.
	 */
	public function admin_scripts( $hook ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( 'edit.php' === $hook || 'post.php' === $hook ) {
			if ( 'gp_elements' === $current_screen->post_type ) {
				wp_enqueue_script( 'generate-elements', plugin_dir_url( __FILE__ ) . 'assets/admin/elements.js', array( 'jquery' ), GP_PREMIUM_VERSION, true );
				wp_enqueue_style( 'generate-elements', plugin_dir_url( __FILE__ ) . 'assets/admin/elements.css', array(), GP_PREMIUM_VERSION );
			}
		}
	}

	/**
	 * Build the Add New Element modal.
	 *
	 * @since 1.11.0
	 */
	public function element_modal() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( 'edit-gp_elements' === $current_screen->id || 'gp_elements' === $current_screen->id ) {
			?>
				<form method="get" class="choose-element-type-parent" action="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" style="display: none;">
					<input type="hidden" name="post_type" value="gp_elements" />

					<div class="choose-element-type">
						<h2><?php _e( 'Choose Element Type', 'gp-premium' ); ?></h2>
						<div class="select-type-container">
							<select class="select-type" name="element_type">
								<option value=""><?php esc_attr_e( 'Choose...', 'gp-premium' ); ?></option>
								<option value="block"><?php esc_attr_e( 'Block', 'gp-premium' ); ?></option>
								<option value="hook"><?php esc_attr_e( 'Hook', 'gp-premium' ); ?></option>
								<option value="layout"><?php esc_attr_e( 'Layout', 'gp-premium' ); ?></option>
								<option value="header"><?php esc_attr_e( 'Header', 'gp-premium' ); ?></option>
							</select>

							<button class="button button-primary"><?php _e( 'Create', 'gp-premium' ); ?></button>
						</div>

						<button class="close-choose-element-type" aria-label="<?php esc_attr_e( 'Close', 'gp-premium' ); ?>">
							<svg aria-hidden="true" data-prefix="fas" data-icon="times" class="svg-inline--fa fa-times fa-w-11" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512">
								<path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/>
							</svg>
						</button>
					</div>
				</form>
			<?php
		}
	}
}

GeneratePress_Elements_Post_Type::get_instance();
