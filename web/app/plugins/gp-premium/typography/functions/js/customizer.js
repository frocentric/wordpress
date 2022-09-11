function gp_premium_typography_live_update( id, selector, property, unit, media, settings ) {
	settings = typeof settings !== 'undefined' ? settings : 'generate_settings';
	wp.customize( settings + '[' + id + ']', function( value ) {
		value.bind( function( newval ) {
			// Get our unit if applicable
			unit = typeof unit !== 'undefined' ? unit : '';

			var isTablet = ( 'tablet' == id.substring( 0, 6 ) ) ? true : false,
				isMobile = ( 'mobile' == id.substring( 0, 6 ) ) ? true : false;

			if ( isTablet ) {
				if ( '' == wp.customize(settings + '[' + id + ']').get() ) {
					var desktopID = id.replace( 'tablet_', '' );
					newval = wp.customize(settings + '[' + desktopID + ']').get();
				}
			}

			if ( isMobile ) {
				if ( '' == wp.customize(settings + '[' + id + ']').get() ) {
					var desktopID = id.replace( 'mobile_', '' );
					newval = wp.customize(settings + '[' + desktopID + ']').get();
				}
			}

			if ( 'buttons_font_size' == id && '' == wp.customize('generate_settings[buttons_font_size]').get() ) {
				newval = wp.customize('generate_settings[body_font_size]').get();
			}

			if ( 'single_post_title_weight' == id && '' == wp.customize('generate_settings[single_post_title_weight]').get() ) {
				newval = wp.customize('generate_settings[heading_1_weight]').get();
			}

			if ( 'single_post_title_transform' == id && '' == wp.customize('generate_settings[single_post_title_transform]').get() ) {
				newval = wp.customize('generate_settings[heading_1_transform]').get();
			}

			if ( 'archive_post_title_weight' == id && '' == wp.customize('generate_settings[archive_post_title_weight]').get() ) {
				newval = wp.customize('generate_settings[heading_2_weight]').get();
			}

			if ( 'archive_post_title_transform' == id && '' == wp.customize('generate_settings[archive_post_title_transform]').get() ) {
				newval = wp.customize('generate_settings[heading_2_transform]').get();
			}

			// We're using a desktop value
			if ( ! isTablet && ! isMobile ) {

				var tabletValue = ( typeof wp.customize(settings + '[tablet_' + id + ']') !== 'undefined' ) ? wp.customize(settings + '[tablet_' + id + ']').get() : '',
					mobileValue = ( typeof wp.customize(settings + '[mobile_' + id + ']') !== 'undefined' ) ? wp.customize(settings + '[mobile_' + id + ']').get() : '';

				// The tablet setting exists, mobile doesn't
				if ( '' !== tabletValue && '' == mobileValue ) {
					media = gp_typography.desktop + ', ' + gp_typography.mobile;
				}

				// The tablet setting doesn't exist, mobile does
				if ( '' == tabletValue && '' !== mobileValue ) {
					media = gp_typography.desktop + ', ' + gp_typography.tablet;
				}

				// The tablet setting doesn't exist, neither does mobile
				if ( '' == tabletValue && '' == mobileValue ) {
					media = gp_typography.desktop + ', ' + gp_typography.tablet + ', ' + gp_typography.mobile;
				}

			}

			// Check if media query
			media_query = typeof media !== 'undefined' ? 'media="' + media + '"' : '';

			jQuery( 'head' ).append( '<style id="' + id + '" ' + media_query + '>' + selector + '{' + property + ':' + newval + unit + ';}</style>' );
			setTimeout(function() {
				jQuery( 'style#' + id ).not( ':last' ).remove();
			}, 1000);

			setTimeout("jQuery('body').trigger('generate_spacing_updated');", 1000);
		} );
	} );
}

/**
 * Body font size, weight and transform
 */
gp_premium_typography_live_update( 'body_font_size', 'body, button, input, select, textarea', 'font-size', 'px' );
gp_premium_typography_live_update( 'body_line_height', 'body', 'line-height', '' );
gp_premium_typography_live_update( 'paragraph_margin', 'p, .entry-content > [class*="wp-block-"]:not(:last-child)', 'margin-bottom', 'em' );
gp_premium_typography_live_update( 'body_font_weight', 'body, button, input, select, textarea', 'font-weight' );
gp_premium_typography_live_update( 'body_font_transform', 'body, button, input, select, textarea', 'text-transform' );

/**
 * Top bar font size, weight and transform
 */
gp_premium_typography_live_update( 'top_bar_font_size', '.top-bar', 'font-size', 'px' );
gp_premium_typography_live_update( 'top_bar_font_weight', '.top-bar', 'font-weight' );
gp_premium_typography_live_update( 'top_bar_font_transform', '.top-bar', 'text-transform' );

/**
 * Site title font size, weight and transform
 */
gp_premium_typography_live_update( 'site_title_font_size', '.main-title, .navigation-branding .main-title', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'tablet_site_title_font_size', '.main-title, .navigation-branding .main-title', 'font-size', 'px', gp_typography.tablet );
gp_premium_typography_live_update( 'mobile_site_title_font_size', '.main-title, .navigation-branding .main-title', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'site_title_font_weight', '.main-title, .navigation-branding .main-title', 'font-weight' );
gp_premium_typography_live_update( 'site_title_font_transform', '.main-title, .navigation-branding .main-title', 'text-transform' );

/**
 * Site description font size, weight and transform
 */
gp_premium_typography_live_update( 'site_tagline_font_size', '.site-description', 'font-size', 'px' );
gp_premium_typography_live_update( 'site_tagline_font_weight', '.site-description', 'font-weight' );
gp_premium_typography_live_update( 'site_tagline_font_transform', '.site-description', 'text-transform' );

/**
 * Main navigation font size, weight and transform
 */
gp_premium_typography_live_update( 'navigation_font_size', '.main-navigation a, .menu-toggle, .main-navigation .menu-bar-items', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'tablet_navigation_font_size', '.main-navigation a, .menu-toggle, .main-navigation .menu-bar-items', 'font-size', 'px', gp_typography.tablet );
gp_premium_typography_live_update( 'mobile_navigation_font_size', '.main-navigation:not(.slideout-navigation) a, .menu-toggle, .main-navigation .menu-bar-items', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'navigation_font_weight', '.main-navigation a, .menu-toggle', 'font-weight' );
gp_premium_typography_live_update( 'navigation_font_transform', '.main-navigation a, .menu-toggle', 'text-transform' );

/**
 * Site title when in navigation.
 */
 gp_premium_typography_live_update( 'navigation_site_title_font_size', '.navigation-branding .main-title', 'font-size', 'px', gp_typography.desktop );
 gp_premium_typography_live_update( 'tablet_navigation_site_title_font_size', '.navigation-branding .main-title', 'font-size', 'px', gp_typography.tablet );
 gp_premium_typography_live_update( 'mobile_navigation_site_title_font_size', '.navigation-branding .main-title', 'font-size', 'px', gp_typography.mobile );

/**
 * Secondary navigation font size, weight and transform
 */
gp_premium_typography_live_update( 'secondary_navigation_font_size', '.secondary-navigation .main-nav ul li a,.secondary-navigation .menu-toggle, .secondary-navigation .top-bar, .secondary-navigation .secondary-menu-bar-items', 'font-size', 'px', '', 'generate_secondary_nav_settings' );
gp_premium_typography_live_update( 'secondary_navigation_font_weight', '.secondary-navigation .main-nav ul li a,.secondary-navigation .menu-toggle, .secondary-navigation .top-bar', 'font-weight', '', '', 'generate_secondary_nav_settings' );
gp_premium_typography_live_update( 'secondary_navigation_font_transform', '.secondary-navigation .main-nav ul li a,.secondary-navigation .menu-toggle, .secondary-navigation .top-bar', 'text-transform', '', '', 'generate_secondary_nav_settings' );

/**
 * Buttons
 */
gp_premium_typography_live_update( 'buttons_font_size', 'button:not(.menu-toggle),html input[type="button"],input[type="reset"],input[type="submit"],.button,.button:visited,.wp-block-button .wp-block-button__link,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button', 'font-size', 'px' );
gp_premium_typography_live_update( 'buttons_font_weight', 'button:not(.menu-toggle),html input[type="button"],input[type="reset"],input[type="submit"],.button,.button:visited,.wp-block-button .wp-block-button__link,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button', 'font-weight' );
gp_premium_typography_live_update( 'buttons_font_transform', 'button:not(.menu-toggle),html input[type="button"],input[type="reset"],input[type="submit"],.button,.button:visited,.wp-block-button .wp-block-button__link,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button', 'text-transform' );

/**
 * H1 font size, weight and transform
 */
gp_premium_typography_live_update( 'heading_1_font_size', 'h1', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'mobile_heading_1_font_size', 'h1', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'heading_1_weight', 'h1', 'font-weight' );
gp_premium_typography_live_update( 'heading_1_transform', 'h1', 'text-transform' );
gp_premium_typography_live_update( 'heading_1_line_height', 'h1', 'line-height', 'em' );
gp_premium_typography_live_update( 'heading_1_margin_bottom', 'h1', 'margin-bottom', 'px' );

/**
 * Single content title (h1)
 */
gp_premium_typography_live_update( 'single_post_title_font_size', 'h1.entry-title', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'single_post_title_font_size_mobile', 'h1.entry-title', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'single_post_title_weight', 'h1.entry-title', 'font-weight' );
gp_premium_typography_live_update( 'single_post_title_transform', 'h1.entry-title', 'text-transform' );
gp_premium_typography_live_update( 'single_post_title_line_height', 'h1.entry-title', 'line-height', 'em' );

/**
 * H2 font size, weight and transform
 */
gp_premium_typography_live_update( 'heading_2_font_size', 'h2', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'mobile_heading_2_font_size', 'h2', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'heading_2_weight', 'h2', 'font-weight' );
gp_premium_typography_live_update( 'heading_2_transform', 'h2', 'text-transform' );
gp_premium_typography_live_update( 'heading_2_line_height', 'h2', 'line-height', 'em' );
gp_premium_typography_live_update( 'heading_2_margin_bottom', 'h2', 'margin-bottom', 'px' );

/**
 * Archive post title (h1)
 */
gp_premium_typography_live_update( 'archive_post_title_font_size', 'h2.entry-title', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'archive_post_title_font_size_mobile', 'h2.entry-title', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'archive_post_title_weight', 'h2.entry-title', 'font-weight' );
gp_premium_typography_live_update( 'archive_post_title_transform', 'h2.entry-title', 'text-transform' );
gp_premium_typography_live_update( 'archive_post_title_line_height', 'h2.entry-title', 'line-height', 'em' );

/**
 * H3 font size, weight and transform
 */
gp_premium_typography_live_update( 'heading_3_font_size', 'h3', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'mobile_heading_3_font_size', 'h3', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'heading_3_weight', 'h3', 'font-weight' );
gp_premium_typography_live_update( 'heading_3_transform', 'h3', 'text-transform' );
gp_premium_typography_live_update( 'heading_3_line_height', 'h3', 'line-height', 'em' );
gp_premium_typography_live_update( 'heading_3_margin_bottom', 'h3', 'margin-bottom', 'px' );

/**
 * H4 font size, weight and transform
 */
gp_premium_typography_live_update( 'heading_4_font_size', 'h4', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'mobile_heading_4_font_size', 'h4', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'heading_4_weight', 'h4', 'font-weight' );
gp_premium_typography_live_update( 'heading_4_transform', 'h4', 'text-transform' );
gp_premium_typography_live_update( 'heading_4_line_height', 'h4', 'line-height', 'em' );

/**
 * H5 font size, weight and transform
 */
gp_premium_typography_live_update( 'heading_5_font_size', 'h5', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'mobile_heading_5_font_size', 'h5', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'heading_5_weight', 'h5', 'font-weight' );
gp_premium_typography_live_update( 'heading_5_transform', 'h5', 'text-transform' );
gp_premium_typography_live_update( 'heading_5_line_height', 'h5', 'line-height', 'em' );

/**
 * H6 font size, weight and transform
 */
gp_premium_typography_live_update( 'heading_6_font_size', 'h6', 'font-size', 'px' );
gp_premium_typography_live_update( 'heading_6_weight', 'h6', 'font-weight' );
gp_premium_typography_live_update( 'heading_6_transform', 'h6', 'text-transform' );
gp_premium_typography_live_update( 'heading_6_line_height', 'h6', 'line-height', 'em' );

/**
 * Widget title font size, weight and transform
 */
gp_premium_typography_live_update( 'widget_title_font_size', '.widget-title', 'font-size', 'px' );
gp_premium_typography_live_update( 'widget_title_font_weight', '.widget-title', 'font-weight' );
gp_premium_typography_live_update( 'widget_title_font_transform', '.widget-title', 'text-transform' );
gp_premium_typography_live_update( 'widget_title_separator', '.widget-title', 'margin-bottom', 'px' );
gp_premium_typography_live_update( 'widget_content_font_size', '.sidebar .widget, .footer-widgets .widget', 'font-size', 'px' );

/**
 * Footer font size, weight and transform
 */
gp_premium_typography_live_update( 'footer_font_size', '.site-info', 'font-size', 'px' );
gp_premium_typography_live_update( 'footer_weight', '.site-info', 'font-weight' );
gp_premium_typography_live_update( 'footer_transform', '.site-info', 'text-transform' );
gp_premium_typography_live_update( 'footer_line_height', '.site-info', 'line-height', 'em' );

/**
 * WooCommerce product title
 */
gp_premium_typography_live_update( 'wc_product_title_font_size', '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'mobile_wc_product_title_font_size', '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'wc_product_title_font_weight', '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title', 'font-weight' );
gp_premium_typography_live_update( 'wc_product_title_font_transform', '.woocommerce ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce ul.products li.product .woocommerce-loop-category__title', 'text-transform' );

gp_premium_typography_live_update( 'wc_related_product_title_font_size', '.woocommerce .up-sells ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce .cross-sells ul.products li.product .woocommerce-LoopProduct-link h2, .woocommerce .related ul.products li.product .woocommerce-LoopProduct-link h2', 'font-size', 'px' );

/**
 * Slideout navigation font size, weight and transform
 */
gp_premium_typography_live_update( 'slideout_font_size', '.slideout-navigation.main-navigation .main-nav ul li a', 'font-size', 'px', gp_typography.desktop );
gp_premium_typography_live_update( 'slideout_mobile_font_size', '.slideout-navigation.main-navigation .main-nav ul li a', 'font-size', 'px', gp_typography.mobile );
gp_premium_typography_live_update( 'slideout_font_weight', '.slideout-navigation.main-navigation .main-nav ul li a', 'font-weight' );
gp_premium_typography_live_update( 'slideout_font_transform', '.slideout-navigation.main-navigation .main-nav ul li a', 'text-transform' );
