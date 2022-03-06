<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ingenyus.com
 * @since      1.0.0
 *
 * @package    Froware
 * @subpackage Froware/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Froware
 * @subpackage Froware/admin
 * @author     Gary McPherson <gary@ingenyus.com>
 */
class Froware_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froware_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froware_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/froware-admin.css', [], $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Froware_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Froware_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/froware-admin.js', [ 'jquery' ], $this->version, false );
	}

	/**
	 * Set the canonical URL if the post is imported and has an external URL defined.
	 *
	 * @param    int     $post_id     The post ID.
	 * @param    WP_Post $post        The post.
	 * @param    bool    $update      Whether this is an existing post being updated.
	 * @since    1.0.0
	 */
	public function set_canonical_url( $post_id, $post, $update ) {

		// Test if The SEO Framework and WPeMatico plugins are installed.
		if ( \get_post_type( $post_id ) === 'post' && defined( 'THE_SEO_FRAMEWORK_DB_VERSION' ) && class_exists( 'WPeMatico' ) ) {
			$source_field    = 'wpe_sourcepermalink';
			$canonical_field = '_genesis_canonical_uri';
			$source          = \get_post_meta( $post_id, $source_field, true );
			$canonical       = \get_post_meta( $post_id, $canonical_field, true );
			// Test if the canonical URL is empty and there is a source permalink URL available.
			if ( $source && ! $canonical ) {
				\update_post_meta( $post_id, $canonical_field, \esc_url_raw( $source ) );
			}
		}

	}

	/**
	 * Hooks in to the option_active_plugins filter and removes any malformed plugins
	 */
	public function filter_active_plugins( $value, $option ) {
		if ( ! is_array( $value ) || count( $value ) === 0 ) {
			return $value;
		}

		for ( $i = count( $value ) - 1; $i >= 0; $i-- ) {
			if ( is_numeric( $value[ $i ] ) ) {
				array_splice( $value, $i, 1 );
			}
		}

		return $value;
	}

	/**
	 * Overrides admin_enqueue_scripts event hook in Elmentor to avoid conflict with Ninja Forms editor
	 */
	public function override_elementor_enqueue_scripts_hook() {
		if ( class_exists( 'Elementor\Plugin' ) && isset( $_GET['page'] ) && 'ninja-forms' === $_GET['page'] ) {
			remove_action( 'admin_enqueue_scripts', [ Elementor\Plugin::instance()->common, 'register_scripts' ] );
		}
	}
}
