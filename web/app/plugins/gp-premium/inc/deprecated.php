<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Backgrounds module.
 */
if ( ! function_exists( 'generate_backgrounds_customize_preview_css' ) ) {
	function generate_backgrounds_customize_preview_css() {
		// No longer needed.
	}
}

if ( ! function_exists( 'generate_backgrounds_init' ) ) {
	function generate_backgrounds_init() {
		load_plugin_textdomain( 'backgrounds', false, 'gp-premium/langs/backgrounds/' );
	}
}

if ( ! function_exists( 'generate_backgrounds_setup' ) ) {
	function generate_backgrounds_setup() {
		// This function is here just in case
		// It's kept so we can check to see if Backgrounds is active elsewhere
	}
}

/**
 * Blog module.
 */
if ( ! function_exists( 'generate_blog_post_image' ) ) {
	/**
	 * Build our featured image HTML
	 *
	 * @deprecated 1.5
	 */
	function generate_blog_post_image() {
		// No longer needed
	}
}

if ( ! function_exists( 'generate_get_masonry_post_width' ) ) {
	/**
	 * Set our masonry post width
	 *
	 * @deprecated 1.5
	 */
	function generate_get_masonry_post_width() {
		// Get our global variables
		global $post, $wp_query;

		// Figure out which page we're on
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		// Figure out if we're on the most recent post or not
		$most_recent = ( $wp_query->current_post == 0 && $paged == 1 ) ? true : false;

		// Get our Customizer options
		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		$masonry_post_width = $generate_blog_settings['masonry_width'];

		// Get our post meta option
		$stored_meta = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate-blog-post-class', true ) : '';

		// If our post meta option is set, use it
		// Or else, use our Customizer option
		if ( '' !== $stored_meta ) {
			if ( 'width4' == $stored_meta && 'width4' == $generate_blog_settings['masonry_width'] ) {
				$masonry_post_width = 'medium';
			} else {
				$masonry_post_width = $stored_meta;
			}
		}

		// Return our width class
		return apply_filters( 'generate_masonry_post_width', $masonry_post_width );
	}
}

if ( ! function_exists( 'generate_blog_add_post_class_meta_box' ) ) {
	/**
	 * Create our masonry meta box
	 *
	 * @deprecated 1.5
	 */
	function generate_blog_add_post_class_meta_box() {
		$generate_blog_settings = wp_parse_args(
			get_option( 'generate_blog_settings', array() ),
			generate_blog_get_defaults()
		);

		if ( 'true' !== $generate_blog_settings['masonry'] ) {
			return;
		}

		$post_types = apply_filters( 'generate_blog_masonry_metabox', array( 'post' ) );

		add_meta_box
		(
			'generate_blog_post_class_meta_box', // $id
			__('Masonry Post Width','generate-blog'), // $title
			'generate_blog_show_post_class_metabox', // $callback
			$post_types, // $page
			'side', // $context
			'default' // $priority
		);
	}
}

if ( ! function_exists( 'generate_blog_show_post_class_metabox' ) ) {
	/**
	 * Outputs the content of the metabox
	 * @deprecated 1.5
	 */
	function generate_blog_show_post_class_metabox( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'generate_blog_post_class_nonce' );
		$stored_meta = get_post_meta( $post->ID );

		// Set defaults to avoid PHP notices
		if ( isset($stored_meta['_generate-blog-post-class'][0]) ) {
			$stored_meta['_generate-blog-post-class'][0] = $stored_meta['_generate-blog-post-class'][0];
		} else {
			$stored_meta['_generate-blog-post-class'][0] = '';
		}
		?>
		<p>
			<label for="_generate-blog-post-class" class="example-row-title"><strong><?php _e( 'Masonry Post Width', 'gp-premium' );?></strong></label><br />
			<select name="_generate-blog-post-class" id="_generate-blog-post-class">
				<option value="" <?php selected( $stored_meta['_generate-blog-post-class'][0], '' ); ?>><?php _e( 'Global setting', 'gp-premium' );?></option>
				<option value="width2" <?php selected( $stored_meta['_generate-blog-post-class'][0], 'width2' ); ?>><?php _e( 'Small', 'gp-premium' );?></option>
				<option value="width4" <?php selected( $stored_meta['_generate-blog-post-class'][0], 'width4' ); ?>><?php _e( 'Medium', 'gp-premium' );?></option>
				<option value="width6" <?php selected( $stored_meta['_generate-blog-post-class'][0], 'width6' ); ?>><?php _e( 'Large', 'gp-premium' );?></option>
			</select>
		</p>
		<?php
	}
}

if ( ! function_exists( 'generate_blog_save_post_class_meta' ) ) {
	/**
	 * Saves post class meta data
	 *
	 * @param int $post_id The post ID being saved
	 * @deprecated 1.5
	 */
	function generate_blog_save_post_class_meta( $post_id ) {
		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'generate_blog_post_class_nonce' ] ) && wp_verify_nonce( $_POST[ 'generate_blog_post_class_nonce' ], basename( __FILE__ ) ) ) ? true : false;

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Checks for input and saves if needed
		if ( isset( $_POST[ '_generate-blog-post-class' ] ) ) {
			update_post_meta( $post_id, '_generate-blog-post-class', sanitize_text_field( $_POST[ '_generate-blog-post-class' ] ) );
		}
	}
}

if ( ! function_exists( 'generate_blog_get_next_posts_url' ) ) {
	/**
	 * Get the URL of the next page
	 * This is for the AJAX load more function
	 */
	function generate_blog_get_next_posts_url( $max_page = 0 ) {
		global $paged, $wp_query;

		if ( ! $max_page ) {
			$max_page = $wp_query->max_num_pages;
		}

		if ( ! $paged ) {
			$paged = 1;
		}

		$nextpage = intval( $paged ) + 1;

		if ( ! is_single() && ( $nextpage <= $max_page ) ) {
			return next_posts( $max_page, false );
		}
	}
}

/**
 * Fixes a bug in Safari where images with srcset won't display when using infinite scroll.
 *
 * @since 1.5.5
 * @deprecated 1.6
 */
function generate_blog_disable_infinite_scroll_srcset() {
	$settings = wp_parse_args(
		get_option( 'generate_blog_settings', array() ),
		generate_blog_get_defaults()
	);

	if ( ! is_singular() && $settings[ 'infinite_scroll' ] ) {
		add_filter( 'wp_calculate_image_srcset', '__return_empty_array' );
	}
}

if ( ! function_exists( 'generate_blog_init' ) ) {
	function generate_blog_init() {
		load_plugin_textdomain( 'generate-blog', false, 'gp-premium/langs/blog/' );
	}
}

/**
 * Colors module.
 */
if ( ! function_exists( 'generate_colors_init' ) ) {
 	function generate_colors_init() {
 		load_plugin_textdomain( 'generate-colors', false, 'gp-premium/langs/colors/' );
 	}
}

if ( ! function_exists( 'generate_colors_setup' ) ) {
 	function generate_colors_setup() {
 		// Here so we can check to see if Colors is activated
 	}
}

/**
 * Copyright module.
 */
if ( ! function_exists( 'generate_copyright_init' ) ) {
	function generate_copyright_init() {
		load_plugin_textdomain( 'generate-copyright', false, 'gp-premium/langs/copyright/' );
	}
}

/**
 * Disable Elements module.
 */
if ( ! function_exists('generate_disable_elements_init') ) {
	function generate_disable_elements_init() {
		load_plugin_textdomain( 'disable-elements', false, 'gp-premium/langs/disable-elements/' );
	}
}

/**
 * Hooks module.
 */
if ( ! function_exists( 'generate_hooks_init' ) ) {
	function generate_hooks_init() {
		load_plugin_textdomain( 'generate-hooks', false, 'gp-premium/langs/hooks/' );
	}
}

/**
 * Import/Export module.
 */
if ( ! function_exists( 'generate_ie_init' ) ) {
	function generate_ie_init() {
		load_plugin_textdomain( 'generate-ie', false, 'gp-premium/langs/import-export/' );
	}
}

/**
 * Menu Plus module.
 */
if ( ! function_exists( 'generate_slideout_navigation_class' ) ) {
	/**
	* Display the classes for the slideout navigation.
	*
	* @since 0.1
	* @param string|array $class One or more classes to add to the class list.
	*/
	function generate_slideout_navigation_class( $class = '' ) {
		// Separates classes with a single space, collates classes for post DIV
		echo 'class="' . join( ' ', generate_get_slideout_navigation_class( $class ) ) . '"';
	}
}

if ( ! function_exists( 'generate_get_slideout_navigation_class' ) ) {
	/**
	* Retrieve the classes for the slideout navigation.
	*
	* @since 0.1
	* @param string|array $class One or more classes to add to the class list.
	* @return array Array of classes.
	*/
	function generate_get_slideout_navigation_class( $class = '' ) {
		$classes = array();

		if ( !empty($class) ) {
			if ( !is_array( $class ) )
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		}

		$classes = array_map('esc_attr', $classes);

		return apply_filters('generate_slideout_navigation_class', $classes, $class);
	}
}

if ( ! function_exists( 'generate_slideout_menu_class' ) ) {
	/**
	* Display the classes for the slideout navigation.
	*
	* @since 0.1
	* @param string|array $class One or more classes to add to the class list.
	*/
	function generate_slideout_menu_class( $class = '' ) {
		// Separates classes with a single space, collates classes for post DIV
		echo 'class="' . join( ' ', generate_get_slideout_menu_class( $class ) ) . '"';
	}
}

if ( ! function_exists( 'generate_get_slideout_menu_class' ) ) {
	/**
	* Retrieve the classes for the slideout navigation.
	*
	* @since 0.1
	* @param string|array $class One or more classes to add to the class list.
	* @return array Array of classes.
	*/
	function generate_get_slideout_menu_class( $class = '' ) {
		$classes = array();

		if ( !empty($class) ) {
			if ( !is_array( $class ) )
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		}

		$classes = array_map('esc_attr', $classes);

		return apply_filters('generate_slideout_menu_class', $classes, $class);
	}
}

if ( ! function_exists( 'generate_slideout_menu_classes' ) ) {
	/**
	* Adds custom classes to the menu
	* @since 0.1
	*/
	function generate_slideout_menu_classes( $classes ) {
		$classes[] = 'slideout-menu';
		return $classes;
	}
}

if ( ! function_exists( 'generate_slideout_navigation_classes' ) ) {
	/**
	* Adds custom classes to the navigation
	* @since 0.1
	*/
	function generate_slideout_navigation_classes( $classes ){
		$slideout_effect = apply_filters( 'generate_menu_slideout_effect','overlay' );
		$slideout_position = apply_filters( 'generate_menu_slideout_position','left' );

		$classes[] = 'main-navigation';
		$classes[] = 'slideout-navigation';

		return $classes;
	}
}

if ( ! function_exists( 'generate_menu_plus_init' ) ) {
	function generate_menu_plus_init() {
		load_plugin_textdomain( 'menu-plus', false, 'gp-premium/langs/menu-plus/' );
	}
}

if ( ! function_exists( 'generate_slideout_menu_fallback' ) ) {
	/**
	 * Menu fallback.
	 *
	 * @param  array $args
	 * @return string
	 * @since 1.1.4
	 */
	function generate_slideout_menu_fallback( $args ) {

	}
}

/**
 * Page header module.
 */
if ( ! function_exists( 'generate_page_header_inside' ) ) {
	/**
	* Add page header inside content
	* @since 0.3
	*/
	function generate_page_header_inside() {
		if ( ! is_page() ) {
			return;
		}

		if ( 'inside-content' == generate_get_page_header_location() ) {
			generate_page_header_area( 'page-header-image', 'page-header-content' );
		}
	}
}

if ( ! function_exists( 'generate_page_header_single_below_title' ) ) {
	/**
	* Add post header below title
	* @since 0.3
	*/
	function generate_page_header_single_below_title() {
		if ( ! is_single() ) {
			return;
		}

		if ( 'below-title' == generate_get_page_header_location() ) {
			generate_page_header_area( 'page-header-image-single page-header-below-title', 'page-header-content-single page-header-below-title' );
		}
	}
}

if ( ! function_exists( 'generate_page_header_single_above' ) ) {
	/**
	* Add post header above content
	* @since 0.3
	*/
	function generate_page_header_single_above() {
		if ( ! is_single() ) {
			return;
		}

		if ( 'above-content' == generate_get_page_header_location() ) {
			generate_page_header_area( 'page-header-image-single', 'page-header-content-single' );
		}
	}
}

if ( ! function_exists( 'generate_page_header_single' ) ) {
	/**
	* Add post header inside content
	* @since 0.3
	*/
	function generate_page_header_single() {
		$image_class = 'page-header-image-single';
		$content_class = 'page-header-content-single';

		if ( 'below-title' == generate_get_page_header_location() ) {
			$image_class = 'page-header-image-single page-header-below-title';
			$content_class = 'page-header-content-single page-header-below-title';
		}

		if ( 'inside-content' == generate_get_page_header_location() ) {
			generate_page_header_area( $image_class, $content_class );
		}
	}
}

if ( ! function_exists( 'generate_page_header_init' ) ) {
	function generate_page_header_init() {
		load_plugin_textdomain( 'page-header', false, 'gp-premium/langs/page-header/' );
	}
}

/**
 * Secondary Navigation module.
 */
if ( ! function_exists( 'generate_secondary_nav_init' ) ) {
	function generate_secondary_nav_init() {
		load_plugin_textdomain( 'secondary-nav', false, 'gp-premium/langs/secondary-nav/' );
	}
}

/**
 * Sections module.
 */
if ( ! function_exists( 'generate_sections_init' ) ) {
	function generate_sections_init() {
		load_plugin_textdomain( 'generate-sections', false, 'gp-premium/langs/sections/' );
	}
}

if ( ! function_exists( 'generate_sections_metabox_init' ) ) {
	/*
	 * Enqueue styles and scripts specific to metaboxs
	 */
	function generate_sections_metabox_init(){

		// I prefer to enqueue the styles only on pages that are using the metaboxes
		wp_enqueue_style( 'generate-sections-metabox', plugin_dir_url( __FILE__ ) . 'wpalchemy/css/meta.css');
		wp_enqueue_style( 'generate-style-grid', get_template_directory_uri() . '/css/unsemantic-grid.css', false, GENERATE_VERSION, 'all' );

		//make sure we enqueue some scripts just in case ( only needed for repeating metaboxes )
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker' );

		// special script for dealing with repeating textareas- needs to run AFTER all the tinyMCE init scripts, so make 'editor' a requirement
		wp_enqueue_script( 'generate-sections-metabox', plugin_dir_url( __FILE__ ) . 'wpalchemy/js/sections-metabox.js', array( 'jquery', 'editor', 'media-upload', 'wp-color-picker' ), GENERATE_SECTIONS_VERSION, true );
		$translation_array = array(
			'no_content_error' => __( 'Error: Content already detected in default editor.', 'gp-premium' ),
			'use_visual_editor' => __( 'Please activate the "Visual" tab in your main editor before transferring content.', 'gp-premium' )
		);
		wp_localize_script( 'generate-sections-metabox', 'generate_sections', $translation_array );
	}
}

/**
 * Spacing module.
 */
if ( ! function_exists( 'generate_spacing_init' ) ) {
	function generate_spacing_init() {
		load_plugin_textdomain( 'generate-spacing', false, 'gp-premium/langs/spacing/' );
	}
}

if ( ! function_exists( 'generate_spacing_setup' ) ) {
	function generate_spacing_setup() {
		// Here so we can check to see if Spacing is active
	}
}

/**
 * Typography module.
 */
if ( ! function_exists( 'generate_typography_init' ) ) {
	function generate_typography_init() {
		load_plugin_textdomain( 'generate-typography', false, 'gp-premium/langs/typography/' );
	}
}

if ( ! function_exists( 'generate_fonts_setup' ) ) {
	function generate_fonts_setup() {
		// Here to check if Typography is active
	}
}

/**
 * WooCommerce module.
 */
if ( ! function_exists( 'generate_woocommerce_init' ) ) {
	function generate_woocommerce_init() {
		load_plugin_textdomain( 'generate-woocommerce', false, 'gp-premium/langs/woocommerce/' );
	}
}

/**
 * Use text instead of an icon if essentials are in use.
 *
 * @since 1.3
 * @deprecated 1.6
 *
 * @param string $icon Existing icon HTML.
 * @return string New icon HTML.
 */
function generatepress_wc_essentials_menu_icon( $icon ) {
	if ( apply_filters( 'generate_fontawesome_essentials', false ) ) {
		return __( 'Cart', 'gp-premium' );
	}

	return $icon;
}

if ( ! function_exists( 'generate_activation_styles' ) ) {
	function generate_activation_styles() {
		// Added to dashboard.css
	}
}

if ( ! function_exists( 'generate_verify_styles' ) ) {
	function generate_verify_styles() {
		// Added to dashboard.css
	}
}

if ( ! function_exists( 'generate_add_license_key_field' ) ) {
	function generate_add_license_key_field() {
		// Replaced by generatepress_premium_license_key_field()
	}
}

if ( ! function_exists( 'generate_premium_license_key' ) ) {
	function generate_premium_license_key() {
		// Replaced by generatepress_premium_license_key_field()
	}
}

if ( ! function_exists( 'generate_save_premium_license_key' ) ) {
	function generate_save_premium_license_key() {
		// Replaced by generatepress_premium_process_license_key()
	}
}


if ( ! function_exists( 'generate_process_license_key' ) ) {
	function generate_process_license_key() {
		// Replaced by generatepress_premium_process_license_key()
	}
}

/**
 * Add the Refresh sites link after the list of sites.
 *
 * @since 1.6
 * @deprecated 1.7
 */
function generate_sites_refresh_link() {
	if ( ! generate_is_sites_dashboard() ) {
		return;
	}

	printf(
		'<div class="refresh-sites">
			<a class="button" href="%1$s">%2$s</a>
		</div>',
		wp_nonce_url( admin_url( 'themes.php?page=generate-options&area=generate-sites' ), 'refresh_sites', 'refresh_sites_nonce' ),
		__( 'Refresh Sites', 'gp-premium' )
	);
}

/**
 * Delete our sites transient if the Refresh sites link is clicked.
 *
 * @since 1.6
 * @deprecated 1.12.0
 */
function generate_sites_refresh_list() {
	if ( ! isset( $_GET['refresh_sites_nonce'] ) || ! wp_verify_nonce( $_GET['refresh_sites_nonce'], 'refresh_sites' ) ) {
		return;
	}

	delete_transient( 'generatepress_sites' );
}

if ( ! function_exists( 'generate_insert_import_export' ) ) {
	/**
	* @deprecated 1.7
	*/
	function generate_insert_import_export() {
		// Replaced by GeneratePress_Import_Export::build_html()
	}
}

if ( ! function_exists( 'generate_ie_import_form' ) ) {
	/**
	* @deprecated 1.7
	*/
	function generate_ie_import_form() {
		// Replaced by GeneratePress_Import_Export::build_html()
	}
}

if ( ! function_exists( 'generate_process_settings_export' ) ) {
	/**
	 * Process a settings export that generates a .json file of the shop settings
	 *
	 * @deprecated 1.7
	 */
	function generate_process_settings_export() {
		// Replaced by GeneratePress_Import_Export::export()
	}
}

if ( ! function_exists( 'generate_process_settings_import' ) ) {
	/**
	 * Process a settings import from a json file
	 *
	 * @deprecated 1.7
	 */
	function generate_process_settings_import() {
		// Replaced by GeneratePress_Import_Export::import()
	}
}

if ( ! function_exists( 'generate_ie_exportable' ) ) {
	/**
	* @deprecated 1.7
	*/
	function generate_ie_exportable() {
		// A check to see if other addons can add their export button
	}
}

/**
 * Build our dynamic CSS.
 *
 * @since 1.6
 */
function generate_menu_plus_make_css() {
	// Replaced by generate_do_off_canvas_css()
}

/**
 * Enqueue our dynamic CSS.
 *
 * @since 1.6
 */
function generate_menu_plus_enqueue_dynamic_css() {
	// No longer needed.
}

if ( ! function_exists( 'generate_hidden_secondary_navigation' ) && function_exists( 'is_customize_preview' ) ) {
	/**
	 * Adds a hidden navigation if no navigation is set
	 * This allows us to use postMessage to position the navigation when it doesn't exist
	 */
	function generate_hidden_secondary_navigation() {
		if ( is_customize_preview() && function_exists( 'generate_secondary_navigation_position' ) ) {
			?>
			<div style="display:none;">
				<?php generate_secondary_navigation_position(); ?>
			</div>
			<?php
		}
	}
}
