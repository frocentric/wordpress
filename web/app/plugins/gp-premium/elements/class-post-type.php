<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

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
			self::$instance = new self;
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
			add_action( 'admin_menu', 								array( $this, 'menu_item' ), 100 );
			add_action( 'admin_head', 								array( $this, 'fix_current_item' ) );
			add_filter( 'manage_gp_elements_posts_columns', 		array( $this, 'register_columns' ) );
			add_action( 'manage_gp_elements_posts_custom_column',	array( $this, 'add_columns' ), 10, 2 );
			add_action( 'restrict_manage_posts',					array( $this, 'build_element_type_filter' ) );
			add_filter( 'pre_get_posts',							array( $this, 'filter_element_types' ) );

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
			'name'                  => _x( 'Elements', 'Post Type General Name', 'gp-premium' ),
			'singular_name'         => _x( 'Element', 'Post Type Singular Name', 'gp-premium' ),
			'menu_name'             => __( 'Elements', 'gp-premium' ),
			'all_items'             => __( 'All Elements', 'gp-premium' ),
			'add_new_item'          => __( 'Add New Element', 'gp-premium' ),
			'new_item'              => __( 'New Element', 'gp-premium' ),
			'edit_item'             => __( 'Edit Element', 'gp-premium' ),
			'update_item'           => __( 'Update Element', 'gp-premium' ),
			'search_items'          => __( 'Search Element', 'gp-premium' ),
			'featured_image'		=> __( 'Background Image', 'gp-premium' ),
			'set_featured_image'	=> __( 'Set background image', 'gp-premium' ),
			'remove_featured_image'	=> __( 'Remove background image', 'gp-premium' ),
		);

		$args = array(
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
		);

		register_post_type( 'gp_elements', $args );
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
		if ( 'gp_elements' !== get_post_type() ) {
			return;
		}

		$values = array(
			'header' => esc_html__( 'Headers', 'gp-premium' ),
			'hook' => esc_html__( 'Hooks', 'gp-premium' ),
			'layout' => esc_html__( 'Layouts', 'gp-premium' ),
		);

		?>
		<select name="gp_element_type_filter">
			<option value=""><?php esc_html_e( 'All types', 'gp-premium' ); ?></option>
			<?php
				$current = isset( $_GET['gp_element_type_filter'] )? esc_html( $_GET['gp_element_type_filter'] ) : '';
				foreach ( $values as $value => $label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_html( $value ),
						$value === $current ? 'selected="selected"' : '',
						$label
					);
				}
			?>
		</select>
		<?php
	}

	/**
	 * Filter the shown elements in the admin list if our filter is set.
	 *
	 * @since 1.7
	 *
	 * @param object $query Existing query.
	 */
	public function filter_element_types( $query ) {
		if ( ! isset( $_GET['post_type'] ) || 'gp_elements' != $_GET['post_type'] ) {
			return;
		}

		global $pagenow;

		$type = isset( $_GET['gp_element_type_filter'] ) ? $_GET['gp_element_type_filter'] : '';

		if ( 'edit.php' === $pagenow && $query->is_main_query() && '' !== $type ) {
			$query->set( 'meta_key', '_generate_element_type' );
			$query->set( 'meta_value', esc_attr( $type ) );
		}
	}

	/**
	 * Add content to our custom post type columns.
	 *
	 * @since 1.7
	 *
	 * @param string $column The name of the column.
	 * @param int $post_id The ID of the post row.
	 */
	public function add_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'element_type' :
				$type = get_post_meta( $post_id, '_generate_element_type', true );

				if ( 'header' === $type ) {
					echo esc_html__( 'Header', 'gp-premium' );
				}

				if ( 'hook' === $type ) {
					echo esc_html__( 'Hook', 'gp-premium' );
				}

				if ( 'layout' === $type ) {
					echo esc_html__( 'Layout', 'gp-premium' );
				}
			break;

			case 'location' :
				$location = get_post_meta( $post_id, '_generate_element_display_conditions', true );

				if ( $location ) {
					foreach ( ( array ) $location as $data ) {
						echo GeneratePress_Conditions::get_saved_label( $data );
						echo '<br />';
					}
				}
			break;

			case 'exclusions' :
				$location = get_post_meta( $post_id, '_generate_element_exclude_conditions', true );

				if ( $location ) {
					foreach ( ( array ) $location as $data ) {
						echo GeneratePress_Conditions::get_saved_label( $data );
						echo '<br />';
					}
				}
			break;

			case 'users' :
				$users = get_post_meta( $post_id, '_generate_element_user_conditions', true );

				if ( $users ) {
					foreach ( ( array ) $users as $data ) {
						if ( strpos( $data, ':' ) !== FALSE ) {
							$data = substr( $data, strpos( $data, ':' ) + 1 );
						}

						$return = ucwords( str_replace( '_', ' ', $data ) );

						echo $return . '<br />';
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
			$parent_file = 'themes.php';
			$submenu_file = 'edit.php?post_type=gp_elements';
		}
	}
}

GeneratePress_Elements_Post_Type::get_instance();
