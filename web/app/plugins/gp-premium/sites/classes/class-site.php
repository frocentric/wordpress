<?php
defined( 'WPINC' ) or die;

class GeneratePress_Site {

	/**
	 * Directory to our site.
	 *
	 * @var string
	 */
	protected $directory;

	/**
	 * Name of our site.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * URL to our preview.
	 *
	 * @var string
	 */
	protected $preview_url;

	/**
	 * Name of site author.
	 *
	 * @var string
	 */
	protected $author_name;

	/**
	 * URL of site author.
	 *
	 * @var string
	 */
	protected $author_url;

	/**
	 * Description of the site.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Icon filename.
	 *
	 * @var string
	 */
	protected $icon;

	/**
	 * Screenshot filename.
	 *
	 * @var string
	 */
	protected $screenshot;

	/**
	 * Page Builder.
	 *
	 * @var string
	 */
	protected $page_builder;

	/**
	 * Minimum version.
	 *
	 * @var int|string
	 */
	protected $minimum_version;

	/**
	 * Plugins.
	 *
	 * @var array
	 */
	protected $plugins;

	/**
	 * Documentation URL.
	 *
	 * @var string
	 */
	protected $documentation;

	/**
	* Get the uploads URL.
	*
	* @var int|string
	*/
	protected $uploads_url;

	/**
	 * Check if site is installable.
	 *
	 * @var bool
	 */
	protected $installable;

	/**
	 * Get it rockin'
	 *
	 * @param array $config
	 */
	public function __construct( $config = array() ) {

		$config = wp_parse_args( $config, array(
			'directory'		=> '',
			'name' 			=> '',
			'preview_url' 	=> '',
			'author_name'	=> '',
			'author_url'	=> '',
			'icon'			=> 'icon.png',
			'screenshot'	=> 'screenshot.png',
			'page_builder'	=> array(),
			'uploads_url'	=> array(),
			'min_version'	=> GP_PREMIUM_VERSION,
			'plugins'		=> '',
			'documentation'	=> '',
		) );

		$this->helpers = new GeneratePress_Sites_Helper();

		$this->directory	= trailingslashit( $config['directory'] );

		$provider = parse_url( $this->directory );

		if ( ! isset( $provider['host'] ) ) {
			return;
		}

		if ( ! in_array( $provider['host'], ( array ) get_transient( 'generatepress_sites_trusted_providers' ) ) ) {
			return;
		}

		$this->name 			= $config['name'];
		$this->slug				= str_replace( ' ', '_', strtolower( $this->name ) );
		$this->preview_url 		= $config['preview_url'];
		$this->author_name		= $config['author_name'];
		$this->author_url		= $config['author_url'];
		$this->description		= $config['description'];
		$this->icon				= $config['icon'];
		$this->screenshot		= $config['screenshot'];
		$this->page_builder 	= $config['page_builder'];
		$this->min_version		= $config['min_version'];
		$this->uploads_url		= $config['uploads_url'];
		$this->plugins			= $config['plugins'];
		$this->documentation 	= $config['documentation'];
		$this->installable		= true;

		if ( empty( $this->min_version ) ) {
			$this->min_version = GP_PREMIUM_VERSION;
		}

		if ( version_compare( GP_PREMIUM_VERSION, $config['min_version'], '<' ) ) {
			$this->installable = false;
		}

		add_action( 'generate_inside_sites_container',						array( $this, 'build_box' ) );
		add_action( "wp_ajax_generate_setup_demo_content_{$this->slug}",	array( $this, 'setup_demo_content' ), 10, 0 );
		add_action( "wp_ajax_generate_check_plugins_{$this->slug}",			array( $this, 'check_plugins' ), 10, 0 );
		add_action( "wp_ajax_generate_backup_options_{$this->slug}",		array( $this, 'backup_options' ), 10, 0 );
		add_action( "wp_ajax_generate_import_options_{$this->slug}",		array( $this, 'import_options' ), 10, 0 );
		add_action( "wp_ajax_generate_activate_plugins_{$this->slug}",		array( $this, 'activate_plugins' ), 10, 0 );
		add_action( "wp_ajax_generate_import_site_options_{$this->slug}",	array( $this, 'import_site_options' ), 10, 0 );
		add_action( "wp_ajax_generate_download_content_{$this->slug}",		array( $this, 'download_content' ), 10, 0 );
		add_action( "wp_ajax_generate_import_content_{$this->slug}",		array( $this, 'import_content' ), 10, 0 );
		add_action( "wp_ajax_generate_import_widgets_{$this->slug}",		array( $this, 'import_widgets' ), 10, 0 );

		// Don't do the WC setup. This wouldn't be necessary if they used an activation hook.
		add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );

	}

	/**
	 * Build the site details, including the screenshot and description.
	 *
	 * @since 1.6
	 */
	public function site_details() {

		printf( '<div class="site-screenshot site-overview-screenshot">
					<img src="" alt="%s" />
				</div>',
				esc_attr( $this->name )
		);

		?>

		<div class="site-description">
			<?php if ( $this->documentation ) : ?>
				<div class="site-documentation">
					<h3><?php _e( 'Documentation', 'gp-premium' ); ?></h3>
					<p>
						<?php _e( 'Learn how to customize this site.', 'gp-premium' ); ?>
						<a href="<?php echo esc_url( $this->documentation ); ?>" target="_blank" rel="noopener"><?php _e( 'View documentation', 'gp-premium' ); ?> &rarr;</a>
					</p>
				</div>
			<?php endif; ?>

			<div class="library-help">
				<h3><?php _e( 'Using the Site Library', 'gp-premium' ); ?></h3>

				<p>
					<?php _e( 'Learn more about using the site library.', 'gp-premium' ); ?>
					<a href="https://docs.generatepress.com/article/using-the-site-library/" target="_blank" rel="noopener"><?php _e( 'View instructions', 'gp-premium' ); ?> &rarr;</a>
				</p>
			</div>

			<?php if ( $this->author_name && 'GeneratePress' !== $this->author_name ) : ?>
				<div class="site-author">
					<h3><?php _e( 'Site Author', 'gp-premium' ); ?></h3>
					<p>
						<?php
							printf(
								__( '%s is brought to you by ', 'gp-premium' ),
								$this->name
							);
						?>
						<a href="<?php echo esc_url( $this->author_url ); ?>" target="_blank" rel="noopener"><?php echo $this->author_name; ?></a>.
					</p>
				</div>
			<?php endif; ?>
		</div>

		<?php

	}

	/**
	 * Build the site controls.
	 *
	 * @since 1.6
	 */
	public function site_controls() {
		?>
		<div class="controls">
			<button title="<?php esc_attr_e( 'Previous Site', 'gp-premium' ); ?>" class="prev"><span class="screen-reader-text"><?php esc_html_e( 'Previous', 'gp-premium' ); ?></span></button>
			<button title="<?php esc_attr_e( 'Next Site', 'gp-premium' ); ?>" class="next"><span class="screen-reader-text"><?php esc_html_e( 'Next', 'gp-premium' ); ?></span></button>
			<button title="<?php esc_attr_e( 'Close', 'gp-premium' ); ?>" class="close"><span class="screen-reader-text"><?php esc_html_e( 'Close', 'gp-premium' ); ?></span></button>
			<button title="<?php esc_attr_e( 'Preview', 'gp-premium' ); ?>" class="preview-site"><?php _e( 'Preview', 'gp-premium' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Build the loading icon.
	 *
	 * @since 1.6
	 */
	public function loading_icon() {
		// Deprecated since 1.9
	}

	/**
	 * Build our site boxes in our Dashboard.
	 *
	 * @since 1.6
	 */
	public function build_box() {

		$site_data = array(
			'slug'			=> $this->slug,
			'preview_url' 	=> $this->preview_url,
			'plugins'		=> $this->plugins,
		);

		$page_builders = array();
		foreach ( ( array ) $this->page_builder as $builder ) {
			$page_builders = str_replace( ' ', '-', strtolower( $builder ) );
		}

		$site_classes = array(
			'site-box',
			$page_builders,
			! $this->installable ? 'disabled-site' : ''
		);

		?>
		<div class="<?php echo implode( ' ', $site_classes ); ?>" data-site-data="<?php echo htmlspecialchars( json_encode( $site_data ), ENT_QUOTES, 'UTF-8' ); ?>">
			<div class="steps step-one">
				<div class="site-info">
					<div class="site-description">
						<h3><a class="site-details" href="#"><?php echo $this->name; ?></a></h3>
						<?php
						if ( $this->description ) {
							echo '<a class="site-details" href="#"> ' . wpautop( $this->description ) . '</a>';
						}
						?>

						<?php if ( $this->installable ) : ?>
							<div class="site-card-buttons">
								<button class="button preview-site"><?php _e( 'Preview', 'gp-premium' ); ?></button>
								<button class="button-primary site-details"><?php _e( 'Details', 'gp-premium' ); ?></button>
							</div>
						<?php else : ?>
							<span class="version-required-message">
								<?php printf( _x( 'Requires GP Premium %s', 'required version number', 'gp-premium' ), $this->min_version ); ?>
							</span>
						<?php endif; ?>
					</div>
				</div>

				<div class="site-screenshot site-card-screenshot">
					<img class="lazyload" src="<?php echo GENERATE_SITES_URL; ?>/assets/images/screenshot.png" data-src="<?php echo esc_url( $this->directory . $this->screenshot ); ?>" alt="" />
				</div>

				<div class="site-title">
					<span class="author-name"><?php echo $this->author_name; ?></span>
					<h3><?php echo $this->name; ?></h3>
				</div>
			</div>

			<div class="steps step-overview" style="display: none;">
				<div class="step-information">
					<h1 style="margin-bottom: 0;">
						<?php printf(
							__( 'Welcome to %s.', 'gp-premium' ),
							$this->name
						); ?>
					</h1>

					<p><?php echo $this->description; ?></p>

					<div class="action-area">
						<div class="action-buttons">
							<?php echo $this->action_button(); ?>

							<div class="loading" style="display: none;">
								<span class="site-message"></span>
								<?php GeneratePress_Sites_Helper::loading_icon(); ?>
							</div>

							<span class="error-message" style="display: none;"><a href="#">[?]</a></span>
						</div>

						<div class="important-note confirm-content-import-message" style="display: none;">
							<label>
								<input id="confirm-content-import" name="confirm-content-import" class="confirm-content-import" type="checkbox" />
								<?php _e( 'I understand that this step will add content, site options, menus, widgets and plugins to my site. It can not be automatically undone.', 'gp-premium' ); ?>
							</label>
						</div>

						<?php if ( GeneratePress_Sites_Helper::do_options_exist() ) : ?>
							<div class="important-note confirm-backup-options">
								<label>
									<input id="confirm-options-import" name="confirm-options-import" class="confirm-options-import" type="checkbox" />
									<?php _e( 'I understand that this step will overwrite my Customizer settings. It is recommended that you only use the Site Library on a fresh site.', 'gp-premium' ); ?>
								</label>
							</div>
						<?php endif; ?>
					</div>

					<div class="site-step-details">
						<div class="theme-options">
							<span class="number"></span>
							<span class="big-loader"><?php GeneratePress_Sites_Helper::loading_icon(); ?></span>

							<h3><?php _e( 'Theme Options', 'gp-premium' ); ?></h3>
							<p><?php _e( 'Options set in the Customizer of the theme.', 'gp-premium' ); ?></p>
						</div>

						<div class="demo-content">
							<span class="number"></span>
							<span class="big-loader"><?php GeneratePress_Sites_Helper::loading_icon(); ?></span>

							<h3 id="demo-content"><?php _e( 'Demo Content', 'gp-premium' ); ?><span class="skip-content-import" style="display: none;"><a href="#"><?php _e( 'Skip this step', 'gp-premium' ); ?> &rarr;</a></span></h3>
							<p>
								<?php _e( 'Things like pages, menus, widgets and plugins.', 'gp-premium' ); ?>
							</p>

							<?php if ( $this->plugins ) :
								$plugins = json_decode( $this->plugins, true );

								if ( ! empty( $plugins ) ) :
									?>
									<div class="site-plugins">
										<p><?php _e( 'This site uses the following plugins.', 'gp-premium' ); ?></p>
										<ul>
											<?php foreach( $plugins as $name => $id ) {
												printf(
													'<li>%s</li>',
													$name
												);
											} ?>
										</ul>
									</div>
									<?php
								endif;
							endif; ?>

							<div class="plugin-area">
								<div class="no-plugins" style="display: none;">
									<p><?php _e( 'No plugins required.', 'gp-premium' ); ?></p>
								</div>

								<div class="automatic-plugins" style="display:none">
									<p><?php _e( 'The following plugins can be installed and activated automatically.', 'gp-premium' ); ?></p>
									<ul></ul>
								</div>

								<div class="installed-plugins" style="display:none">
									<p><?php _e( 'The following plugins are already installed.', 'gp-premium' ); ?></p>
									<ul></ul>
								</div>

								<div class="manual-plugins" style="display:none;">
									<p><?php _e( 'The following plugins need to be installed and activated manually.', 'gp-premium' ); ?></p>
									<ul></ul>
								</div>
							</div>
						</div>

						<div class="import-complete">
							<span class="number"></span>
							<span class="big-loader"><?php GeneratePress_Sites_Helper::loading_icon(); ?></span>

							<h3 id="import-complete"><?php _e( 'All Done', 'gp-premium' ); ?></h3>
							<p><?php _e( 'Your site is ready to go!', 'gp-premium' ); ?></p>
							<?php
							$plugins_array = json_decode( $this->plugins, true );

							if ( $this->uploads_url && is_array( $plugins_array ) && in_array( 'elementor/elementor.php', $plugins_array ) ) :
								if ( function_exists( 'wp_get_upload_dir' ) ) {
									$uploads_url = wp_get_upload_dir();
								} else {
									$uploads_url = wp_upload_dir( null, false );
								}

								$uploads_url = $uploads_url['baseurl'];

								if ( $this->uploads_url ) : ?>
									<div class="replace-elementor-urls" style="display: none;">
										<h4><?php _e( 'Additional Cleanup', 'gp-premium' ); ?></h4>
										<p><?php _e( 'This site is using Elementor which means you will want to replace the imported image URLs.', 'gp-premium' ); ?> <a title="<?php _e( 'Learn more', 'gp-premium' ); ?>" href="https://docs.generatepress.com/article/replacing-urls-in-elementor/" target="_blank" rel="noopener">[?]</a></p>

										<p>
											<?php printf(
												__( 'Go to %s, enter the below URLs and click the "Replace URL" button.', 'gp-premium' ),
												'<a href="' . admin_url( 'admin.php?page=elementor-tools#tab-replace_url' ) . '" target="_blank" rel="noopener">Elementor > Tools > Replace URLs</a>'
											) ?>
										</p>

										<div class="elementor-urls">
											<label for="old-url"><?php _e( 'Old URL', 'gp-premium' ); ?></label>
											<input id="old-url" type="text" value="<?php echo $this->uploads_url; ?>" />

											<label for="new-url"><?php _e( 'New URL', 'gp-premium' ); ?></label>
											<input id="new-url" type="text" value="<?php echo $uploads_url; ?>" />
										</div>
									</div>
								<?php
								endif;
							endif;
							?>
						</div>
					</div>
				</div>

				<div class="site-overview-details">
					<?php $this->site_controls(); ?>
					<?php $this->site_details(); ?>
				</div>
			</div>

			<div class="site-demo" style="display: none;">
				<div class="demo-loading loading">
					<?php GeneratePress_Sites_Helper::loading_icon(); ?>
				</div>

				<iframe></iframe>
				<div class="demo-panel">
					<button title="<?php esc_attr_e( 'Close', 'gp-premium' ); ?>" class="close-demo"><span class="screen-reader-text"><?php _e( 'Close', 'gp-premium' ); ?></span></button>
					<button title="<?php esc_attr_e( 'Previous', 'gp-premium' ); ?>" class="prev"><span class="screen-reader-text"><?php _e( 'Previous', 'gp-premium' ); ?></span></button>
					<button title="<?php esc_attr_e( 'Next', 'gp-premium' ); ?>" class="next"><span class="screen-reader-text"><?php _e( 'Next', 'gp-premium' ); ?></span></button>
					<button title="<?php esc_attr_e( 'Desktop', 'gp-premium' ); ?>" class="show-desktop"><span class="screen-reader-text"><?php _e( 'Desktop', 'gp-premium' ); ?></span></button>
					<button title="<?php esc_attr_e( 'Tablet', 'gp-premium' ); ?>" class="show-tablet"><span class="screen-reader-text"><?php _e( 'Tablet', 'gp-premium' ); ?></span></button>
					<button title="<?php esc_attr_e( 'Mobile', 'gp-premium' ); ?>" class="show-mobile"><span class="screen-reader-text"><?php _e( 'Mobile', 'gp-premium' ); ?></span></button>
					<button class="button button-primary get-started"><?php _e( 'Details', 'gp-premium' ); ?></button>
				</div>
			</div>
		</div>
	<?php

	}

	public function action_button() {

		$options = GeneratePress_Sites_Helper::do_options_exist();

		submit_button(
			__( 'Backup Options', 'gp-premium' ),
			'button-primary backup-options site-action',
			'submit',
			false,
			array(
				'id' => '',
				'disabled' => 'disabled',
				'style' => ! $options ? 'display: none;' : '',
			)
		);

		submit_button(
			__( 'Import Options', 'gp-premium' ),
			'button-primary import-options site-action',
			'submit',
			false,
			array(
				'id' => '',
				'style' => $options ? 'display:none' : '',
			)
		);

		submit_button(
			__( 'Import Content', 'gp-premium' ),
			'button-primary import-content site-action',
			'submit',
			false,
			array(
				'id' => '',
				'disabled' => 'disabled',
				'style' => 'display: none;',
			)
		);

		submit_button(
			__( 'View Your Site', 'gp-premium' ),
			'button-primary view-site',
			'submit',
			false,
			array(
				'id' => '',
				'style' => 'display: none;',
			)
		);
	}

	/**
	 * Backup our existing GeneratePress options.
	 *
	 * @since 1.6
	 */
	public function backup_options() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$theme_mods = GeneratePress_Sites_Helper::get_theme_mods();
		$settings = GeneratePress_Sites_Helper::get_theme_settings();

		$data = array(
			'mods' => array(),
			'options' => array()
		);

		foreach ( $theme_mods as $theme_mod ) {
			$data['mods'][$theme_mod] = get_theme_mod( $theme_mod );
		}

		foreach ( $settings as $setting ) {
			$data['options'][$setting] = get_option( $setting );
		}

		echo json_encode( $data );

		die();

	}

	/**
	 * Tells our JS which files exist.
	 *
	 * @since 1.8
	 */
	public function setup_demo_content() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = GeneratePress_Sites_Helper::get_options( $this->directory . 'options.json' );

		$data['plugins'] = $settings['plugins'];

		if ( GeneratePress_Sites_Helper::file_exists( $this->directory . 'content.xml' ) ) {
			$data['content'] = true;
		} else {
			$data['content'] = false;
		}

		if ( GeneratePress_Sites_Helper::file_exists( $this->directory . 'widgets.wie' ) ) {
			$data['widgets'] = true;
		} else {
			$data['widgets'] = false;
		}

		// Backup our plugins early.
		$backup_data = get_option( '_generatepress_site_library_backup', array() );
		$backup_data['plugins'] = get_option( 'active_plugins', array() );
		update_option( '_generatepress_site_library_backup', $backup_data );

		wp_send_json( $data );

		die();

	}

	/**
	 * Import our demo GeneratePress options.
	 *
	 * @since 1.6
	 */
	public function import_options() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! GeneratePress_Sites_Helper::file_exists( $this->directory . 'options.json' ) ) {
			wp_send_json_error( __( 'No theme options exist.', 'gp-premium' ) );
		}

		// Delete existing backup.
		delete_option( '_generatepress_site_library_backup' );

		// Backup options.
		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		$theme_mods = GeneratePress_Sites_Helper::get_theme_mods();
		$settings = GeneratePress_Sites_Helper::get_theme_settings();

		$data = array(
			'mods' => array(),
			'options' => array()
		);

		foreach ( $theme_mods as $theme_mod ) {
			$data['mods'][$theme_mod] = get_theme_mod( $theme_mod );
		}

		foreach ( $settings as $setting ) {
			$data['options'][$setting] = get_option( $setting );
		}

		$backup_data['theme_options'] = $data;

		$modules = generatepress_get_site_premium_modules();

		$active_modules = array();
		foreach ( $modules as $name => $key ) {
			if ( 'activated' == get_option( $key ) ) {
				$active_modules[ $name ] = $key;
			}
		}

		$backup_data['modules'] = $active_modules;

		$settings = GeneratePress_Sites_Helper::get_options( $this->directory . 'options.json' );

		// Remove all existing theme options.
		$option_keys = array(
			'generate_settings',
			'generate_background_settings',
			'generate_blog_settings',
			'generate_hooks',
			'generate_page_header_settings',
			'generate_secondary_nav_settings',
			'generate_spacing_settings',
			'generate_menu_plus_settings',
			'generate_woocommerce_settings',
		);

		foreach ( $option_keys as $key ) {
			delete_option( $key );
		}

		// Need to backup these items before we remove all theme mods.
		$backup_data['site_options']['nav_menu_locations'] = get_theme_mod( 'nav_menu_locations' );
		$backup_data['site_options']['custom_logo'] = get_theme_mod( 'custom_logo' );

		// Remove existing theme mods.
		remove_theme_mods();

		// Remove existing activated premium modules.
		$premium_modules = generatepress_get_site_premium_modules();

		foreach ( $premium_modules as $name => $key ) {
			delete_option( $key );
		}

		// Activate necessary modules.
		foreach ( $settings['modules'] as $name => $key ) {
			// Only allow valid premium modules.
			if ( ! in_array( $key, $premium_modules ) ) {
				GeneratePress_Sites_Helper::log( 'Bad premium module key: ' . $key );
				continue;
			}

			update_option( $key, 'activated' );
		}

		// Set theme mods.
		foreach ( $settings['mods'] as $key => $val ) {
			// Only allow valid theme mods.
			if ( ! in_array( $key, GeneratePress_Sites_Helper::get_theme_mods() ) ) {
				GeneratePress_Sites_Helper::log( 'Bad theme mod key: ' . $key );
				continue;
			}

			set_theme_mod( $key, $val );
		}

		// Set theme options.
		foreach ( $settings['options'] as $key => $val ) {
			// Only allow valid options.
			if ( ! in_array( $key, GeneratePress_Sites_Helper::get_theme_settings() ) ) {
				GeneratePress_Sites_Helper::log( 'Bad theme setting key: ' . $key );
				continue;
			}

			// Import any images
			if ( is_array( $val ) || is_object( $val ) ) {
				foreach ( $val as $option_name => $option_value ) {
					if ( is_string( $option_value ) && preg_match( '/\.(jpg|jpeg|png|gif)/i', $option_value ) ) {

						$data = GeneratePress_Sites_Helper::sideload_image( $option_value );

						if ( ! is_wp_error( $data ) ) {
							$val[$option_name] = $data->url;
						}

					}
				}
			}

			update_option( $key, $val );
		}

		// Remove dynamic CSS cache.
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );

		// Custom CSS.
		$css = $settings['custom_css'];
		$css = '/* GeneratePress Site CSS */ ' . $css . ' /* End GeneratePress Site CSS */';

		$current_css = wp_get_custom_css_post();

		if ( isset( $current_css->post_content ) ) {
			preg_match( '#(/\* GeneratePress Site CSS).*?(End GeneratePress Site CSS \*/)#s', $current_css->post_content, $matches );

			if ( ! empty( $matches ) ) {
				$backup_data['css'] = $matches[0];
			}

			$current_css->post_content = preg_replace( '#(/\\* GeneratePress Site CSS \\*/).*?(/\\* End GeneratePress Site CSS \\*/)#s', '', $current_css->post_content );
			$css = $current_css->post_content . $css;
		}

		wp_update_custom_css_post( $css );

		update_option( '_generatepress_site_library_backup', $backup_data );

		die();

	}

	public function download_content() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Increase PHP max execution time.
		set_time_limit( apply_filters( 'generate_sites_content_import_time_limit', 300 ) );

		$xml_path = $this->directory . 'content.xml';
		$xml_file = GeneratePress_Sites_Helper::download_file( $xml_path );
		$xml_path = $xml_file['data']['file'];

		if ( file_exists( $xml_path ) ) {
			set_transient( 'generatepress_sites_content_file', $xml_path, HOUR_IN_SECONDS );
		}

		die();
	}

	/**
	 * Import our demo content.
	 *
	 * @since 1.6
	 */
	public function import_content() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Increase PHP max execution time.
		set_time_limit( apply_filters( 'generate_sites_content_import_time_limit', 300 ) );

		// Disable import of authors.
		add_filter( 'wxr_importer.pre_process.user', '__return_false' );

		// Keep track of our progress.
		add_action( 'wxr_importer.processed.post', array( $this, 'track_post' ) );
		add_action( 'wxr_importer.processed.term', array( $this, 'track_term' ) );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		if ( ! apply_filters( 'generate_sites_regen_thumbnails', true ) ) {
			add_filter( 'intermediate_image_sizes_advanced', '__return_null' );
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );
		$backup_data['content'] = true;
		update_option( '_generatepress_site_library_backup', $backup_data );

		// Import content
		$content = get_transient( 'generatepress_sites_content_file' );

		if ( $content ) {
			GeneratePress_Sites_Helper::import_xml( $content, $this->slug );
			delete_transient( 'generatepress_sites_content_file' );
		}

		die();

	}

	/**
	 * Import our widgets.
	 *
	 * @since 1.6
	 */
	public function import_widgets() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$widgets_path = $this->directory . 'widgets.wie';

		$wie_file = GeneratePress_Sites_Helper::download_file( $widgets_path );
		$wie_path = $wie_file['data']['file'];

		$data = implode( '', file( $wie_path ) );
		$data = json_decode( $data );

		GeneratePress_Sites_Helper::clear_widgets();

		$widgets_importer = GeneratePress_Sites_Widget_Importer::instance();
		$widgets_importer->wie_import_data( $data );

		die();

	}

	/**
	 * Import any necessary site options.
	 *
	 * @since 1.6
	 */
	public function import_site_options() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$backup_data = get_option( '_generatepress_site_library_backup', array() );

		$settings = GeneratePress_Sites_Helper::get_options( $this->directory . 'options.json' );

		delete_option( 'generate_page_header_global_locations' );

		foreach( $settings['site_options'] as $key => $val ) {

			switch( $key ) {

				case 'page_for_posts':
				case 'page_on_front':
					$backup_data['site_options'][ $key ] = get_option( $key );
					GeneratePress_Sites_Helper::set_reading_pages( $key, $val, $this->slug );
				break;

				case 'woocommerce_shop_page_id':
				case 'woocommerce_cart_page_id':
				case 'woocommerce_checkout_page_id':
				case 'woocommerce_myaccount_page_id':
					$backup_data['site_options'][ $key ] = get_option( $key );
					GeneratePress_Sites_Helper::set_woocommerce_pages( $key, $val, $this->slug );
				break;

				case 'nav_menu_locations':
					GeneratePress_Sites_Helper::set_nav_menu_locations( $val );
				break;

				case 'page_header_global_locations':
					GeneratePress_Sites_Helper::set_global_page_header_locations( $val, $this->slug );
				break;

				case 'page_headers':
					GeneratePress_Sites_Helper::set_page_headers( $val, $this->slug );
				break;

				case 'element_locations':
					GeneratePress_Sites_Helper::set_element_locations( $val, $this->slug );
				break;

				case 'element_exclusions':
					GeneratePress_Sites_Helper::set_element_exclusions( $val, $this->slug );
				break;

				case 'custom_logo':
					$data = GeneratePress_Sites_Helper::sideload_image( $val );

					if ( ! is_wp_error( $data ) && isset( $data->attachment_id ) ) {
						set_theme_mod( 'custom_logo', $data->attachment_id );
						update_post_meta( $data->attachment_id, '_wp_attachment_is_custom_header', get_option( 'stylesheet' ) );
					} else {
						remove_theme_mod( 'custom_logo' );
					}

				break;

				default:
					if ( in_array( $key, ( array ) generatepress_sites_disallowed_options() ) ) {
						GeneratePress_Sites_Helper::log( 'Disallowed option: ' . $key );
					} else {
						$backup_data['site_options'][ $key ] = get_option( $key );
						delete_option( $key );
						update_option( $key, $val );
					}
				break;

			}

		}

		// Set our backed up options.
		update_option( '_generatepress_site_library_backup', $backup_data );

		// Update any custom menu link URLs.
		GeneratePress_Sites_Helper::update_menu_urls( $this->preview_url );

		// Clear page builder cache.
		GeneratePress_Sites_Helper::clear_page_builder_cache();

		wp_send_json( __( 'Site options imported', 'gp-premium' ) );

		die();

	}

	/**
	 * Activates our freshly installed plugins.
	 *
	 * @since 1.6
	 */
	public function activate_plugins() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = GeneratePress_Sites_Helper::get_options( $this->directory . 'options.json' );
		$plugins = $settings['plugins'];

		if ( ! empty( $plugins ) ) {

			$pro_plugins = GeneratePress_Sites_Helper::check_for_pro_plugins();

			foreach( $plugins as $plugin ) {
				// If the plugin has a pro version and it exists, activate it instead.
				if ( array_key_exists( $plugin, $pro_plugins ) ) {
					if ( file_exists( WP_PLUGIN_DIR . '/' . $pro_plugins[$plugin] ) ) {
						$plugin = $pro_plugins[$plugin];
					}
				}

				// Install BB lite if pro doesn't exist.
				if ( 'bb-plugin/fl-builder.php' === $plugin && ! file_exists( WP_PLUGIN_DIR . '/bb-plugin/fl-builder.php' ) ) {
					$plugin = 'beaver-builder-lite-version/fl-builder.php';
				}

				if ( ! is_plugin_active( $plugin ) ) {
					activate_plugin( $plugin, '', false, true );
				}
			}

			wp_send_json( __( 'Plugins activated', 'gp-premium' ) );

		}

		die();

	}

	/**
	 * Checks a few things:
	 * 1. Is the plugin installed already?
	 * 2. Is the plugin active already?
	 * 3. Can the plugin be downloaded from WordPress.org?
	 *
	 * @since 1.6
	 */
	public function check_plugins() {

		check_ajax_referer( 'generate_sites_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( GeneratePress_Sites_Helper::file_exists( $this->directory . 'options.json' ) ) {
			$data['options'] = true;

			$settings = GeneratePress_Sites_Helper::get_options( $this->directory . 'options.json' );
			$data['modules'] = $settings['modules'];
			$data['plugins'] = $settings['plugins'];

			if ( ! is_array( $data['plugins'] ) ) {
				return;
			}

			$plugin_data = array();
			foreach( $data['plugins'] as $name => $slug ) {
				$basename = strtok( $slug, '/' );
				$plugin_data[$name] = array(
					'name' => $name,
					'slug' => $slug,
					'installed' => GeneratePress_Sites_Helper::is_plugin_installed( $slug ) ? true : false,
					'active' => is_plugin_active( $slug ) ? true : false,
					'repo' => GeneratePress_Sites_Helper::file_exists( 'https://api.wordpress.org/plugins/info/1.0/' . $basename ) ? true : false,
				);
			}

			$data['plugin_data'] = $plugin_data;
		}

		wp_send_json( array(
			'plugins'		=> $data['plugins'],
			'plugin_data'	=> $data['plugin_data'],
		) );

		die();

	}

	/**
	 * Track Imported Post
	 *
	 * @param  int $post_id Post ID.
	 * @return void
	 */
	function track_post( $post_id ) {
		update_post_meta( $post_id, '_generatepress_sites_imported_post', true );
	}

	/**
	 * Track Imported Term
	 *
	 * @param  int $term_id Term ID.
	 * @return void
	 */
	function track_term( $term_id ) {
		$term = get_term( $term_id );

		update_term_meta( $term_id, '_generatepress_sites_imported_term', true );
	}
}
