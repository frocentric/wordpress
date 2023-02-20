<?php
/**
 * Elementor Hooks
 *
 * @package     Frocentric/Customizations
 * @version     1.0.0
 */

namespace Frocentric\Customizations;

use Frocentric\Constants as Constants;
use Frocentric\Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor Class.
 */
class Elementor {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			if ( Utils::is_request( Constants::ADMIN_REQUEST ) ) {
				add_action( 'init', [ __CLASS__, 'init_override_enqueue_scripts' ], 20 );
			}

			if ( Utils::is_request( Constants::FRONTEND_REQUEST ) ) {
				add_action( 'pre_get_posts', [ __CLASS__, 'pre_get_posts' ], 100 );
			}
		}
	}

	/**
	 * Overrides admin_enqueue_scripts event hook in Elementor to avoid conflict with Ninja Forms editor
	 */
	public static function init_override_enqueue_scripts() {
		if ( class_exists( '\Elementor\Plugin' ) && isset( $_GET['page'] ) && 'ninja-forms' === $_GET['page'] ) {
			remove_action( 'admin_enqueue_scripts', [ \Elementor\Plugin::instance()->common, 'register_scripts' ] );
		}
	}

	/**
	 * Fixes landing page 404 when non-standard permalinks are enabled.
	 *
	 * @param \WP_Query $query
	 * @return   string
	 * @since    1.0.0
	 */
	public static function pre_get_posts( \WP_Query $query ) {
		if (
			// If the post type includes the Elementor landing page CPT.
			class_exists( '\Elementor\Modules\LandingPages\Module' )
			&& is_array( $query->get( 'post_type' ) )
			&& in_array( \Elementor\Modules\LandingPages\Module::CPT, $query->get( 'post_type' ), true )
			// If custom permalinks are enabled.
			&& '' !== get_option( 'permalink_structure' )
			// If the query is for a front-end page.
			&& ( ! is_admin() || wp_doing_ajax() )
			&& $query->is_main_query()
			// If the query is for a page.
			&& isset( $query->query['page'] )
			// If the query is not for a static home/blog page.
			&& ! is_home()
			// If the page name has been set and is not for a path.
			&& ! empty( $query->query['pagename'] )
			&& false === strpos( $query->query['pagename'], '/' )
			// If the name has not already been set.
			&& empty( $query->query['name'] ) ) {
			$query->set( 'name', $query->query['pagename'] );
		}
	}
}
