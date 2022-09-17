<?php
/**
 * This file handles the Disable Elements functionality.
 *
 * @package GP Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

define( 'GENERATE_DE_LAYOUT_META_BOX', true );

if ( ! function_exists( 'generate_disable_elements' ) ) {
	/**
	 * Remove the default disable_elements.
	 *
	 * @since 0.1
	 */
	function generate_disable_elements() {
		// Don't run the function unless we're on a page it applies to.
		if ( ! is_singular() ) {
			return;
		}

		global $post;

		// Prevent PHP notices.
		if ( isset( $post ) ) {
			$disable_header = get_post_meta( $post->ID, '_generate-disable-header', true );
			$disable_nav = get_post_meta( $post->ID, '_generate-disable-nav', true );
			$disable_secondary_nav = get_post_meta( $post->ID, '_generate-disable-secondary-nav', true );
			$disable_post_image = get_post_meta( $post->ID, '_generate-disable-post-image', true );
			$disable_headline = get_post_meta( $post->ID, '_generate-disable-headline', true );
			$disable_footer = get_post_meta( $post->ID, '_generate-disable-footer', true );
		}

		$return = '';

		if ( ! empty( $disable_header ) && false !== $disable_header ) {
			$return = '.site-header {display:none}';
		}

		if ( ! empty( $disable_nav ) && false !== $disable_nav ) {
			$return .= '#site-navigation,.navigation-clone, #mobile-header {display:none !important}';
		}

		if ( ! empty( $disable_secondary_nav ) && false !== $disable_secondary_nav ) {
			$return .= '#secondary-navigation {display:none}';
		}

		if ( ! empty( $disable_post_image ) && false !== $disable_post_image ) {
			$return .= '.generate-page-header, .page-header-image, .page-header-image-single {display:none}';
		}

		$need_css_removal = true;

		if ( defined( 'GENERATE_VERSION' ) && version_compare( GENERATE_VERSION, '3.0.0-alpha.1', '>=' ) ) {
			$need_css_removal = false;
		}

		if ( $need_css_removal && ! empty( $disable_headline ) && false !== $disable_headline && ! is_single() ) {
			$return .= '.entry-header {display:none} .page-content, .entry-content, .entry-summary {margin-top:0}';
		}

		if ( ! empty( $disable_footer ) && false !== $disable_footer ) {
			$return .= '.site-footer {display:none}';
		}

		return $return;
	}
}

if ( ! function_exists( 'generate_de_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'generate_de_scripts', 50 );
	/**
	 * Enqueue scripts and styles
	 */
	function generate_de_scripts() {
		wp_add_inline_style( 'generate-style', generate_disable_elements() );
	}
}

if ( ! function_exists( 'generate_add_de_meta_box' ) ) {
	add_action( 'add_meta_boxes', 'generate_add_de_meta_box', 50 );
	/**
	 * Generate the layout metabox.
	 *
	 * @since 0.1
	 */
	function generate_add_de_meta_box() {
		// Set user role - make filterable.
		$allowed = apply_filters( 'generate_metabox_capability', 'edit_theme_options' );

		// If not an administrator, don't show the metabox.
		if ( ! current_user_can( $allowed ) ) {
			return;
		}

		if ( defined( 'GENERATE_LAYOUT_META_BOX' ) ) {
			return;
		}

		$args = array( 'public' => true );
		$post_types = get_post_types( $args );
		foreach ( $post_types as $type ) {
			if ( 'attachment' !== $type ) {
				add_meta_box(
					'generate_de_meta_box',
					__( 'Disable Elements', 'gp-premium' ),
					'generate_show_de_meta_box',
					$type,
					'side',
					'default'
				);
			}
		}
	}
}

if ( ! function_exists( 'generate_show_de_meta_box' ) ) {
	/**
	 * Outputs the content of the metabox.
	 *
	 * @param object $post The post object.
	 */
	function generate_show_de_meta_box( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'generate_de_nonce' );
		$stored_meta = get_post_meta( $post->ID );
		$stored_meta['_generate-disable-header'][0] = ( isset( $stored_meta['_generate-disable-header'][0] ) ) ? $stored_meta['_generate-disable-header'][0] : '';
		$stored_meta['_generate-disable-nav'][0] = ( isset( $stored_meta['_generate-disable-nav'][0] ) ) ? $stored_meta['_generate-disable-nav'][0] : '';
		$stored_meta['_generate-disable-secondary-nav'][0] = ( isset( $stored_meta['_generate-disable-secondary-nav'][0] ) ) ? $stored_meta['_generate-disable-secondary-nav'][0] : '';
		$stored_meta['_generate-disable-post-image'][0] = ( isset( $stored_meta['_generate-disable-post-image'][0] ) ) ? $stored_meta['_generate-disable-post-image'][0] : '';
		$stored_meta['_generate-disable-headline'][0] = ( isset( $stored_meta['_generate-disable-headline'][0] ) ) ? $stored_meta['_generate-disable-headline'][0] : '';
		$stored_meta['_generate-disable-footer'][0] = ( isset( $stored_meta['_generate-disable-footer'][0] ) ) ? $stored_meta['_generate-disable-footer'][0] : '';
		$stored_meta['_generate-disable-top-bar'][0] = ( isset( $stored_meta['_generate-disable-top-bar'][0] ) ) ? $stored_meta['_generate-disable-top-bar'][0] : '';
		?>

		<p>
			<div class="generate_disable_elements">
				<?php if ( function_exists( 'generate_top_bar' ) ) : ?>
					<label for="meta-generate-disable-top-bar" style="display:block;margin-bottom:3px;" title="<?php _e( 'Top Bar', 'gp-premium' ); ?>">
						<input type="checkbox" name="_generate-disable-top-bar" id="meta-generate-disable-top-bar" value="true" <?php checked( $stored_meta['_generate-disable-top-bar'][0], 'true' ); ?>>
						<?php _e( 'Top Bar', 'gp-premium' ); ?>
					</label>
				<?php endif; ?>

				<label for="meta-generate-disable-header" style="display:block;margin-bottom:3px;" title="<?php _e( 'Header', 'gp-premium' ); ?>">
					<input type="checkbox" name="_generate-disable-header" id="meta-generate-disable-header" value="true" <?php checked( $stored_meta['_generate-disable-header'][0], 'true' ); ?>>
					<?php _e( 'Header', 'gp-premium' ); ?>
				</label>

				<label for="meta-generate-disable-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Primary Navigation', 'gp-premium' ); ?>">
					<input type="checkbox" name="_generate-disable-nav" id="meta-generate-disable-nav" value="true" <?php checked( $stored_meta['_generate-disable-nav'][0], 'true' ); ?>>
					<?php _e( 'Primary Navigation', 'gp-premium' ); ?>
				</label>

				<?php if ( function_exists( 'generate_secondary_nav_setup' ) ) : ?>
					<label for="meta-generate-disable-secondary-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Secondary Navigation', 'gp-premium' ); ?>">
						<input type="checkbox" name="_generate-disable-secondary-nav" id="meta-generate-disable-secondary-nav" value="true" <?php checked( $stored_meta['_generate-disable-secondary-nav'][0], 'true' ); ?>>
						<?php _e( 'Secondary Navigation', 'gp-premium' ); ?>
					</label>
				<?php endif; ?>

				<label for="meta-generate-disable-post-image" style="display:block;margin-bottom:3px;" title="<?php _e( 'Featured Image', 'gp-premium' ); ?>">
					<input type="checkbox" name="_generate-disable-post-image" id="meta-generate-disable-post-image" value="true" <?php checked( $stored_meta['_generate-disable-post-image'][0], 'true' ); ?>>
					<?php _e( 'Featured Image', 'gp-premium' ); ?>
				</label>

				<label for="meta-generate-disable-headline" style="display:block;margin-bottom:3px;" title="<?php _e( 'Content Title', 'gp-premium' ); ?>">
					<input type="checkbox" name="_generate-disable-headline" id="meta-generate-disable-headline" value="true" <?php checked( $stored_meta['_generate-disable-headline'][0], 'true' ); ?>>
					<?php _e( 'Content Title', 'gp-premium' ); ?>
				</label>

				<label for="meta-generate-disable-footer" style="display:block;margin-bottom:3px;" title="<?php _e( 'Footer', 'gp-premium' ); ?>">
					<input type="checkbox" name="_generate-disable-footer" id="meta-generate-disable-footer" value="true" <?php checked( $stored_meta['_generate-disable-footer'][0], 'true' ); ?>>
					<?php _e( 'Footer', 'gp-premium' ); ?>
				</label>
			</div>
		</p>

		<?php
	}
}

if ( ! function_exists( 'generate_save_de_meta' ) ) {
	add_action( 'save_post', 'generate_save_de_meta' );
	/**
	 * Save our options.
	 *
	 * @param int $post_id The post ID.
	 */
	function generate_save_de_meta( $post_id ) {

		if ( defined( 'GENERATE_LAYOUT_META_BOX' ) ) {
			return;
		}

		// Checks save status.
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['generate_de_nonce'] ) && wp_verify_nonce( $_POST['generate_de_nonce'], basename( __FILE__ ) ) ) ? true : false;

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Check that the logged in user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$options = array(
			'_generate-disable-top-bar',
			'_generate-disable-header',
			'_generate-disable-nav',
			'_generate-disable-secondary-nav',
			'_generate-disable-headline',
			'_generate-disable-footer',
			'_generate-disable-post-image',
		);

		foreach ( $options as $key ) {
			$value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRING );

			if ( $value ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}
}

if ( ! function_exists( 'generate_disable_elements_setup' ) ) {
	add_action( 'wp', 'generate_disable_elements_setup', 50 );
	/**
	 * Disable the things.
	 */
	function generate_disable_elements_setup() {
		// Don't run the function unless we're on a page it applies to.
		if ( ! is_singular() ) {
			return;
		}

		// Get the current post.
		global $post;

		// Grab our values.
		if ( isset( $post ) ) {
			$disable_top_bar = get_post_meta( $post->ID, '_generate-disable-top-bar', true );
			$disable_header = get_post_meta( $post->ID, '_generate-disable-header', true );
			$disable_mobile_header = get_post_meta( $post->ID, '_generate-disable-mobile-header', true );
			$disable_nav = get_post_meta( $post->ID, '_generate-disable-nav', true );
			$disable_headline = get_post_meta( $post->ID, '_generate-disable-headline', true );
			$disable_footer = get_post_meta( $post->ID, '_generate-disable-footer', true );
		}

		// Remove the top bar.
		if ( ! empty( $disable_top_bar ) && false !== $disable_top_bar && function_exists( 'generate_top_bar' ) ) {
			remove_action( 'generate_before_header', 'generate_top_bar', 5 );
			remove_action( 'generate_inside_secondary_navigation', 'generate_secondary_nav_top_bar_widget', 5 );
		}

		// Remove the header.
		if ( ! empty( $disable_header ) && false !== $disable_header && function_exists( 'generate_construct_header' ) ) {
			remove_action( 'generate_header', 'generate_construct_header' );
		}

		// Remove the mobile header.
		if ( ! empty( $disable_mobile_header ) && false !== $disable_mobile_header && function_exists( 'generate_menu_plus_mobile_header' ) ) {
			remove_action( 'generate_after_header', 'generate_menu_plus_mobile_header', 5 );
		}

		// Remove the navigation.
		if ( ! empty( $disable_nav ) && false !== $disable_nav && function_exists( 'generate_get_navigation_location' ) ) {
			add_filter( 'generate_navigation_location', '__return_false', 20 );
			add_filter( 'generate_disable_mobile_header_menu', '__return_true' );
		}

		// Remove the title.
		if ( ! empty( $disable_headline ) && false !== $disable_headline && function_exists( 'generate_show_title' ) ) {
			add_filter( 'generate_show_title', '__return_false' );
		}

		// Remove the footer.
		if ( ! empty( $disable_footer ) && false !== $disable_footer ) {
			if ( function_exists( 'generate_construct_footer_widgets' ) ) {
				remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
			}

			if ( function_exists( 'generate_construct_footer' ) ) {
				remove_action( 'generate_footer', 'generate_construct_footer' );
			}
		}
	}
}

add_action( 'generate_layout_disable_elements_section', 'generate_premium_disable_elements_options' );
/**
 * Add the meta box options to the Layout meta box in the new GP
 *
 * @since 1.4
 * @param array $stored_meta Existing meta data.
 */
function generate_premium_disable_elements_options( $stored_meta ) {
	$stored_meta['_generate-disable-header'][0] = ( isset( $stored_meta['_generate-disable-header'][0] ) ) ? $stored_meta['_generate-disable-header'][0] : '';
	$stored_meta['_generate-disable-mobile-header'][0] = ( isset( $stored_meta['_generate-disable-mobile-header'][0] ) ) ? $stored_meta['_generate-disable-mobile-header'][0] : '';
	$stored_meta['_generate-disable-nav'][0] = ( isset( $stored_meta['_generate-disable-nav'][0] ) ) ? $stored_meta['_generate-disable-nav'][0] : '';
	$stored_meta['_generate-disable-secondary-nav'][0] = ( isset( $stored_meta['_generate-disable-secondary-nav'][0] ) ) ? $stored_meta['_generate-disable-secondary-nav'][0] : '';
	$stored_meta['_generate-disable-post-image'][0] = ( isset( $stored_meta['_generate-disable-post-image'][0] ) ) ? $stored_meta['_generate-disable-post-image'][0] : '';
	$stored_meta['_generate-disable-headline'][0] = ( isset( $stored_meta['_generate-disable-headline'][0] ) ) ? $stored_meta['_generate-disable-headline'][0] : '';
	$stored_meta['_generate-disable-footer'][0] = ( isset( $stored_meta['_generate-disable-footer'][0] ) ) ? $stored_meta['_generate-disable-footer'][0] : '';
	$stored_meta['_generate-disable-top-bar'][0] = ( isset( $stored_meta['_generate-disable-top-bar'][0] ) ) ? $stored_meta['_generate-disable-top-bar'][0] : '';
	?>
	<div class="generate_disable_elements">
		<?php if ( function_exists( 'generate_top_bar' ) ) : ?>
			<label for="meta-generate-disable-top-bar" style="display:block;margin-bottom:3px;" title="<?php _e( 'Top Bar', 'gp-premium' ); ?>">
				<input type="checkbox" name="_generate-disable-top-bar" id="meta-generate-disable-top-bar" value="true" <?php checked( $stored_meta['_generate-disable-top-bar'][0], 'true' ); ?>>
				<?php _e( 'Top Bar', 'gp-premium' ); ?>
			</label>
		<?php endif; ?>

		<label for="meta-generate-disable-header" style="display:block;margin-bottom:3px;" title="<?php _e( 'Header', 'gp-premium' ); ?>">
			<input type="checkbox" name="_generate-disable-header" id="meta-generate-disable-header" value="true" <?php checked( $stored_meta['_generate-disable-header'][0], 'true' ); ?>>
			<?php _e( 'Header', 'gp-premium' ); ?>
		</label>

		<?php
		if ( function_exists( 'generate_menu_plus_get_defaults' ) ) :
			$menu_plus_settings = wp_parse_args(
				get_option( 'generate_menu_plus_settings', array() ),
				generate_menu_plus_get_defaults()
			);

			if ( 'enable' === $menu_plus_settings['mobile_header'] ) :
				?>
				<label for="meta-generate-disable-mobile-header" style="display:block;margin-bottom:3px;" title="<?php esc_attr_e( 'Mobile Header', 'gp-premium' ); ?>">
					<input type="checkbox" name="_generate-disable-mobile-header" id="meta-generate-disable-mobile-header" value="true" <?php checked( $stored_meta['_generate-disable-mobile-header'][0], 'true' ); ?>>
					<?php esc_html_e( 'Mobile Header', 'gp-premium' ); ?>
				</label>
				<?php
			endif;
		endif;
		?>

		<label for="meta-generate-disable-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Primary Navigation', 'gp-premium' ); ?>">
			<input type="checkbox" name="_generate-disable-nav" id="meta-generate-disable-nav" value="true" <?php checked( $stored_meta['_generate-disable-nav'][0], 'true' ); ?>>
			<?php _e( 'Primary Navigation', 'gp-premium' ); ?>
		</label>

		<?php if ( function_exists( 'generate_secondary_nav_setup' ) ) : ?>
			<label for="meta-generate-disable-secondary-nav" style="display:block;margin-bottom:3px;" title="<?php _e( 'Secondary Navigation', 'gp-premium' ); ?>">
				<input type="checkbox" name="_generate-disable-secondary-nav" id="meta-generate-disable-secondary-nav" value="true" <?php checked( $stored_meta['_generate-disable-secondary-nav'][0], 'true' ); ?>>
				<?php _e( 'Secondary Navigation', 'gp-premium' ); ?>
			</label>
		<?php endif; ?>

		<label for="meta-generate-disable-post-image" style="display:block;margin-bottom:3px;" title="<?php _e( 'Featured Image', 'gp-premium' ); ?>">
			<input type="checkbox" name="_generate-disable-post-image" id="meta-generate-disable-post-image" value="true" <?php checked( $stored_meta['_generate-disable-post-image'][0], 'true' ); ?>>
			<?php _e( 'Featured Image', 'gp-premium' ); ?>
		</label>

		<label for="meta-generate-disable-headline" style="display:block;margin-bottom:3px;" title="<?php _e( 'Content Title', 'gp-premium' ); ?>">
			<input type="checkbox" name="_generate-disable-headline" id="meta-generate-disable-headline" value="true" <?php checked( $stored_meta['_generate-disable-headline'][0], 'true' ); ?>>
			<?php _e( 'Content Title', 'gp-premium' ); ?>
		</label>

		<label for="meta-generate-disable-footer" style="display:block;margin-bottom:3px;" title="<?php _e( 'Footer', 'gp-premium' ); ?>">
			<input type="checkbox" name="_generate-disable-footer" id="meta-generate-disable-footer" value="true" <?php checked( $stored_meta['_generate-disable-footer'][0], 'true' ); ?>>
			<?php _e( 'Footer', 'gp-premium' ); ?>
		</label>
	</div>
	<?php
}

add_action( 'generate_layout_meta_box_save', 'generate_premium_save_disable_elements_meta' );
/**
 * Save the Disable Elements meta box values
 *
 * @since 1.4
 * @param int $post_id The post ID.
 */
function generate_premium_save_disable_elements_meta( $post_id ) {
	$options = array(
		'_generate-disable-top-bar',
		'_generate-disable-header',
		'_generate-disable-mobile-header',
		'_generate-disable-nav',
		'_generate-disable-secondary-nav',
		'_generate-disable-headline',
		'_generate-disable-footer',
		'_generate-disable-post-image',
	);

	foreach ( $options as $key ) {
		$value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRING );

		if ( $value ) {
			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}

add_filter( 'body_class', 'generate_disable_elements_body_classes', 20 );
/**
 * Remove body classes if certain elements are disabled.
 *
 * @since 2.1.0
 * @param array $classes Our body classes.
 */
function generate_disable_elements_body_classes( $classes ) {
	if ( is_singular() ) {
		$disable_featured_image = get_post_meta( get_the_ID(), '_generate-disable-post-image', true );
		$classes = generate_premium_remove_featured_image_class( $classes, $disable_featured_image );
	}

	return $classes;
}
