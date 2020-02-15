<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ingenyus.com
 * @since      1.0.0
 *
 * @package    Froware
 * @subpackage Froware/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Froware
 * @subpackage Froware/public
 * @author     Gary McPherson <gary@ingenyus.com>
 */
class Froware_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/froware-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'dashicons' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/froware-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add custom fonts to GeneratePress font list.
	 *
	 * @since    1.0.0
	 */
	public function add_generatepress_fonts( $fonts ) {
		$fonts[] = 'Helvetica Neue Condensed Bold';

		sort($fonts);
	
		return $fonts;
	}

	/**
	 * Modify navigation output to correctly highlight current section.
	 *
	 * @since    1.0.0
	 */
	public function special_nav_class( $classes, $item, $args, $depth = 0 ) {
		if( 'primary' === $args->theme_location ) {
			$parent_classes = array('current-menu-item', 'page_item', 'current_page_item', 'current_page_parent');
			$posts_page = get_option( 'page_for_posts' );

			// Specify default page if posts page not enabled
			if( $posts_page == 0) $posts_page = 13283;
			
			// Highlight Content page link for any post or category page
			if( ((is_single() && get_post_type() == 'post') || is_category()) && $posts_page == $item->object_id ) {
				$classes = array_merge($classes, $parent_classes);
			}
		}
		// Filter out duplicate classes
		return array_unique($classes);
	}

	/**
	 * Adds support for audio post types
	 */
	public function extend_theme_support() {
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'status', 'audio' ) );
	}

	/**
	 * Overrides WP Show Posts card styling
	 */
	public function wpsp_defaults( $defaults ) {
		$defaults['wpsp_post_meta_bottom_style'] = 'inline';

		return $defaults;
	}

	/**
	 * Forces imported posts to take date from feed
	 */
	public function wpematico_item_parsers_callback( $current_item, $campaign, $feed, $item ){
		$found = false;
		$tags = array ( 'pubDate', 'published' );

		foreach ($tags as $tag) {
			$elem = $item->get_item_tags('', $tag);

			if( $elem != null ) {
				if( is_array( $elem ) )
				{
					$dateElem = $elem[0]['data'];
					$found = true;
				}
				elseif ( is_string ( $elem ) ) {
					$dateElem = $elem;
					$found = true;
				}
			}
		}
		
		if($found) {
			write_log('Date: ' . $dateElem);
			$date = strtotime(trim($dateElem, "; \t\n\r\0\x0B"));
			$current_item['date'] = $date;
			trigger_error(__('Assigning original date to post from custom hooks.', 'wpematico' ),E_USER_NOTICE);
		}

		return $current_item;
	}
	
	/**
	 * Get the post content up to the More tag
	 * @see https://codex.wordpress.org/Function_Reference/get_extended
	 */
	public function get_content_to_more_tag() {
		global $post;
		$content_main = get_extended( $post->post_content );
		$content_main = $content_main['main'];
		return wpautop( $content_main );
	}
	
	/**
	 * Filter the content feed to use main content instead of excerpt
	 * Add a featured image
	 * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content_feed
	 */
	public function filter_content_feed( $excerpt ) {
		// Add featured image?
		global $post;
		$excerpt = $post->post_excerpt;
		if( has_post_thumbnail( $post->ID ) ) {
			$excerpt = get_the_post_thumbnail( $post->ID, 'post-thumbnail', array( 'style' => 'max-width: 600px; width: 100%; height: auto; margin: 30px 0;' ) ) . $excerpt;
		}
		return $excerpt;
	}
}
