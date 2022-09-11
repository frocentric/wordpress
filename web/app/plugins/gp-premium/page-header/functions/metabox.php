<?php
defined( 'WPINC' ) or die;

if ( ! function_exists( 'add_generate_page_header_meta_box' ) ) {
	add_action( 'add_meta_boxes', 'add_generate_page_header_meta_box', 50 );
	/**
	 * Generate the page header metabox.
	 *
	 * @since 0.1
	 */
	function add_generate_page_header_meta_box() {
		// Set user role - make filterable
		$allowed = apply_filters( 'generate_page_header_metabox_capability', 'edit_posts' );

		// If not an administrator, don't show the metabox
		if ( ! current_user_can( $allowed ) ) {
			return;
		}

		$stored_meta = (array) get_post_meta( get_the_ID() );

		// Set defaults to avoid PHP notices
		$stored_meta['_meta-generate-page-header-image'][0] = ( isset( $stored_meta['_meta-generate-page-header-image'][0] ) ) ? $stored_meta['_meta-generate-page-header-image'][0] : '';
		$stored_meta['_meta-generate-page-header-image-id'][0] = ( isset( $stored_meta['_meta-generate-page-header-image-id'][0] ) ) ? $stored_meta['_meta-generate-page-header-image-id'][0] : '';
		$stored_meta['_meta-generate-page-header-content'][0] = ( isset( $stored_meta['_meta-generate-page-header-content'][0] ) ) ? $stored_meta['_meta-generate-page-header-content'][0] : '';

		$args = array( 'public' => true );
		$post_types = get_post_types( $args );

		// Bail if we're not using the old Page Header meta box
		if ( 'generate_page_header' !== get_post_type() && '' == $stored_meta['_meta-generate-page-header-content'][0] && '' == $stored_meta['_meta-generate-page-header-image'][0] && '' == $stored_meta['_meta-generate-page-header-image-id'][0] ) {
			if ( ! defined( 'GENERATE_LAYOUT_META_BOX' ) ) {
				foreach ( $post_types as $type ) {
					if ( 'attachment' !== $type ) {
						add_meta_box(
							'generate_select_page_header_meta_box',
							__( 'Page Header', 'gp-premium' ),
							'generate_do_select_page_header_meta_box',
							$type,
							'normal',
							'high'
						);
					}
				}
			}

			if ( ! apply_filters( 'generate_page_header_legacy_metabox', false ) ) {
				return;
			}
		}

		array_push( $post_types, 'generate_page_header' );
		foreach ($post_types as $type) {
			if ( 'attachment' !== $type ) {
				add_meta_box(
					'generate_page_header_meta_box',
					__( 'Page Header', 'gp-premium' ),
					'show_generate_page_header_meta_box',
					$type,
					'normal',
					'high'
				);
			}
		}
	}
}

if ( ! function_exists( 'generate_page_header_metabox_enqueue' ) ) {
	add_action( 'admin_enqueue_scripts', 'generate_page_header_metabox_enqueue' );
	/**
	 * Add our metabox scripts
	 */
	function generate_page_header_metabox_enqueue( $hook ) {
		// I prefer to enqueue the styles only on pages that are using the metaboxes
		if ( in_array( $hook, array( "post.php", "post-new.php" ) ) ) {
			$args = array( 'public' => true );
			$post_types = get_post_types( $args );

			$screen = get_current_screen();
			$post_type = $screen->id;

			if ( in_array( $post_type, (array) $post_types ) || 'generate_page_header' == get_post_type() ){
				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-alpha', GP_LIBRARY_DIRECTORY_URL . 'alpha-color-picker/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '3.0.0', true );

				wp_add_inline_script(
					'wp-color-picker-alpha',
					'jQuery( function() { jQuery( ".color-picker" ).wpColorPicker(); } );'
				);

				wp_enqueue_style( 'generate-page-header-metabox', plugin_dir_url( __FILE__ ) . 'css/metabox.css', array(), GENERATE_PAGE_HEADER_VERSION );
				wp_enqueue_script( 'generate-lc-switch', plugin_dir_url( __FILE__ ) . 'js/lc_switch.js', array( 'jquery' ), GENERATE_PAGE_HEADER_VERSION, false );
				wp_enqueue_script( 'generate-page-header-metabox', plugin_dir_url( __FILE__ ) . 'js/metabox.js', array( 'jquery','generate-lc-switch', 'wp-color-picker' ), GENERATE_PAGE_HEADER_VERSION, false );

				if ( function_exists( 'wp_add_inline_script' ) && function_exists( 'generate_get_default_color_palettes' ) ) {
					// Grab our palette array and turn it into JS
					$palettes = json_encode( generate_get_default_color_palettes() );

					// Add our custom palettes
					// json_encode takes care of escaping
					wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
				}
			}
		}
	}
}

/**
 * Build our Select Page Header meta box.
 *
 * @since 1.4
 */
function generate_do_select_page_header_meta_box( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'generate_page_header_nonce' );
    $stored_meta = get_post_meta( $post->ID );
	$stored_meta['_generate-select-page-header'][0] = ( isset( $stored_meta['_generate-select-page-header'][0] ) ) ? $stored_meta['_generate-select-page-header'][0] : '';

	$page_headers = get_posts(array(
		'posts_per_page' => -1,
		'orderby' => 'title',
		'post_type' => 'generate_page_header',
	));

	if ( count( $page_headers ) > 0 ) :
	?>
	<p>
		<select name="_generate-select-page-header" id="_generate-select-page-header">
			<option value="" <?php selected( $stored_meta['_generate-select-page-header'][0], '' ); ?>></option>
			<?php
			foreach( $page_headers as $header ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>',
					$header->ID,
					selected( $stored_meta['_generate-select-page-header'][0], $header->ID ),
					$header->post_title
				);
			}
			?>
		</select>
	</p>
    <?php else : ?>
		<p>
			<?php
			printf( __( 'No Page Headers found. Want to <a href="%1$s" target="_blank">create one</a>?', 'gp-premium' ),
				esc_url( admin_url( 'post-new.php?post_type=generate_page_header' ) )
			);
			?>
		</p>
	<?php endif;
}

if ( ! function_exists( 'show_generate_page_header_meta_box' ) ) {
	/**
	 * Outputs the content of the metabox
	 * This could use some cleaning up
	 */
	function show_generate_page_header_meta_box( $post ) {
	    wp_nonce_field( basename( __FILE__ ), 'generate_page_header_nonce' );
		$show_excerpt_option = ( has_post_thumbnail() ) ? 'style="display:none;"' : 'style="display:block;"';

		$content_required = sprintf(
			'<div class="page-header-content-required" %2$s><p>%1$s</p></div>',
			__( 'Content is required for the below settings to work.', 'gp-premium' ),
			'' !== generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-content', true ) ? 'style="display:none"' : ''
		);

		if ( '' !== generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-content', true ) ) {
			?>
			<script>
				jQuery( function( $ ) {
					$('#generate-image-tab').hide();
					$('#generate-content-tab').show();
					$('.generate-tabs-menu .content-settings').addClass('generate-current');
					$('.generate-tabs-menu .image-settings').removeClass('generate-current');
				} );
			</script>
			<?php
		}
		?>
		<div id="generate-tabs-container">
			<ul class="generate-tabs-menu">
				<li class="generate-current image-settings">
					<a href="#generate-image-tab"><?php _e( 'Image', 'gp-premium' ); ?></a>
				</li>

				<li class="content-settings">
					<a href="#generate-content-tab"><?php _e( 'Content', 'gp-premium' ); ?></a>
				</li>

				<li class="video-settings">
					<a href="#generate-video-background-tab"><?php _e( 'Background Video', 'gp-premium' ); ?></a>
				</li>

				<?php if ( generate_page_header_logo_exists() || generate_page_header_navigation_logo_exists() ) : ?>
					<li class="logo-settings">
						<a href="#generate-logo-tab"><?php _e( 'Logo', 'gp-premium' ); ?></a>
					</li>
				<?php endif; ?>

				<li class="advanced-settings">
					<a href="#generate-advanced-tab"><?php _e( 'Advanced', 'gp-premium' ); ?></a>
				</li>

				<?php if ( 'post' == get_post_type() && !is_single() ) : ?>
					<div class="show-in-excerpt" <?php echo $show_excerpt_option; ?>>
						<p>
							<label for="_meta-generate-page-header-add-to-excerpt"><strong><?php _e( 'Add to excerpt', 'gp-premium' );?></strong></label><br />
							<input class="add-to-excerpt" type="checkbox" name="_meta-generate-page-header-add-to-excerpt" id="_meta-generate-page-header-add-to-excerpt" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-add-to-excerpt', true ), 'yes' ); ?> />
						</p>
					</div>
				<?php endif; ?>
			</ul>
			<div class="generate-tab">
				<div id="generate-image-tab" class="generate-tab-content" style="display:block;">
					<?php
					$show_featured_image_message = ( has_post_thumbnail() && '' == generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-id', true ) ) ? 'style="display:block;"' : 'style="display:none;"';
					$remove_button = ( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image', true ) != "") ? 'style="display:inline-block;"' : 'style="display:none;"';
					$show_image_settings = ( has_post_thumbnail() || '' !== generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-id', true ) ) ? 'style="display:block;"' : 'style="display: none;"';
					$no_image_selected = ( ! has_post_thumbnail() && '' == generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-id', true ) ) ? 'style="display:block;"' : 'style="display:none;"';
					?>
					<div class="featured-image-message" <?php echo $show_featured_image_message; ?>>
						<p class="description">
							<?php _e( 'Currently using your <a href="#" class="generate-featured-image">featured image</a>.', 'gp-premium' ); ?>
						</p>
					</div>

					<div id="preview-image" class="generate-page-header-image">
						<?php if( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image', true ) != "") { ?>
							<img class="saved-image" src="<?php echo esc_url( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image', true ) );?>" width="100" style="margin-bottom:12px;" />
						<?php } ?>
					</div>

					<input data-prev="true" id="upload_image" type="hidden" name="_meta-generate-page-header-image" value="<?php echo esc_url(generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image', true )); ?>" />
					<button class="generate-upload-file button" type="button" data-type="image" data-title="<?php _e( 'Page Header Image', 'gp-premium' );?>" data-insert="<?php _e( 'Insert Image', 'gp-premium' ); ?>" data-prev="true">
						<?php _e( 'Choose Image', 'gp-premium' ) ;?>
					</button>
					<button class="generate-page-header-remove-image button" type="button" <?php echo $remove_button; ?> data-input="#upload_image" data-input-id="#_meta-generate-page-header-image-id" data-prev=".generate-page-header-image">
						<?php _e( 'Remove Image', 'gp-premium' ) ;?>
					</button>
					<input class="image-id" id="_meta-generate-page-header-image-id" type="hidden" name="_meta-generate-page-header-image-id" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-id', true ) ); ?>" />

					<div class="generate-page-header-set-featured-image" <?php echo $no_image_selected; ?>>
						<p class="description"><?php _e( 'Or you can <a href="#">set the featured image</a>.', 'gp-premium' ); ?></p>
					</div>

					<div class="page-header-image-settings" <?php echo $show_image_settings; ?>>
						<p>
							<label for="_meta-generate-page-header-image-link" class="example-row-title"><strong><?php _e( 'Image Link', 'gp-premium' );?></strong></label><br />
							<input class="widefat" style="max-width:350px;" placeholder="http://" id="_meta-generate-page-header-image-link" type="text" name="_meta-generate-page-header-image-link" value="<?php echo esc_url(generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-link', true )); ?>" />
						</p>

						<p>
							<label for="_meta-generate-page-header-enable-image-crop" class="example-row-title"><strong><?php _e( 'Resize Image', 'gp-premium' );?></strong></label><br />
							<select name="_meta-generate-page-header-enable-image-crop" id="_meta-generate-page-header-enable-image-crop">
								<option value="" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-enable-image-crop', true ), '' ); ?>><?php _e( 'Disable', 'gp-premium' );?></option>
								<option value="enable" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-enable-image-crop', true ), 'enable' ); ?>><?php _e( 'Enable', 'gp-premium' );?></option>
							</select>
						</p>

						<div id="crop-enabled" style="display:none">
							<p><?php _e( 'These options are no longer available as of GP Premium 1.10.0.', 'gp-premium' ); ?>
							<div style="display: none;">
								<p>
									<label for="_meta-generate-page-header-image-width" class="example-row-title"><strong><?php _e( 'Image Width', 'gp-premium' );?></strong></label><br />
									<input style="width:45px" type="text" name="_meta-generate-page-header-image-width" id="_meta-generate-page-header-image-width" value="<?php echo intval( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-width', true ) ); ?>" /><label for="_meta-generate-page-header-image-width"><span class="pixels">px</span></label>
								</p>

								<p style="margin-bottom:0;">
									<label for="_meta-generate-page-header-image-height" class="example-row-title"><strong><?php _e( 'Image Height', 'gp-premium' );?></strong></label><br />
									<input placeholder="" style="width:45px" type="text" name="_meta-generate-page-header-image-height" id="_meta-generate-page-header-image-height" value="<?php echo intval( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-height', true ) ); ?>" />
									<label for="_meta-generate-page-header-image-height"><span class="pixels">px</span></label>
									<span class="description" style="display:block;"><?php _e( 'Use "0" or leave blank for proportional resizing.', 'gp-premium' );?></span>
								</p>
							</div>
						</div>
					</div>
				</div>

				<div id="generate-content-tab" class="generate-tab-content">

					<textarea style="width:100%;min-height:200px;" name="_meta-generate-page-header-content" id="_meta-generate-page-header-content"><?php echo esc_textarea( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-content', true ) ); ?></textarea>
					<p class="description" style="margin:0;"><?php _e( 'HTML and shortcodes allowed.', 'gp-premium' );?></p>

					<div style="margin-top:12px;">
						<?php echo $content_required; ?>
						<div class="page-header-column">
							<p>
								<input type="checkbox" name="_meta-generate-page-header-content-autop" id="_meta-generate-page-header-content-autop" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-content-autop', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-content-autop"><?php _e( 'Automatically add paragraphs', 'gp-premium' );?></label>
							</p>

							<p>
								<input type="checkbox" name="_meta-generate-page-header-content-padding" id="_meta-generate-page-header-content-padding" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-content-padding', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-content-padding"><?php _e( 'Add Padding', 'gp-premium' );?></label>
							</p>

							<p>
								<input class="image-background" type="checkbox" name="_meta-generate-page-header-image-background" id="_meta-generate-page-header-image-background" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-image-background"><?php _e( 'Add Background Image', 'gp-premium' );?></label>
							</p>

							<p class="parallax">
								<input type="checkbox" name="_meta-generate-page-header-image-background-overlay" id="_meta-generate-page-header-image-background-overlay" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-overlay', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-image-background-overlay"><?php _e( 'Use background color as overlay', 'gp-premium' );?></label>
							</p>

							<p class="parallax">
								<input type="checkbox" name="_meta-generate-page-header-image-background-fixed" id="_meta-generate-page-header-image-background-fixed" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-fixed', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-image-background-fixed"><?php _e( 'Parallax Effect', 'gp-premium' );?></label>
							</p>

							<p class="fullscreen">
								<input type="checkbox" name="_meta-generate-page-header-full-screen" id="_meta-generate-page-header-full-screen" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-full-screen', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-full-screen"><?php _e( 'Full Screen', 'gp-premium' );?></label>
							</p>

							<p class="vertical-center">
								<input type="checkbox" name="_meta-generate-page-header-vertical-center" id="_meta-generate-page-header-vertical-center" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-vertical-center', true ), 'yes' ); ?> />
								<label for="_meta-generate-page-header-vertical-center"><?php _e( 'Vertical center content', 'gp-premium' );?></label>
							</p>
						</div>

						<div class="page-header-column">
							<p>
								<label for="_meta-generate-page-header-image-background-type" class="example-row-title"><strong><?php _e( 'Container', 'gp-premium' );?></strong></label><br />
								<select name="_meta-generate-page-header-image-background-type" id="_meta-generate-page-header-image-background-type">
									<option value="" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-type', true ), '' ); ?>><?php _ex( 'Contained', 'Width', 'gp-premium' );?></option>
									<option value="fluid" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-type', true ), 'fluid' ); ?>><?php _e( 'Full Width', 'gp-premium' );?></option>
								</select>
							</p>

							<p>
								<label for="_meta-generate-page-header-image-background-type" class="example-row-title"><strong><?php _e( 'Inner Container', 'gp-premium' );?></strong></label><br />
								<select name="_meta-generate-page-header-inner-container" id="_meta-generate-page-header-inner-container">
									<option value="" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-inner-container', true ), '' ); ?>><?php _ex( 'Contained', 'Width', 'gp-premium' );?></option>
									<option value="full" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-inner-container', true ), 'full' ); ?>><?php _e( 'Full Width', 'gp-premium' );?></option>
								</select>
							</p>

							<p>
								<label for="_meta-generate-page-header-image-background-alignment" class="example-row-title"><strong><?php _e( 'Text Alignment', 'gp-premium' );?></strong></label><br />
								<select name="_meta-generate-page-header-image-background-alignment" id="_meta-generate-page-header-image-background-alignment">
									<option value="" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-alignment', true ), '' ); ?>><?php _e( 'Left', 'gp-premium' );?></option>
									<option value="center" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-alignment', true ), 'center' ); ?>><?php _e( 'Center', 'gp-premium' );?></option>
									<option value="right" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-alignment', true ), 'right' ); ?>><?php _e( 'Right', 'gp-premium' );?></option>
								</select>
							</p>

							<p>
								<label for="_meta-generate-page-header-image-background-spacing" class="example-row-title"><strong><?php _e( 'Top & Bottom Padding', 'gp-premium' );?></strong></label><br />
								<input placeholder="" style="width:45px" type="text" name="_meta-generate-page-header-image-background-spacing" id="_meta-generate-page-header-image-background-spacing" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-spacing', true ) ); ?>" />
								<select name="_meta-generate-page-header-image-background-spacing-unit" id="_meta-generate-page-header-image-background-spacing-unit">
									<option value="" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-spacing-unit', true ), '' ); ?>>px</option>
									<option value="%" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-spacing-unit', true ), '%' ); ?>>%</option>
								</select>
							</p>

							<p>
								<label for="_meta-generate-page-header-left-right-padding" class="example-row-title"><strong><?php _e( 'Left & Right Padding', 'gp-premium' );?></strong></label><br />
								<input placeholder="" style="width:45px" type="text" name="_meta-generate-page-header-left-right-padding" id="_meta-generate-page-header-left-right-padding" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-left-right-padding', true ) ); ?>" />
								<select name="_meta-generate-page-header-left-right-padding-unit" id="_meta-generate-page-header-left-right-padding-unit">
									<option value="" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-left-right-padding-unit', true ), '' ); ?>>px</option>
									<option value="%" <?php selected( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-left-right-padding-unit', true ), '%' ); ?>>%</option>
								</select>
							</p>
						</div>

						<div class="page-header-column last">
							<p>
								<label for="_meta-generate-page-header-image-background-color" class="example-row-title"><strong><?php _e( 'Background Color', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" style="width:45px" type="text" name="_meta-generate-page-header-image-background-color" id="_meta-generate-page-header-image-background-color" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-color', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-image-background-text-color" class="example-row-title"><strong><?php _e( 'Text Color', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-image-background-text-color" id="_meta-generate-page-header-image-background-text-color" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-text-color', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-image-background-link-color" class="example-row-title"><strong><?php _e( 'Link Color', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-image-background-link-color" id="_meta-generate-page-header-image-background-link-color" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-link-color', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-image-background-link-color-hover" class="example-row-title"><strong><?php _e( 'Link Color Hover', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-image-background-link-color-hover" id="_meta-generate-page-header-image-background-link-color-hover" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-image-background-link-color-hover', true ) ); ?>" />
							</p>
						</div>
						<div class="clear"></div>
					</div>
				</div>

				<div id="generate-video-background-tab" class="generate-tab-content generate-video-tab" style="display:none">
					<?php echo $content_required; ?>
					<p style="margin-top:0;">
						<label for="_meta-generate-page-header-video" class="example-row-title"><strong><?php _e( 'MP4 file', 'gp-premium' );?></strong></label><br />
						<input placeholder="http://" class="widefat" style="max-width:350px" id="_meta-generate-page-header-video" type="text" name="_meta-generate-page-header-video" value="<?php echo esc_url(generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-video', true )); ?>" />
						<button class="generate-upload-file button" type="button" data-type="video" data-title="<?php _e( 'Page Header Video', 'gp-premium' );?>" data-insert="<?php _e( 'Insert Video', 'gp-premium' ); ?>" data-prev="false">
							<?php _e( 'Choose Video', 'gp-premium' ) ;?>
						</button>
					</p>

					<p>
						<label for="_meta-generate-page-header-video-ogv" class="example-row-title"><strong><?php _e( 'OGV file', 'gp-premium' );?></strong></label><br />
						<input placeholder="http://" class="widefat" style="max-width:350px" id="_meta-generate-page-header-video-ogv" type="text" name="_meta-generate-page-header-video-ogv" value="<?php echo esc_url(generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-video-ogv', true )); ?>" />
						<button class="generate-upload-file button" type="button" data-type="video" data-title="<?php _e( 'Page Header Video', 'gp-premium' );?>" data-insert="<?php _e( 'Insert Video', 'gp-premium' ); ?>" data-prev="false">
							<?php _e( 'Choose Video', 'gp-premium' ) ;?>
						</button>
					</p>

					<p>
						<label for="_meta-generate-page-header-video-webm" class="example-row-title"><strong><?php _e( 'WEBM file', 'gp-premium' );?></strong></label><br />
						<input placeholder="http://" class="widefat" style="max-width:350px" id="_meta-generate-page-header-video-webm" type="text" name="_meta-generate-page-header-video-webm" value="<?php echo esc_url(generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-video-webm', true )); ?>" />
						<button class="generate-upload-file button" type="button" data-type="video" data-title="<?php _e( 'Page Header Video', 'gp-premium' );?>" data-insert="<?php _e( 'Insert Video', 'gp-premium' ); ?>" data-prev="false">
							<?php _e( 'Choose Video', 'gp-premium' ) ;?>
						</button>
					</p>

					<p>
						<label for="_meta-generate-page-header-video-overlay" class="example-row-title"><strong><?php _e( 'Overlay Color', 'gp-premium' );?></strong></label><br />
						<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" style="width:45px" type="text" name="_meta-generate-page-header-video-overlay" id="_meta-generate-page-header-video-overlay" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-video-overlay', true ) ); ?>" />
					</p>
				</div>

				<?php if ( generate_page_header_logo_exists() || generate_page_header_navigation_logo_exists() ) : ?>
					<div id="generate-logo-tab" class="generate-tab-content">
						<?php if ( function_exists( 'generate_get_defaults' ) ) {
							$generate_settings = wp_parse_args(
								get_option( 'generate_settings', array() ),
								generate_get_defaults()
							);

							if ( function_exists( 'generate_construct_logo' ) && ( '' !== $generate_settings[ 'logo' ] || get_theme_mod( 'custom_logo' ) ) ) {
								?>
								<p class="description" style="margin-top:0;">
									<?php _e( 'Overwrite your site-wide logo/header on this page.', 'gp-premium' ); ?>
								</p>

								<div id="preview-image" class="generate-logo-image">
									<?php if( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-logo', true ) != "") { ?>
										<img class="saved-image" src="<?php echo esc_url( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-logo', true ) );?>" width="100" style="margin-bottom:12px;" />
									<?php } ?>
								</div>

								<input style="width:350px" id="_meta-generate-page-header-logo" type="hidden" name="_meta-generate-page-header-logo" value="<?php echo esc_url(generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-logo', true )); ?>" />
								<button class="generate-upload-file button" type="button" data-type="image" data-title="<?php _e( 'Header / Logo', 'gp-premium' );?>" data-insert="<?php _e( 'Insert Logo', 'gp-premium' ); ?>" data-prev="true">
									<?php _e('Choose Logo', 'gp-premium' ) ;?>
								</button>
								<input class="image-id" id="_meta-generate-page-header-logo-id" type="hidden" name="_meta-generate-page-header-logo-id" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-logo-id', true ) ); ?>" />

								<?php if( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-logo', true ) != "") {
									$remove_button = 'style="display:inline-block;"';
								} else {
									$remove_button = 'style="display:none;"';
								}
								?>
								<button class="generate-page-header-remove-image button" type="button" <?php echo $remove_button; ?> data-input="#_meta-generate-page-header-logo" data-input-id="_meta-generate-page-header-logo-id" data-prev=".generate-logo-image">
									<?php _e( 'Remove Logo', 'gp-premium' ) ;?>
								</button>

								<p style="margin-bottom:20px;"></p>
								<?php
							}
						}

						if ( function_exists( 'generate_menu_plus_get_defaults' ) ) {
							$generate_menu_plus_settings = wp_parse_args(
								get_option( 'generate_menu_plus_settings', array() ),
								generate_menu_plus_get_defaults()
							);

							if ( '' !== $generate_menu_plus_settings[ 'sticky_menu_logo' ] ) {
								?>
								<p class="description" style="margin-top:0;">
									<?php _e( 'Overwrite your navigation logo on this page.', 'gp-premium' ); ?>
								</p>

								<div id="preview-image" class="generate-navigation-logo-image">
									<?php if( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-logo', true ) != "") { ?>
										<img class="saved-image" src="<?php echo esc_url( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-logo', true ) );?>" width="100" style="margin-bottom:12px;" />
									<?php } ?>
								</div>

								<input style="width:350px" id="_meta-generate-page-header-navigation-logo" type="hidden" name="_meta-generate-page-header-navigation-logo" value="<?php echo esc_url( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-logo', true ) ); ?>" />
								<button class="generate-upload-file button" type="button" data-type="image" data-title="<?php _e( 'Navigation Logo', 'gp-premium' );?>" data-insert="<?php _e( 'Insert Logo', 'page-header'); ?>" data-prev="true">
									<?php _e( 'Choose Logo', 'gp-premium' ) ;?>
								</button>
								<input class="image-id" id="_meta-generate-page-header-navigation-logo-id" type="hidden" name="_meta-generate-page-header-navigation-logo-id" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-logo-id', true ) ); ?>" />

								<?php if ( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-logo', true ) != "" ) {
									$remove_button = 'style="display:inline-block;"';
								} else {
									$remove_button = 'style="display:none;"';
								}
								?>

								<button class="generate-page-header-remove-image button" type="button" <?php echo $remove_button; ?> data-input="#_meta-generate-page-header-navigation-logo" data-input-id="_meta-generate-page-header-navigation-logo-id" data-prev=".generate-navigation-logo-image">
									<?php _e( 'Remove Logo', 'gp-premium' ) ;?>
								</button>
							<?php }
						}
						?>
					</div>
				<?php endif; ?>

				<div id="generate-advanced-tab" class="generate-tab-content" style="display:none">
					<?php echo $content_required; ?>
					<p style="margin-top:0;">
						<input type="checkbox" name="_meta-generate-page-header-combine" id="_meta-generate-page-header-combine" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-combine', true ), 'yes' ); ?> />
						<label for="_meta-generate-page-header-combine"><?php _e( 'Merge with site header', 'gp-premium' );?></label>
					</p>

					<div class="combination-options">
						<p class="absolute-position">
							<input type="checkbox" name="_meta-generate-page-header-absolute-position" id="_meta-generate-page-header-absolute-position" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-absolute-position', true ), 'yes' ); ?> />
							<label for="_meta-generate-page-header-absolute-position"><?php _e( 'Place content behind header (sliders etc..)', 'gp-premium' );?></label>
						</p>

						<p>
							<label for="_meta-generate-page-header-site-title" class="example-row-title"><?php _e( 'Site Title', 'gp-premium' );?></label><br />
							<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-site-title" id="_meta-generate-page-header-site-title" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-site-title', true ) ); ?>" />
						</p>

						<p>
							<label for="_meta-generate-page-header-site-tagline" class="example-row-title"><?php _e( 'Site Tagline', 'gp-premium' );?></label><br />
							<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-site-tagline" id="_meta-generate-page-header-site-tagline" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-site-tagline', true ) ); ?>" />
						</p>

						<p>
							<input type="checkbox" name="_meta-generate-page-header-transparent-navigation" id="_meta-generate-page-header-transparent-navigation" value="yes" <?php checked( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-transparent-navigation', true ), 'yes' ); ?> />
							<label for="_meta-generate-page-header-transparent-navigation"><?php _e( 'Custom Navigation Colors', 'gp-premium' );?></label>
						</p>

						<div class="navigation-colors">
							<p>
								<label for="_meta-generate-page-header-navigation-background" class="example-row-title"><strong><?php _e( 'Navigation Background', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" style="width:45px" type="text" name="_meta-generate-page-header-navigation-background" id="_meta-generate-page-header-navigation-background" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-background', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-navigation-text" class="example-row-title"><strong><?php _e( 'Navigation Text', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-navigation-text" id="_meta-generate-page-header-navigation-text" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-text', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-navigation-background-hover" class="example-row-title"><strong><?php _e( 'Navigation Background Hover', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" style="width:45px" type="text" name="_meta-generate-page-header-navigation-background-hover" id="_meta-generate-page-header-navigation-background-hover" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-background-hover', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-navigation-text-hover" class="example-row-title"><strong><?php _e( 'Navigation Text Hover', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-navigation-text-hover" id="_meta-generate-page-header-navigation-text-hover" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-text-hover', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-navigation-background-current" class="example-row-title"><strong><?php _e( 'Navigation Background Current', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" data-alpha-enabled="true" data-alpha-color-type="hex" style="width:45px" type="text" name="_meta-generate-page-header-navigation-background-current" id="_meta-generate-page-header-navigation-background-current" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-background-current', true ) ); ?>" />
							</p>

							<p>
								<label for="_meta-generate-page-header-navigation-text-current" class="example-row-title"><strong><?php _e( 'Navigation Text Current', 'gp-premium' );?></strong></label><br />
								<input class="color-picker" style="width:45px" type="text" name="_meta-generate-page-header-navigation-text-current" id="_meta-generate-page-header-navigation-text-current" value="<?php echo esc_attr( generate_page_header_get_post_meta( get_the_ID(), '_meta-generate-page-header-navigation-text-current', true ) ); ?>" />
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	    <?php
	}
}

if ( ! function_exists( 'save_generate_page_header_meta' ) ) {
	add_action( 'save_post', 'save_generate_page_header_meta' );
	/**
	 * Save our settings
	 */
	function save_generate_page_header_meta($post_id) {
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'generate_page_header_nonce' ] ) && wp_verify_nonce( $_POST[ 'generate_page_header_nonce' ], basename( __FILE__ ) ) ) ? true : false;

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
	    }

		// Check that the logged in user has permission to edit this post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$options = array(
			'_meta-generate-page-header-content' => 'FILTER_CONTENT',
			'_meta-generate-page-header-image' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-image-id' => 'FILTER_SANITIZE_NUMBER_INT',
			'_meta-generate-page-header-image-link' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-enable-image-crop' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-crop' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-width' => 'FILTER_SANITIZE_NUMBER_INT',
			'_meta-generate-page-header-image-height' => 'FILTER_SANITIZE_NUMBER_INT',
			'_meta-generate-page-header-image-background-type' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-inner-container' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-alignment' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-spacing' => 'FILTER_SANITIZE_NUMBER_INT',
			'_meta-generate-page-header-image-background-spacing-unit' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-left-right-padding' => 'FILTER_SANITIZE_NUMBER_INT',
			'_meta-generate-page-header-left-right-padding-unit' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-color' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-text-color' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-link-color' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-link-color-hover' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-navigation-background' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-navigation-text' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-navigation-background-hover' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-navigation-text-hover' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-navigation-background-current' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-navigation-text-current' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-site-title' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-site-tagline' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-video' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-video-ogv' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-video-webm' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-video-overlay' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-content-autop' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-content-padding' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-full-screen' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-vertical-center' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-fixed' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-image-background-overlay' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-combine' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-absolute-position' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-transparent-navigation' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-add-to-excerpt' => 'FILTER_SANITIZE_STRING',
			'_meta-generate-page-header-logo' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-logo-id' => 'FILTER_SANITIZE_NUMBER_INT',
			'_meta-generate-page-header-navigation-logo' => 'FILTER_SANITIZE_URL',
			'_meta-generate-page-header-navigation-logo-id' => 'FILTER_SANITIZE_NUMBER_INT',
		);

		if ( ! defined( 'GENERATE_LAYOUT_META_BOX' ) ) {
			$options[ '_generate-select-page-header' ] = 'FILTER_SANITIZE_NUMBER_INT';
		}

		foreach ( $options as $key => $sanitize ) {
			if ( 'FILTER_SANITIZE_STRING' == $sanitize ) {
				$value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRING );
			} elseif ( 'FILTER_SANITIZE_URL' == $sanitize ) {
				$value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_URL );
			} elseif ( 'FILTER_SANITIZE_NUMBER_INT' == $sanitize ) {
				$value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_NUMBER_INT );
			} elseif ( 'FILTER_CONTENT' == $sanitize && isset( $_POST[ $key ] ) ) {
				if ( current_user_can( 'unfiltered_html' ) ) {
					$value = $_POST[ $key ];
				} else {
					$value = wp_kses_post( $_POST[ $key ] );
				}
			} else {
				$value = filter_input( INPUT_POST, $key, FILTER_DEFAULT );
			}

			if ( $value ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}
}

add_action( 'add_meta_boxes', 'generate_page_header_tags_add_meta_box' );
/**
 * Add our Template Tags meta box.
 *
 * @param WP_Post $post Current post object.
 *
 * @since 1.4
 */
function generate_page_header_tags_add_meta_box( $post ) {
	add_meta_box( 'generate_page_header_tags', __( 'Template Tags', 'gp-premium' ), 'generate_page_header_tags_do_meta_box', 'generate_page_header', 'side', 'low' );
}

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 *
 * @since 1.4
 */
function generate_page_header_tags_do_meta_box( $post ) {
    ?>
	<input type="text" readonly="readonly" value="{{post_title}}" />
	<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;"><?php _e( 'The content title of the current post/taxonomy.', 'gp-premium' ); ?></p>

	<input type="text" readonly="readonly" value="{{post_date}}" />
	<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;"><?php _e( 'The published date of the current post.', 'gp-premium' ); ?></p>

	<input type="text" readonly="readonly" value="{{post_author}}" />
	<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;"><?php _e( 'The author of the current post.', 'gp-premium' ); ?></p>

	<input type="text" readonly="readonly" value="{{post_terms.taxonomy}}" />
	<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;"><?php _e( 'The terms attached to the chosen taxonomy (category, post_tag, product_cat).', 'gp-premium' ); ?></p>

	<input type="text" readonly="readonly" value='{{custom_field.name}}' />
	<p class="decription" style="margin-top:0;opacity:0.8;font-size:85%;"><?php _e( 'Custom post meta. Replace "name" with the name of your custom field.', 'gp-premium' ); ?></p>
	<?php
}

add_action( 'generate_layout_meta_box_content', 'generate_premium_page_header_meta_box_options' );
/**
 * Add the meta box options to the Layout meta box in the new GP
 *
 * @since 1.4
 */
function generate_premium_page_header_meta_box_options( $stored_meta ) {
	$stored_meta = (array) get_post_meta( get_the_ID() );
	$stored_meta['_generate-select-page-header'][0] = ( isset( $stored_meta['_generate-select-page-header'][0] ) ) ? $stored_meta['_generate-select-page-header'][0] : '';
	?>
	<div id="generate-layout-page-header" style="display: none;">
		<?php
		$page_headers = get_posts(array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'post_type' => 'generate_page_header',
			'suppress_filters' => false,
		));

		if ( count( $page_headers ) > 0 ) :
		?>
		<p style="margin-top:0;">
			<select name="_generate-select-page-header" id="_generate-select-page-header">
				<option value="" <?php selected( $stored_meta['_generate-select-page-header'][0], '' ); ?>></option>
				<?php
				foreach( $page_headers as $header ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>',
						$header->ID,
						selected( $stored_meta['_generate-select-page-header'][0], $header->ID ),
						$header->post_title
					);
				}
				?>
			</select>
		</p>
		<?php else : ?>
			<p>
				<?php
				printf( __( 'No Page Headers found. Want to <a href="%1$s" target="_blank">create one</a>?', 'gp-premium' ),
					esc_url( admin_url( 'post-new.php?post_type=generate_page_header' ) )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
    <?php
}

add_action( 'generate_layout_meta_box_menu_item', 'generate_premium_page_header_menu_item' );

function generate_premium_page_header_menu_item() {
	?>
	<li class="page-heade-meta-menu-item"><a href="#generate-layout-page-header"><?php _e( 'Page Header', 'gp-premium' ); ?></a></li>
	<?php
}

add_action( 'generate_layout_meta_box_save', 'generate_premium_save_page_header_meta' );
/**
 * Save the Page Header meta box values
 *
 * @since 1.4
 */
function generate_premium_save_page_header_meta( $post_id ) {
	$page_header_key   = '_generate-select-page-header';
	$page_header_value = filter_input( INPUT_POST, $page_header_key, FILTER_SANITIZE_NUMBER_INT );

	if ( $page_header_value ) {
		update_post_meta( $post_id, $page_header_key, $page_header_value );
	} else {
		delete_post_meta( $post_id, $page_header_key );
	}
}
