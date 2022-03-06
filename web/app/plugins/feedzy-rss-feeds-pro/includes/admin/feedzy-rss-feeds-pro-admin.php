<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://themeisle.com/plugins/feedzy-rss-feed-pro/
 * @since      1.0.0
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    feedzy-rss-feeds-pro
 * @subpackage feedzy-rss-feeds-pro/includes/admin
 * @author     Bogdan Preda <bogdan.preda@themeisle.com>
 */

/**
 * Class Feedzy_Rss_Feed_Pro_Admin
 */
class Feedzy_Rss_Feeds_Pro_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings for Feedzy PRO services.
	 *
	 * @since   1.3.2
	 * @access  public
	 * @var     array $settings The settings for Feedzy PRO.
	 */
	private $settings;

	/**
	 * The settings for Feedzy free.
	 *
	 * @access  public
	 * @var     array $settings The settings for Feedzy free.
	 */
	private $free_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @access      public
	 *
	 * @param       string $plugin_name The name of this plugin.
	 * @param       string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->settings      = get_option( 'feedzy-rss-feeds-settings', array() );
		$this->free_settings = get_option( 'feedzy-settings', array() );
	}

	/**
	 * The custom plugin_row_meta function
	 * Adds additional links on the plugins page for this plugin
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   array  $links The array having default links for the plugin.
	 * @param   string $file The name of the plugin file.
	 *
	 * @return  array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'feedzy-rss-feed-pro.php' ) !== false ) {
			$new_links = array(
				'doc'          => '<a href="http://docs.themeisle.com/article/277-feedzy-rss-feeds-hooks" target="_blank" title="' . __( 'Documentation and examples', 'feedzy-rss-feeds' ) . '">' . __( 'Documentation and examples', 'feedzy-rss-feeds' ) . '</a>',
				'more_plugins' => '<a href="http://themeisle.com/wordpress-plugins/" target="_blank" title="' . __( 'More Plugins', 'feedzy-rss-feeds' ) . '">' . __( 'More Plugins', 'feedzy-rss-feeds' ) . '</a>',
			);
			$links     = array_merge( $links, $new_links );
		}

		return $links;
	}

	/**
	 * Returns the custom field template required on the
	 * feed configuration screen to add custom fields.
	 *
	 * @param string $html HTML.
	 */
	public function custom_field_template( $html ) {
		if ( feedzy_is_pro() ) {
			$html .= '
				<div class="row">
					<div class="feedzy-row fields">
						<div class="form-group form_item">
							<input type="text" name="custom_vars_key[]" placeholder="' . __( 'Key Name', 'feedzy-rss-feeds' ) . '" class="form-control">
						</div>
						<div class="feedzy-separator dashicons dashicons-leftright"></div>
						<div class="form-group input-group form_item">
							<input type="text" name="custom_vars_value[]" placeholder="' . __( 'Value', 'feedzy-rss-feeds' ) . '" class="form-control">
							<div class="input-group-btn">
								<button type="button" class="btn btn-remove-fields">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			';
		}
		return $html;
	}

	/**
	 * Register required plugins default image for Feedzy with PRO version
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function register_required_plugins() {
		if ( ! function_exists( 'tgmpa' ) ) {
			include_once FEEDZY_PRO_ABSPATH . '/lib/tgmpa/tgm-plugin-activation/class-tgm-plugin-activation.php';
		}

		if ( function_exists( 'tgmpa' ) ) {
			add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );
			add_filter( 'tgmpa_notice_action_links', array( $this, 'remove_avada_conflict' ) );
		}
	}

	/**
	 * Removes the rude hijacking of links by avada by removing the entire button.
	 * NOTE: This will prevent users from seeing the "Begin installing plugins" links and they will have to install the plugins themselves.
	 *
	 * @param array $links Array of links, possibly containing Avada install link.
	 */
	public function remove_avada_conflict( $links ) {
		if ( strpos( $links['install'], 'avada' ) !== false ) {
			unset( $links['install'] );
		}
	}

	/**
	 * Initialize TGM.
	 */
	public function tgmpa_register() {
		$plugins = array(
			array(
				'name'     => 'Feedzy RSS Feeds Lite',
				'slug'     => 'feedzy-rss-feeds',
				'required' => true,
			),
		);
		$config  = array(
			'id'           => 'feedzy-rss-feeds-pro',
			// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',
			// Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',
			// Menu slug.
			'parent_slug'  => 'plugins.php',
			// Parent menu slug.
			'capability'   => 'manage_options',
			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,
			// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => 'Required',
			// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,
			// Automatically activate plugins after installation or not.
			'message'      => '',
			'strings'      => array(
				'page_title'                      => __( 'Install Required Plugins', 'feedzy-rss-feeds' ),
				'menu_title'                      => __( 'Install Plugins', 'feedzy-rss-feeds' ),
				/* translators: %s: plugin name. */
				'installing'                      => __( 'Installing Plugin: %s', 'feedzy-rss-feeds' ),
				/* translators: %s: plugin name. */
				'updating'                        => __( 'Updating Plugin: %s', 'feedzy-rss-feeds' ),
				'oops'                            => __( 'Something went wrong with the plugin API.', 'feedzy-rss-feeds' ),
				/* translators: 1: plugin name(s). */
				'notice_can_install_required'     => _n_noop(
					'Feedzy requires the following plugin: %1$s.',
					'Feedzy requires the following plugins: %1$s.',
					'feedzy-rss-feeds'
				),
				/* translators: 1: plugin name(s). */
				'notice_can_install_recommended'  => _n_noop(
					'Feedzy recommends the following plugin: %1$s.',
					'Feedzy recommends the following plugins: %1$s.',
					'feedzy-rss-feeds'
				),
				/* translators: 1: plugin name(s). */
				'notice_ask_to_update'            => _n_noop(
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with Feedzy: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with Feedzy: %1$s.',
					'feedzy-rss-feeds'
				),
				/* translators: 1: plugin name(s). */
				'notice_ask_to_update_maybe'      => _n_noop(
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					'feedzy-rss-feeds'
				),
				/* translators: 1: plugin name(s). */
				'notice_can_activate_required'    => _n_noop(
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					'feedzy-rss-feeds'
				),
				/* translators: 1: plugin name(s). */
				'notice_can_activate_recommended' => _n_noop(
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					'feedzy-rss-feeds'
				),
				'install_link'                    => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					'feedzy-rss-feeds'
				),
				'update_link'                     => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					'feedzy-rss-feeds'
				),
				'activate_link'                   => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					'feedzy-rss-feeds'
				),
				'return'                          => __( 'Return to Required Plugins Installer', 'feedzy-rss-feeds' ),
				'plugin_activated'                => __( 'Plugin activated successfully.', 'feedzy-rss-feeds' ),
				'activated_successfully'          => __( 'The following plugin was activated successfully:', 'feedzy-rss-feeds' ),
				/* translators: 1: plugin name. */
				'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'feedzy-rss-feeds' ),
				/* translators: 1: plugin name. */
				'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for Feedzy. Please update the plugin.', 'feedzy-rss-feeds' ),
				/* translators: 1: dashboard link. */
				'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'feedzy-rss-feeds' ),
				'dismiss'                         => __( 'Dismiss this notice', 'feedzy-rss-feeds' ),
				'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'feedzy-rss-feeds' ),
				'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'feedzy-rss-feeds' ),
				'nag_type'                        => '',
				// Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			),
		);
		tgmpa( $plugins, $config );
	}

	/**
	 * Returns the attributes of the shortcode for the PRO version
	 * Overrides the Lite method
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   array $atts The attributes passed by WordPress.
	 *
	 * @return array
	 */
	public function feedzy_pro_get_short_code_attributes( $atts ) {
		// Retrieve & extract shorcode parameters.
		$sc = shortcode_atts(
			array(
				'price'        => '',          // yes, no, auto (if price is shown).
				'referral_url' => '',   // the referral variables.
				'keywords_ban' => '',   // the keywords exclude var.
				'columns'      => '1',       // the columns number.
				'template'     => '',       // the template name.
				'mapping'      => '',       // the mapping for custom tags e.g. price=someCustomTag.
			),
			$atts,
			'feedzy_default'
		);

		return $sc;
	}

	/**
	 * Add grid class to item
	 *
	 * @since   1.0.0
	 * @access  private
	 *
	 * @param   array $classes The feed item classes.
	 * @param   array $sc The shortcode attributes.
	 *
	 * @return string
	 */
	public function add_grid_class( $classes = array(), $sc = array() ) {
		$classes[] = 'feedzy-rss-col-' . $sc['columns'];

		return $classes;
	}

	/**
	 * Check title for banned keywords
	 *
	 * @since   1.0.2
	 * @access  public
	 *
	 * @param   boolean $continue A boolean to stop the script.
	 * @param   array   $sc The shortcode attrs.
	 * @param   object  $item The feed item.
	 * @param   string  $feed_url The feed URL.
	 *
	 * @return  boolean
	 */
	public function item_keywords_ban( $continue, $sc, $item, $feed_url ) {
		$keywords_ban = $sc['keywords_ban'];
		if ( ! empty( $keywords_ban ) ) {
			foreach ( $keywords_ban as $keyword ) {
				if ( strpos( $item->get_title(), $keyword ) !== false || strpos( $item->get_content(), $keyword ) !== false ) {
					$continue = false;
				}
			}
		}

		return $continue;
	}

	/**
	 * Add attributes to $item_array.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   array  $item_array The item attributes array.
	 * @param   object $item The feed item.
	 * @param   array  $sc The shorcode attributes array.
	 * @param   int    $index The item number (may not be the same as the item_index).
	 * @param   int    $item_index The real index of this items in the feed (maybe be different from $index if filters are used).
	 *
	 * @return mixed
	 */
	public function add_data_to_item( $item_array, $item, $sc = null, $index = null, $item_index = null ) {
		$price                    = $this->retrive_price( $item, $sc, $item_index );
		$price                    = apply_filters( 'feedzy_price_output', $price );
		$item_array['item_price'] = $price;

		$media                    = $this->retrive_media( $item );
		$media                    = apply_filters( 'feedzy_media_output', $media );
		$item_array['item_media'] = $media;

		$item_array = $this->fetch_additional_content( $item_array, $item, $sc );
		return $item_array;
	}

	/**
	 * Retrive the price from feed
	 *
	 * @since   1.0.0
	 * @access  private
	 *
	 * @param   object $item The feed item.
	 * @param   array  $sc The shorcode attributes array.
	 * @param   int    $index The real index of this items in the feed.
	 *
	 * @return string
	 */
	private function retrive_price( $item, $sc = null, $index = null ) {
		$the_price = '';
		if ( empty( $the_price ) ) {
			$data = $item->get_item_tags( '', 'price' );
			if ( isset( $data[0]['data'] ) && ! empty( $data[0]['data'] ) ) {
				$the_price = $data[0]['data'];
			}
		}
		if ( empty( $the_price ) ) {
			$data = $item->get_item_tags( 'http://base.google.com/ns/1.0', 'price' );
			if ( isset( $data[0]['data'] ) && ! empty( $data[0]['data'] ) ) {
				$the_price = $data[0]['data'];
			}
		}
		if ( empty( $the_price ) ) {
			$data = $item->get_item_tags( 'http://www.ebay.com/marketplace/search/v1/services', 'CurrentPrice' );
			if ( isset( $data[0] ) && isset( $data[0]['data'] ) && ! empty( $data[0]['data'] ) ) {
				$the_price = $data[0]['data'];
			}
		}

		$the_price = apply_filters( 'feedzy_extract_from_custom_tag', $the_price, 'price', $item, $sc, $index );

		return $the_price;
	}

	/**
	 * Extracts a particular component (e.g. price) from a custom tag in the feed.
	 *
	 * @param   string|mixed $default    The default value of the component.
	 * @param   string       $name   The name of the component.
	 * @param   object       $item The feed item.
	 * @param   array        $sc The shorcode attributes array.
	 * @param   int          $index The real index of this items in the feed.
	 */
	public function extract_from_custom_tag( $default, $name, $item, $sc, $index ) {
		if ( is_null( $sc ) ) {
			return $default;
		}

		if ( ! $this->feedzy_is_business() ) {
			return $default;
		}

		$map = array();
		if ( $sc && ! empty( $sc['mapping'] ) ) {
			$array = explode( ',', $sc['mapping'] );
			if ( $array ) {
				foreach ( $array as $mapping ) {
					$array1 = explode( '=', $mapping );
					$tag    = $array1[1];
					if ( strpos( $tag, 'feed|' ) !== false ) {
						$tag = '[#feed_custom_' . str_replace( 'feed|', '', $tag ) . ']';
					} else {
						$tag = '[#item_custom_' . $tag . ']';
					}
					$map[ $array1[0] ] = $tag;
				}
			}
		}

		if ( ! array_key_exists( $name, $map ) ) {
			return $default;
		}

		$tag    = $map[ $name ];
		$result = $this->parse_custom_tags( $tag, $item->get_feed(), $index );
		return $result;
	}

	/**
	 * Retrive media form feed enclosure.
	 *
	 * @since   1.4.0
	 * @access  private
	 *
	 * @param   object $item The feed item.
	 *
	 * @return array
	 */
	private function retrive_media( $item ) {
		$enclosure = $item->get_enclosure();
		if ( isset( $enclosure ) ) {
			$type = $enclosure->type;
			if ( in_array( $type, apply_filters( 'feedzy_add_player_for_media_formats', array( 'audio/mpeg', 'audio/x-m4a', 'audio/mp3' ) ), true ) ) {
				return array(
					'src'      => $enclosure->link,
					'duration' => $enclosure->duration,
					'length'   => $enclosure->length,
					'type'     => $type,
				);
			}
		}

		return array();
	}

	/**
	 * Append referral params if the option is set.
	 *
	 * This will work for 2 different cases:
	 * 1) When the value contains #url#, #url# will be replaced with the URL of the feed item.
	 * Otherwise
	 * 2) The value will be appended to the URL of the feed item.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   string $item_link The item url.
	 * @param   array  $sc The shortcode attributes array.
	 *
	 * @return string
	 */
	public function referral_url( $item_link, $sc ) {
		$new_link = $item_link;
		if ( isset( $sc['referral_url'] ) && ! empty( $sc['referral_url'] ) ) {
			$value = $sc['referral_url'];
			if ( false !== strpos( $value, '#url#' ) ) {
				$new_link = str_replace( '#url#', $item_link, $value );
			} else {
				$parse_url = wp_parse_url( $item_link );
				if ( isset( $parse_url['query'] ) ) {
					$new_link = $item_link . '&' . $value;
				} else {
					$new_link = $item_link . '?' . $value;
				}
			}
		}

		return $new_link;
	}

	/**
	 * Render the content to be displayed for the PRO version
	 * Takes into account the PRO shortcode attributes
	 * Overrides the Lite method
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param   string $content The original content.
	 * @param   array  $sc The shorcode attributes array.
	 * @param   array  $feed_title The feed title array.
	 * @param   array  $feed_items The feed items array.
	 *
	 * @return string
	 */
	public function render_content( $content, $sc, $feed_title, $feed_items ) {
		if ( ! array_key_exists( 'item_url_follow', $sc ) ) {
			$sc['item_url_follow'] = '';
		}
		$template_name = 'default';
		if ( isset( $sc['template'] ) && '' !== $sc['template'] ) {
			$template_name = $sc['template'];
		}
		if ( $this->check_template_file_exists( $template_name ) ) {
			// this global is used in template-functions.php.
			global $_custom_feedzy_feed_title;
			$_custom_feedzy_feed_title = $feed_title;
			ob_start();
			include $this->get_template( $template_name );
			$content = ob_get_clean();

			return $content;
		} else {
			return $content;
		}
	}

	/**
	 * Checks if file exists in templates.
	 *
	 * @since   1.0.0
	 * @access  private
	 *
	 * @param   string $file_name The name of the file to check in templates (defaults to default).
	 *
	 * @return mixed
	 */
	private function check_template_file_exists( $file_name = 'default' ) {
		$user_template = get_stylesheet_directory() . '/feedzy_templates/' . $file_name . '.php';
		$file_path     = FEEDZY_PRO_ABSPATH . '/templates/' . $file_name . '.php';
		$default_path  = FEEDZY_PRO_ABSPATH . '/templates/default.php';
		if ( file_exists( $user_template ) ) {
			return $user_template;
		}
		if ( file_exists( $file_path ) ) {
			return $file_path;
		}
		if ( file_exists( $default_path ) ) {
			return $default_path;
		}

		return false;
	}

	/**
	 * Get the template content
	 *
	 * @since   1.0.0
	 * @access  private
	 *
	 * @param   string $file_name The name of the file to check in templates (defaults to default).
	 *
	 * @return string
	 */
	private function get_template( $file_name = 'default' ) {
		if ( $this->check_template_file_exists( $file_name ) !== false ) {
			return $this->check_template_file_exists( $file_name );
		}

		return FEEDZY_PRO_ABSPATH . '/templates/default.php';
	}

	/**
	 * Adds more options required by the metabox page.
	 *
	 * @param array $options Empty or filtered array.
	 * @param int   $job_id Post ID.
	 */
	public function add_metabox_options( $options, $job_id ) {
		return $options;
	}

	/**
	 * Shows additional rows in the metabox page as required by the license.
	 *
	 * @param   string  $html The default HTML shown in the metabox (empty string).
	 * @param   integer $job_id The post ID.
	 * @param   string  $row_slug The slug that indicates which portion of the file to show.
	 *                   This is important in scenarios where 2 rows need to be shown
	 *                   in 2 different locations. The slug will indicate which one needs to be shown.
	 */
	public function metabox_show_rows( $html, $job_id, $row_slug ) {
		$include_file = null;
		if ( apply_filters( 'feedzy_is_license_of_type', false, 'business' ) ) {
			$language_dropdown = $this->get_languages( $job_id );
			$include_file      = FEEDZY_PRO_ABSPATH . '/includes/views/metabox-business.php';
		}

		if ( $include_file ) {
			include $include_file;
		}
	}

	/**
	 * Get the languagues supported for full text content.
	 *
	 * @param int $job_id Post ID.
	 */
	private function get_languages( $job_id ) {
		if ( ! apply_filters( 'feedzy_is_license_of_type', false, 'business' ) ) {
			return null;
		}

		$language = get_post_meta( $job_id, 'import_feed_language', true );
		$dropdown = wp_dropdown_languages(
			array(
				'id'                          => 'feedzy_language',
				'name'                        => 'feedzy_meta_data[import_feed_language]',
				'show_available_translations' => true,
				'echo'                        => false,
				'selected'                    => $language,
			)
		);
		return str_replace( '<select ', '<select class="form-control feedzy-chosen" ', $dropdown );
	}

	/**
	 * Save method for custom post type
	 * import feeds.
	 *
	 * @since   1.2.0
	 * @access  public
	 *
	 * @param   integer $post_id The post ID.
	 * @param   object  $post The post object.
	 *
	 * @return bool
	 */
	public function save_feedzy_import_feed_meta( $post_id, $post ) {
		// phpcs:ignore
		$nonce = isset( $_POST['feedzy_category_meta_noncename'] ) ? esc_html( wp_unslash( $_POST['feedzy_category_meta_noncename'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, FEEDZY_BASEFILE ) ) {
			return;
		}
		$custom_fields_keys = array();
		if ( isset( $_POST['custom_vars_key'] ) && is_array( $_POST['custom_vars_key'] ) ) {
			// phpcs:ignore
			foreach ( wp_unslash( $_POST['custom_vars_key'] ) as $key => $var ) {
				$custom_fields_keys[ esc_html( $key ) ] = esc_html( $var );
			}
		}
		$custom_fields_values = array();
		if ( isset( $_POST['custom_vars_value'] ) && is_array( $_POST['custom_vars_value'] ) ) {
			// phpcs:ignore
			foreach ( wp_unslash( $_POST['custom_vars_value'] ) as $key => $var ) {
				$custom_fields_values[ esc_html( $key ) ] = esc_html( $var );
			}
		}
		$custom_fields = array();
		foreach ( $custom_fields_keys as $index => $key_value ) {
			$value = '';
			if ( isset( $custom_fields_values[ $index ] ) ) {
				$value = implode( ',', (array) $custom_fields_values[ $index ] );
			}
			$custom_fields[ $key_value ] = $value;
		}
		if ( 'revision' !== $post->post_type ) {
			if ( get_post_meta( $post_id, 'imports_custom_fields', false ) ) {
				update_post_meta( $post_id, 'imports_custom_fields', $custom_fields );
			} else {
				add_post_meta( $post_id, 'imports_custom_fields', $custom_fields );
			}
			if ( empty( $custom_fields ) ) {
				delete_post_meta( $post_id, 'imports_custom_fields' );
			}
		}
		return true;
	}

	/**
	 * Appends additional messages when showing the last run status.
	 *
	 * @param string $msg    Message for last run status.
	 * @param int    $job_id Post ID.
	 */
	public function run_status_errors( $msg, $job_id ) {
		$msg .= $this->show_service_errors( $job_id );
		return $msg;
	}

	/**
	 * The Cron Job.
	 *
	 * @since   1.2.0
	 * @access  public
	 * @param  WP_Post $job A WP_Post Object.
	 */
	public function run_cron_extra( $job ) {
		$this->delete_old_posts( $job );
	}

	/**
	 * Deletes posts created by a specific job.
	 *
	 * @since   1.6.5
	 * @access  private
	 * @return  void
	 * @param  WP_Post $job A WP_Post Object.
	 */
	private function delete_old_posts( $job ) {
		$days = intval( get_post_meta( $job->ID, 'import_feed_delete_days', true ) );
		if ( 0 === $days ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Not deleting any posts imported by %s', $job->post_title ), 'info', __FILE__, __LINE__ );
			return;
		}

		$import_post_type = get_post_meta( $job->ID, 'import_post_type', true );

		$query = new WP_Query(
			array(
				'post_type'      => $import_post_type,
				'post_status'    => 'any',
				'fields'         => 'ids',
				// phpcs:ignore
				'posts_per_page' => 300,
				// phpcs:ignore
				'meta_query'     => array(
					array(
						'key'   => 'feedzy_job',
						'value' => $job->ID,
					),
				),
				'date_query'     => array(
					array(
						'before' => "$days days ago",
					),
				),
			)
		);

		$count = 0;
		if ( $query->have_posts() ) {
			while ( $query->next_post() ) {
				wp_delete_post( $query->post );
				$count++;
			}
		}
		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Deleted %d posts imported by %s that were more than %d days old ', $count, $job->post_title, $days ), 'info', __FILE__, __LINE__ );
	}

	/**
	 * Adds additional options when running the cron job.
	 *
	 * @param array   $options Array of options.
	 * @param WP_Post $job     A WP_Post object of post type feedzy_imports.
	 */
	public function run_cron_options( $options, $job ) {
		if ( ! empty( $options['keywords_title'] ) ) {
			$options['keywords_title'] = rtrim( $options['keywords_title'], ',' );
			$options['keywords_title'] = array_map( 'trim', explode( ',', $options['keywords_title'] ) );
		}
		if ( ! empty( $options['keywords_ban'] ) ) {
			$options['keywords_ban'] = rtrim( $options['keywords_ban'], ',' );
			$options['keywords_ban'] = array_map( 'trim', explode( ',', $options['keywords_ban'] ) );
		}
		return $options;
	}

	/**
	 * Performs actions before running the cron job.
	 *
	 * @param WP_Post $job    A WP_Post object of post type feedzy_imports.
	 * @param array   $result $results['items'] from feedzy_run_job_pre action.
	 */
	public function run_job_pre( $job, $result ) {
		$this->remove_service_errors( $job );
	}

	/**
	 * Runs a specific job.
	 *
	 * @param   WP_Post $job The import job object.
	 * @param   array   $results The array that stores results.
	 * @param   int     $new_post_id The newly created import ID.
	 * @param   int     $index The item number (may not be the same as the item_index).
	 * @param   int     $item_index The real index of this items in the feed (maybe be different from $index if filters are used).
	 * @param   array   $import_errors The array that contains the import errors.
	 * @param   array   $import_info The array that contains the import info data.
	 *
	 * @since   ?
	 * @access  public
	 */
	public function import_extra( $job, $results, $new_post_id, $index, $item_index = null, $import_errors = null, $import_info = null ) {
		$import_custom_fields = get_post_meta( $job->ID, 'imports_custom_fields', true );
		if ( ! empty( $import_custom_fields ) ) {
			foreach ( $import_custom_fields as $key => $value ) {
				if ( $value && $this->feedzy_is_business() ) {
					$value = apply_filters( 'feedzy_parse_custom_tags', $value, $results['feed'], is_null( $item_index ) ? $index : $item_index );
				}

				if ( get_post_meta( $new_post_id, $key, false ) ) {
					update_post_meta( $new_post_id, $key, $value );
				} else {
					add_post_meta( $new_post_id, $key, $value );
				}
				if ( ! $value ) {
					delete_post_meta( $new_post_id, $key );
				}
			}
		}
	}

	/**
	 * Method to extract and parse custom tags feed items to use on cron job.
	 *
	 * The custom tags can be defined in these ways:
	 * - [#item_custom_x] will extract text from an item-level element <x>.
	 * - [#item_custom_y:x] will extract text from an item-level element <y:x>.
	 * - [#item_custom_x@z] will extract text from an attribute 'z' inside an item-level element <x>.
	 * - [#item_custom_y:x@z] will extract text from an attribute 'z' inside an item-level element <y:x>.
	 * - [#feed_custom_y:x@z] will extract text from an attribute 'z' inside the feed-level element <y:x>.
	 *
	 * @since   ?
	 * @access  public
	 *
	 * @param   string    $content    The content from where to extract the custom tags.
	 * @param   SimplePie $feed       The SimplePie feed object.
	 * @param   int       $index      The index of the element to fetch.
	 *
	 * @return string
	 */
	public function parse_custom_tags( $content, $feed, $index ) {
		// only allow this for single feed.
		if ( ! empty( $feed->multifeed_url ) ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, 'This is only supported for single URL feeds. Skipping.', 'info', __FILE__, __LINE__ );
			return $content;
		}

		$has_custom_tags = strpos( $content, '#item_custom_' ) !== false || strpos( $content, '#feed_custom_' ) !== false;

		if ( ! $has_custom_tags ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s does not contain any custom tags. Skipping.', $content ), 'warn', __FILE__, __LINE__ );
			return $content;
		}

		// item related data.
		preg_match_all( '/\[(#item_custom_)([a-zA-Z0-9:@\-]*)\]/i', $content, $item_matches );
		// feed related data.
		preg_match_all( '/\[(#feed_custom_)([a-zA-Z0-9:@\-]*)\]/i', $content, $feed_matches );

		if ( ! is_array( $item_matches ) && ! is_array( $item_matches[0] ) && ! is_array( $feed_matches ) && ! is_array( $feed_matches[0] ) ) {
			// phpcs:ignore
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s does not match custom tags in the content = %s', print_r( array_merge( $item_matches, $feed_matches ), true ), $content ), 'warn', __FILE__, __LINE__ );
			return $content;
		}

		$new_content = $content;

		$sxe = null;
		libxml_use_internal_errors( true );
		try {
			$sxe = new SimpleXMLElement( $feed->feed_url, LIBXML_NOCDATA, true );
		} catch ( Exception $ex ) {
			// if for some reason the URL is not being directly parsed
			// we will fetch it manually.
			$content = wp_remote_retrieve_body( wp_remote_get( $feed->feed_url ) );
			if ( ! empty( $content ) ) {
				$sxe = new SimpleXMLElement( $content, LIBXML_NOCDATA, false );
			} else {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Unable to fetch URL %s manually or automatically', $feed->feed_url ), 'error', __FILE__, __LINE__ );
			}
		}

		if ( is_null( $sxe ) ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, 'SimpleXMLElement is null; will abort parsing of custom tags', 'error', __FILE__, __LINE__ );
			return $content;
		}

		foreach ( $sxe->getNamespaces( true ) as $prefix => $ns ) {
			if ( strlen( $prefix ) === 0 ) {
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Namespace %s has no prefix; prefixing with themeisle', $ns ), 'debug', __FILE__, __LINE__ );
				// assign an arbitrary namespace prefix.
				$prefix = 'themeisle';
			}
			$sxe->registerXPathNamespace( $prefix, $ns );
		}

		// for ATOM feeds we have to prefix a tag, not so for RSS feeds.
		$prefix    = 'themeisle:';
		$feed_type = $feed->get_type();
		$item_tag  = 'entry';
		if ( $feed_type & SIMPLEPIE_TYPE_RSS_ALL ) {
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Treating this feed of type %d as an RSS feed', $feed_type ), 'debug', __FILE__, __LINE__ );
			$item_tag = 'item';
			$prefix   = '';
		}

		// Get item elements.
		if ( is_array( $item_matches[0] ) && is_array( $item_matches[2] ) && ! empty( $item_matches[0] ) && ! empty( $item_matches[2] ) ) {
			$tags = array_combine( $item_matches[0], $item_matches[2] );
			foreach ( $tags as $tag => $element ) {
				$attribute = '';
				if ( strpos( $element, '@' ) !== false ) {
					$array     = explode( '@', $element );
					$element   = $array[0];
					$attribute = $array[1];
				}

				// Prefix only if the element is not already prefixed with a namespace.
				if ( strpos( $element, ':' ) === false ) {
					$element = $prefix . $element;
				}

				$xpath = empty( $attribute ) ? "//{$prefix}{$item_tag}//{$element}/text()" : "//{$prefix}{$item_tag}//{$element}/@{$attribute}";

				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s: going to extract from %s for item %d', $tag, $xpath, $index ), 'debug', __FILE__, __LINE__ );

				$eval = $sxe->xpath( $xpath );

				if ( false === $eval ) {
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Nothing found corresponding to %s for item %d. Skipping.', $xpath, $index ), 'error', __FILE__, __LINE__ );
					$new_content = str_replace( $tag, '', $new_content );
					continue;
				}

				if ( $index > count( $eval ) - 1 ) {
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Number of elements extracted by xpath %s (%d) is less than the item being parsed in SimplePie (%d). Skipping this item.', $xpath, count( $eval ), $index ), 'error', __FILE__, __LINE__ );
					$new_content = str_replace( $tag, '', $new_content );
					continue;
				}

				if ( ! empty( $attribute ) ) {
					$store_evals = array();
					foreach ( range( 1, count( $sxe->channel->item ) + 1 ) as $key => $e ) {
						$xpath  = "//{$prefix}{$item_tag}[$key]//{$element}/@{$attribute}";
						$store_evals[] = $sxe->xpath( $xpath );
					}
					$store_evals = array_values(
						array_filter(
							$store_evals,
							function( $attr_val ) {
								$attr_val = array_filter( $attr_val );
								return $attr_val;
							}
						)
					);

					$merge_eval = array();
					if ( ! empty( $store_evals ) ) {
						if ( count( $store_evals ) > 1 ) {
							foreach ( $store_evals as $key => $store_eval ) {
								if ( ! empty( $store_eval ) ) {
									foreach ( $store_eval as $data ) {
										$merge_eval[ $key ][] = (string) $data;
									}
								}
							}
						} else {
							$merge_eval = reset( $store_evals );
						}
					}
					if ( is_array( $merge_eval[ $index ] ) ) {
						$merge_eval[ $index ] = implode( ' ', $merge_eval[ $index ] );
					}
					$text   = (string) $merge_eval[ $index ];
				} else {
					$text   = (string) $eval[ $index ];
				}

				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s: extracted %s', $tag, $text ), 'debug', __FILE__, __LINE__ );

				$text = apply_filters( 'feedzy_custom_magic_tag_format', $text, $tag, $feed, $index );

				$new_content = str_replace( $tag, $text, $new_content );
			}
		}

		// Get feed elements.
		if ( is_array( $feed_matches[0] ) && is_array( $feed_matches[2] ) && ! empty( $feed_matches[0] ) && ! empty( $feed_matches[2] ) ) {
			$tags = array_combine( $feed_matches[0], $feed_matches[2] );

			foreach ( $tags as $tag => $element ) {
				$attribute = '';
				if ( strpos( $element, '@' ) !== false ) {
					$array     = explode( '@', $element );
					$element   = $array[0];
					$attribute = $array[1];
				}

				// Prefix only if the element is not already prefixed with a namespace.
				if ( strpos( $element, ':' ) === false ) {
					$element = $prefix . $element;
				}

				$xpath = empty( $attribute ) ? "//{$element}/text()" : "//{$element}/@{$attribute}";

				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s: going to extract from %s', $tag, $xpath ), 'debug', __FILE__, __LINE__ );

				$eval = $sxe->xpath( $xpath );

				if ( false === $eval || 0 === count( $eval ) ) {
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Nothing found corresponding to attribute %s from feed element %s. Skipping.', $attribute, $element ), 'error', __FILE__, __LINE__ );
					$new_content = str_replace( $tag, '', $new_content );
					continue;
				}

				$text = (string) $eval[0];

				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s: extracted %s', $tag, $text ), 'debug', __FILE__, __LINE__ );

				$text = apply_filters( 'feedzy_custom_magic_tag_format', $text, $tag, $feed, $index );

				$new_content = str_replace( $tag, $text, $new_content );
			}
		}

		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Changed %s to %s', $content, $new_content ), 'debug', __FILE__, __LINE__ );

		return $new_content;

	}

	/**
	 * Get additional client data while invoking the full post feed URL.
	 *
	 * @access  private
	 *
	 * @return array
	 */
	private function get_additional_client_data() {
		$data = array();

		// if license does not exist, use the site url
		// this should obviously never happen unless on dev instances.
		$data['license'] = sprintf( 'n/a - %s', get_site_url() );
		$license_data    = get_option( 'feedzy_rss_feeds_pro_license_data', '' );
		if ( ! empty( $license_data ) && isset( $license_data->key ) ) {
			$data['license'] = $license_data->key;
		}

		return $data;
	}

	/**
	 * Get full feed URL, if supported by the license.
	 *
	 * @access  public
	 *
	 * @param   mixed  $feed_url The original url(s).
	 * @param   string $import_content The import content (along with the magic tags).
	 * @param   array  $options The options for the job.
	 *
	 * @return mixed
	 */
	public function import_feed_url( $feed_url, $import_content, $options ) {
		$is_business = $this->feedzy_is_business();

		do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'business user = *%s*, import_content = %s', $is_business, $import_content ), 'debug', __FILE__, __LINE__ );

		if ( $is_business && $import_content && ( false !== strpos( $import_content, '[#item_full_content' ) || false !== strpos( $import_content, '[#full_content' ) ) ) {
			$response = wp_remote_post(
				FEEDZY_PRO_FULL_CONTENT_URL,
				apply_filters(
					'feedzy_full_content_attributes',
					array(
						'timeout' => 100,
						'body'    => array_merge(
							array(
								'feeds' => is_array( $feed_url ) ? implode( '|||', $feed_url ) : $feed_url,
								'cache' => '12_hours',
							),
							$this->get_additional_client_data()
						),
					)
				)
			);

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'feedURL = %s, response = %s', print_r( $feed_url, true ), print_r( $response, true ) ), 'debug', __FILE__, __LINE__ );

			if ( ! is_wp_error( $response ) ) {
				if ( array_key_exists( 'response', $response ) && array_key_exists( 'code', $response['response'] ) && intval( $response['response']['code'] ) !== 200 ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'error in response = %s', print_r( $response, true ) ), 'error', __FILE__, __LINE__ );
				}
				$body = wp_remote_retrieve_body( $response );
				if ( ! is_wp_error( $body ) ) {
					$json = json_decode( $body, true );
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'json = %s', print_r( $json, true ) ), 'debug', __FILE__, __LINE__ );

					if ( is_array( $json ) ) {
						if ( array_key_exists( 'code', $json ) ) {
							return new WP_Error( $json['code'], $json['message'] );
						} elseif ( array_key_exists( 'url', $json ) ) {
							// let's find out what language to use for this full content import.
							$job_id   = $options['__jobID'];
							$language = '';
							if ( $job_id ) {
								$language = get_post_meta( $job_id, 'import_feed_language', true );
							}

							do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Using language = %s for full content import job = %d', $language, $job_id ), 'debug', __FILE__, __LINE__ );

							$feed_url = add_query_arg(
								array(
									'count'    => $options['max'],
									'language' => $language,
								),
								$json['url']
							);
							do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'full content url = %s', $feed_url ), 'info', __FILE__, __LINE__ );
							add_action( 'feedzy_modify_feed_config', array( $this, 'feedzy_modify_feed_config' ), 10, 1 );
							add_filter( 'feedzy_item_filter', array( $this, 'populate_middleware_content' ), 999, 2 );
						}
					} else {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'error in body = %s', print_r( $body, true ) ), 'error', __FILE__, __LINE__ );
					}
				} else {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'error in body = %s', print_r( $body, true ) ), 'error', __FILE__, __LINE__ );
				}
			} else {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'error in request = %s', print_r( $response, true ) ), 'error', __FILE__, __LINE__ );
			}
		}

		return $feed_url;
	}

	/**
	 * Modifies the feed object before it is processed.
	 *
	 * @access  public
	 *
	 * @param   SimplePie $feed SimplePie object.
	 */
	public function feedzy_modify_feed_config( $feed ) {
		// @codingStandardsIgnoreStart
		// set_time_limit(0);
		// @codingStandardsIgnoreEnd
		$feed->set_timeout( 60 );
	}

	/**
	 * Populates the content from the middleware feed into the item array for further use.
	 *
	 * @access  public
	 *
	 * @param   array          $item_array Array of items.
	 * @param   SimplePie_Item $item SimplePie_Item object.
	 *
	 * @return array
	 */
	public function populate_middleware_content( $item_array, $item ) {
		$content = $item->get_item_tags( SIMPLEPIE_NAMESPACE_ATOM_10, 'full-content' );
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		do_action( 'themeisle_log_event', FEEDZY_NAME, 'full content = ' . print_r( $content, true ), 'debug', __FILE__, __LINE__ );
		$content                         = $content[0]['data'];
		$item_array['item_full_content'] = $content;

		// if full content is empty, check if there is an error
		// at the item or feed level.
		if ( empty( $content ) ) {
			// at item level.
			$error = $item->get_item_tags( SIMPLEPIE_NAMESPACE_ATOM_10, 'error' );
			if ( is_array( $error ) && ! empty( $error ) ) {
				$error_msg = $error[0]['data'];
			} else {
				// at feed level.
				$error = $item->feed->get_feed_tags( SIMPLEPIE_NAMESPACE_ATOM_10, 'error' );
				if ( is_array( $error ) && ! empty( $error ) ) {
					$error_msg = $error[0]['data'];
				}
			}
			$item_array['full_content_error'] = $error_msg;
		}
		return $item_array;
	}

	/**
	 * Method to return license status.
	 * Used to filter PRO version types.
	 *
	 * @since   1.2.0
	 * @access  private
	 * @return bool
	 */
	private function feedzy_is_business() {
		return apply_filters( 'feedzy_is_license_of_type', false, 'business' );
	}

	/**
	 * Method to return if licence is agency.
	 *
	 * @since   1.3.2
	 * @access  private
	 * @return bool
	 */
	private function feedzy_is_agency() {
		return apply_filters( 'feedzy_is_license_of_type', false, 'agency' );
	}


	/**
	 * Method for updating settings page via AJAX.
	 *
	 * @since   1.3.2
	 * @access  public
	 */
	public function update_settings_page() {
		$post_data = array();
		if ( ! check_ajax_referer( 'update_settings_page' ) ) {
			exit;
		}
		if ( isset( $_POST['feedzy_settings'] ) && is_array( $_POST['feedzy_settings'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			foreach ( wp_unslash( $_POST['feedzy_settings'] ) as $key => $val ) {
				$post_data[ esc_html( $key ) ] = esc_html( $val );
			}
		}

		$this->check_services( $post_data );

		$this->save_settings();
		wp_send_json_success();
	}

	/**
	 * Check service status once every hour. This is called when the spinner is called.
	 *
	 * @param string $slug Service slug used as settings prefix.
	 */
	private function check_status_of_service( $slug ) {
		$last  = $this->settings[ "{$slug}_last_check" ];
		$error = $this->settings[ "{$slug}_message" ];

		$then = empty( $last ) ? DateTime::createFromFormat( 'U', 0 ) : DateTime::createFromFormat( 'd/m/Y H:i:s', $last );
		if ( time() - $then->format( 'U' ) > HOUR_IN_SECONDS ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$name = $addon->get_service_slug();
					if ( $name !== $slug ) {
						continue;
					}
					$post_data = $this->settings;
					$addon->check_api( $post_data, $this->settings );
					$this->settings = array_merge( $this->settings, $post_data );
					$this->save_settings();
					$error = $post_data[ "{$slug}_message" ];
				}
			}
		}
		return empty( $error ) ? null : $error;
	}

	/**
	 * Invoke the additional services.
	 *
	 * @access  public
	 */
	public function invoke_services( $field, $type, $text, $job ) {
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();

			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$name = $addon->get_service_slug();
					$tag  = "[#{$type}_{$name}]";

					// no tag, bail!
					if ( strpos( $field, $tag ) === false ) {
						continue;
					}

					do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Going to call service %s for tag %s', $name, $tag ), 'debug', __FILE__, __LINE__ );

					// let's check account status before spinning.
					$error = $this->check_status_of_service( $name );
					if ( null !== $error ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( '%s account appears to have a problem %s', $name, print_r( $error, true ) ), 'error', __FILE__, __LINE__ );
						update_post_meta( $job->ID, "{$name}_errors", array( array( 'message' => $error ) ) );
						return null;
					}

					$additional = array( 'lang' => get_post_meta( $job->ID, 'import_feed_language', true ) );
					// we will apply strip_tags as a fail-safe (e.g. in case of full text content it contains HTML).
					$spun = $addon->call_api( $this->settings, strip_tags( $text, '<br>' ), $type, $additional );
					if ( is_null( $spun ) || is_wp_error( $spun ) ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						do_action( 'themeisle_log_event', FEEDZY_NAME, sprintf( 'Error while calling service %s = %s', $name, print_r( $spun, true ) ), 'error', __FILE__, __LINE__ );
						update_post_meta( $job->ID, "{$name}_errors", $addon->get_api_errors() );
						return null;
					} elseif ( ! is_null( $spun ) ) {
						// when we get back the spun text, we may get back HTML tags.
						// they are only relevant for content, not for titles.
						// so for titles, we strip the HTML tags.
						if ( strpos( $tag, '#title_' ) !== false ) {
							$spun = wp_strip_all_tags( $spun );
						}
						$field = str_replace( $tag, $spun, $field );
					}
				}
			}
		}
		return $field;
	}


	/**
	 * Show errors corresponding to the additional services.
	 *
	 * @access  private
	 */
	private function show_service_errors( $post_id ) {
		$msg = '';
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$name = $addon->get_service_slug();

					$errors = get_post_meta( $post_id, "{$name}_errors", true );
					if ( $errors ) {
						$msg .= '<div class="feedzy-error feedzy-api-error">';
						foreach ( $errors as $error ) {
							$msg .= '<br>' . sprintf( __( '%1$s: %2$s', 'feedzy-rss-feeds' ), ucwords( $name ), $error['message'] );
						}
						$msg .= '</div>';
					}
				}
			}
		}
		return $msg;
	}

	/**
	 * Removes errors corresponding to the additional services.
	 *
	 * @access  private
	 */
	private function remove_service_errors( $job ) {
		$addons = $this->get_services();
		if ( $addons ) {
			foreach ( $addons as $addon ) {
				$name = $addon->get_service_slug();
				delete_post_meta( $job->ID, "{$name}_errors" );
			}
		}
	}

	/**
	 * Determine all the additional services that are supported.
	 *
	 * @access  private
	 */
	private function get_services() {
		if ( ! $this->feedzy_is_agency() ) {
			return null;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;
		$plugin_path = str_replace( ABSPATH, $wp_filesystem->abspath(), FEEDZY_PRO_ABSPATH );
		$addons_path = trailingslashit( $plugin_path ) . '/includes/admin/services/';
		$files       = $wp_filesystem->dirlist( $addons_path, false, true );

		if ( ! $files ) {
			return null;
		}
		$addons = array();
		$files  = array_keys( $files );
		foreach ( $files as $file ) {
			if ( strpos( $file, 'interface' ) !== false ) {
				continue;
			}

			$class    = str_replace( ' ', '_', ucwords( trim( str_replace( array( '-', '.php' ), ' ', $file ) ) ) );
			$addon    = new $class();
			$addons[] = $addon;
		}

		return $addons;
	}

	/**
	 * Check the status of the additional services.
	 *
	 * @access  private
	 */
	private function check_services( $post_data ) {
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$addon->check_api( $post_data, $this->settings );
					$this->settings = array_merge( $this->settings, $post_data );
				}
			}
		}
	}

	/**
	 * Add the magic tags corresponding to the additional services.
	 *
	 * @access  public
	 */
	public function get_service_magic_tags( $magic_tags, $type ) {
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$magic_tags[] = "{$type}_{$addon->get_service_slug()}";
				}
			}
		}
		return $magic_tags;
	}

	/**
	 * Method to save settings.
	 *
	 * @since   1.3.2
	 * @access  private
	 */
	private function save_settings() {
		update_option( 'feedzy-rss-feeds-settings', $this->settings );
	}

	/**
	 * Add settings tab.
	 *
	 * @since   1.3.2
	 * @access  public
	 */
	public function settings_tabs( $tabs ) {
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$tabs[ $addon->get_service_slug() ] = $addon->get_service_name_proper();
				}
			}
		}
		return $tabs;
	}

	/**
	 * Render a view page.
	 *
	 * @since   1.3.2
	 * @access  public
	 *
	 * @param   string $file The default file being included.
	 * @param   string $name The name of the view.
	 *
	 * @return string
	 */
	public function render_view( $file, $name ) {
		if ( file_exists( FEEDZY_PRO_ABSPATH . '/includes/views/' . $name . '-view.php' ) ) {
			return FEEDZY_PRO_ABSPATH . '/includes/views/' . $name . '-view.php';
		}
		return $file;
	}

	/**
	 * Renders the tags for the title.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_title( $default ) {
		return apply_filters( 'feedzy_agency_magic_tags_title', $default );
	}

	/**
	 * Renders the tags for the date.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_date( $default ) {
		return apply_filters( 'feedzy_agency_magic_tags_date', $default );
	}

	/**
	 * Renders the tags for the content.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_content( $default ) {
		if ( $this->feedzy_is_business() ) {
			$default['item_full_content'] = __( 'Item Full Content', 'feedzy-rss-feeds' );
		} else {
			$default['item_full_content:disabled'] = __( 'Item Full Content', 'feedzy-rss-feeds' );
		}
		return apply_filters( 'feedzy_agency_magic_tags_content', $default );
	}

	/**
	 * Renders the tags for the featured image.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function magic_tags_image( $default ) {
		return apply_filters( 'feedzy_agency_magic_tags_image', $default );
	}


	/**
	 * Renders the tags for the title, for the agency.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function agency_magic_tags_title( $default ) {
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$default[ 'title_' . $addon->get_service_slug() ] = sprintf( __( 'Title from %s', 'feedzy-rss-feeds' ), $addon->get_service_name_proper() );
				}
			}
		} else {
			$default['title_spinnerchief:disabled'] = __( 'Title from SpinnerChief', 'feedzy-rss-feeds' );
			$default['title_wordai:disabled']       = __( 'Title from WordAI', 'feedzy-rss-feeds' );
		}
		return $default;
	}


	/**
	 * Renders the tags for the content, for the agency.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function agency_magic_tags_content( $default ) {
		if ( $this->feedzy_is_agency() ) {
			$addons = $this->get_services();
			if ( $addons ) {
				foreach ( $addons as $addon ) {
					$default[ 'content_' . $addon->get_service_slug() ] = sprintf( __( 'Content from %s', 'feedzy-rss-feeds' ), $addon->get_service_name_proper() );
					if ( $this->feedzy_is_business() ) {
						$default[ 'full_content_' . $addon->get_service_slug() ] = sprintf( __( 'Full content from %s', 'feedzy-rss-feeds' ), $addon->get_service_name_proper() );
					}
				}
			}
		} else {
			$default['content_spinnerchief:disabled']      = __( 'Content from SpinnerChief', 'feedzy-rss-feeds' );
			$default['full_content_spinnerchief:disabled'] = __( 'Full content from SpinnerChief', 'feedzy-rss-feeds' );
			$default['content_wordai:disabled']            = __( 'Content from WordAI', 'feedzy-rss-feeds' );
			$default['full_content_wordai:disabled']       = __( 'Full content from WordAI', 'feedzy-rss-feeds' );
		}
		return $default;
	}

	/**
	 * Renders the tags for the date, for the agency.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function agency_magic_tags_date( $default ) {
		return $default;
	}

	/**
	 * Renders the tags for the featured image, for the agency.
	 *
	 * @since   1.4.2
	 * @access  public
	 *
	 * @param   array $default The default tags, empty.
	 */
	public function agency_magic_tags_image( $default ) {
		return $default;
	}

	/**
	 * Fetches additional information for each item.
	 *
	 * @access  public
	 *
	 * @param   array  $item_array The item attributes array.
	 * @param   object $item The feed item.
	 * @param   array  $sc The shorcode attributes array. This will be empty through the block editor.
	 */
	private function fetch_additional_content( $item_array, $item, $sc ) {
		$url = '';
		if ( array_key_exists( 'item_url', $item_array ) ) {
			$url = $item_array['item_url'];
		} elseif ( $item ) {
			$url = $item->get_permalink();
		}

		if ( empty( $url ) ) {
			return $item_array;
		}

		$host = wp_parse_url( $url, PHP_URL_HOST );
		// remove all dots in the hostname so that shortforms such as youtu.be can also be resolved.
		$host = str_replace( array( '.', 'www' ), '', $host );

		// youtube.
		if ( in_array( $host, array( 'youtubecom', 'youtube' ), true ) ) {
			$tags = $item->get_item_tags( SIMPLEPIE_NAMESPACE_MEDIARSS, 'group' );
			$desc = '';
			if ( $tags ) {
				$desc_tag = $tags[0]['child'][ SIMPLEPIE_NAMESPACE_MEDIARSS ]['description'];
				if ( $desc_tag ) {
					$desc = $desc_tag[0]['data'];
				}
			}

			if ( ! empty( $desc ) ) {
				if ( empty( $item_array['item_content'] ) ) {
					$item_array['item_content'] = $desc;
				}
				if ( ( empty( $sc ) || 'yes' === $sc['summary'] ) && empty( $item_array['item_description'] ) ) {
					$item_array['item_description'] = $desc;
				}
			}
			$item_array['item_content'] .= '[video src="' . $url . '"]';
		}
		return $item_array;
	}

	/**
	 * Check if plugin has been activated and then redirect to the correct page.
	 *
	 * @access  public
	 */
	public function admin_init() {
		if ( defined( 'TI_UNIT_TESTING' ) ) {
			return;
		}

		if ( get_option( 'feedzy-pro-activated' ) ) {
			delete_option( 'feedzy-pro-activated' );
			if ( ! headers_sent() ) {
				if ( ! defined( 'FEEDZY_BASEFILE' ) ) {
					wp_redirect(
						add_query_arg(
							array(
								'plugin_status' => 'all',
							), admin_url( 'plugins.php' )
						)
					);
				} else {
					wp_redirect(
						add_query_arg(
							array(
								'page' => 'feedzy-support',
								'tab' => 'help#import',
							), admin_url( 'admin.php' )
						)
					);
				}
				exit();
			}
		}
	}

	/**
	 * Add support tabs headings.
	 */
	public function support_tab_heading( $default, $active_tab ) {
		return '
		<a href="' . esc_url( admin_url( 'admin.php?page=feedzy-support&tab=contact' ) ) . '"
		   class="nav-tab ' . ( 'contact' === $active_tab ? 'nav-tab-active' : '' ) . '">' . __( 'Contact Us', 'feedzy-rss-feeds' ) . '</a>';

	}

	/**
	 * Add support tabs content.
	 *
	 * @param string $default    File path.
	 * @param string $active_tab Title of tab.
	 */
	public function support_tab_content( $default, $active_tab ) {
		switch ( $active_tab ) {
			case 'contact':
				$default = FEEDZY_PRO_ABSPATH . '/includes/views/contact-us.php';
				break;
		}

		return $default;
	}

	/**
	 * View feedzy settings page.
	 */
	public function view_feedzy_settings() {
		add_filter( 'feedzy_wp_kses_allowed_html', array( $this, 'feedzy_wp_kses_allowed_html' ) );
	}

	/**
	 * Filters the HTML that is allowed for a given setting content.
	 *
	 * @param array $allowed_html Allowed HTML.
	 * @return array
	 */
	public function feedzy_wp_kses_allowed_html( $allowed_html ) {
		$allowed_html['script'] = array(
			array(
				'type' => array(),
				'src' => array(),
			),
		);
		$allowed_html['button']['onclick'] = array();
		return $allowed_html;
	}
}
