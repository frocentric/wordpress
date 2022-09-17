function generate_spacing_live_update( name, id, selector, property, negative, divide, media, unit ) {
	settings = typeof settings !== 'undefined' ? settings : 'generate_spacing_settings';
	wp.customize( settings + '[' + id + ']', function( value ) {
		value.bind( function( newval ) {
			negative = typeof negative !== 'undefined' ? negative : false;
			media = typeof media !== 'undefined' ? media : '';
			divide = typeof divide !== 'undefined' ? divide : false;
			unit = typeof unit !== 'undefined' ? unit : 'px';

			// Get new value
			newval = ( divide ) ? newval / 2 : newval;

			// Check if negative integer
			negative = ( negative ) ? '-' : '';

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

			// We're using a desktop value
			if ( ! isTablet && ! isMobile ) {

				var tabletValue = ( typeof wp.customize(settings + '[tablet_' + id + ']') !== 'undefined' ) ? wp.customize(settings + '[tablet_' + id + ']').get() : '',
					mobileValue = ( typeof wp.customize(settings + '[mobile_' + id + ']') !== 'undefined' ) ? wp.customize(settings + '[mobile_' + id + ']').get() : '';

				// The tablet setting exists, mobile doesn't
				if ( '' !== tabletValue && '' == mobileValue ) {
					media = gp_spacing.desktop + ', ' + gp_spacing.mobile;
				}

				// The tablet setting doesn't exist, mobile does
				if ( '' == tabletValue && '' !== mobileValue ) {
					media = gp_spacing.desktop + ', ' + gp_spacing.tablet;
				}

				// The tablet setting doesn't exist, neither does mobile
				if ( '' == tabletValue && '' == mobileValue ) {
					media = gp_spacing.desktop + ', ' + gp_spacing.tablet + ', ' + gp_spacing.mobile;
				}

			}

			// Check if media query
			media_query = ( '' !== media ) ? 'media="' + media + '"' : '';

			jQuery( 'head' ).append( '<style id="' + name + '" ' + media_query + '>' + selector + '{' + property + ':' + negative + newval + unit +';}</style>' );
			setTimeout(function() {
				jQuery( 'style#' + name ).not( ':last' ).remove();
			}, 50 );

			jQuery('body').trigger('generate_spacing_updated');
		} );
	} );
}

/**
 * Top bar padding
 */
generate_spacing_live_update( 'top_bar_top', 'top_bar_top', '.inside-top-bar', 'padding-top' );
generate_spacing_live_update( 'top_bar_right', 'top_bar_right', '.inside-top-bar', 'padding-right' );
generate_spacing_live_update( 'top_bar_bottom', 'top_bar_bottom', '.inside-top-bar', 'padding-bottom' );
generate_spacing_live_update( 'top_bar_left', 'top_bar_left', '.inside-top-bar', 'padding-left' );

/**
 * Header padding
 */
generate_spacing_live_update( 'header_top', 'header_top', '.inside-header', 'padding-top', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'header_right', 'header_right', '.inside-header', 'padding-right', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'header_bottom', 'header_bottom', '.inside-header', 'padding-bottom', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'header_left', 'header_left', '.inside-header', 'padding-left', false, false, gp_spacing.desktop );

generate_spacing_live_update( 'mobile_header_top', 'mobile_header_top', '.inside-header', 'padding-top', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_header_right', 'mobile_header_right', '.inside-header', 'padding-right', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_header_bottom', 'mobile_header_bottom', '.inside-header', 'padding-bottom', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_header_left', 'mobile_header_left', '.inside-header', 'padding-left', false, false, gp_spacing.mobile );

jQuery( window ).on( 'load', function() {
	var containerAlignment = wp.customize( 'generate_settings[container_alignment]' );

	if ( gp_spacing.isFlex && containerAlignment && 'text' === containerAlignment.get() ) {
		generate_spacing_live_update( 'header_left_sticky_nav', 'header_left', '.main-navigation.navigation-stick:not(.has-branding) .inside-navigation.grid-container', 'padding-left', false, false, gp_spacing.desktop );
		generate_spacing_live_update( 'header_right_sticky_nav', 'header_right', '.main-navigation.navigation-stick:not(.has-branding) .inside-navigation.grid-container', 'padding-right', false, false, gp_spacing.desktop );

		generate_spacing_live_update( 'mobile_header_left_sticky_nav', 'mobile_header_left', '.main-navigation.navigation-stick:not(.has-branding) .inside-navigation.grid-container', 'padding-left', false, false, gp_spacing.mobile );
		generate_spacing_live_update( 'mobile_header_right_sticky_nav', 'mobile_header_right', '.main-navigation.navigation-stick:not(.has-branding) .inside-navigation.grid-container', 'padding-right', false, false, gp_spacing.mobile );
	}
} );

/**
 * Content padding
 */
var content_areas = '.separate-containers .inside-article, \
					.separate-containers .comments-area, \
					.separate-containers .page-header, \
					.separate-containers .paging-navigation, \
					.one-container .site-content, \
					.inside-page-header, \
					.wp-block-group__inner-container';

generate_spacing_live_update( 'content_top', 'content_top', content_areas, 'padding-top', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'content_right', 'content_right', content_areas, 'padding-right', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'content_bottom', 'content_bottom', content_areas, 'padding-bottom', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'content_left', 'content_left', content_areas, 'padding-left', false, false, gp_spacing.desktop );

jQuery( window ).on( 'load', function() {
	var containerAlignment = wp.customize( 'generate_settings[container_alignment]' );

	if ( gp_spacing.isFlex && containerAlignment && 'text' === containerAlignment.get() ) {
		generate_spacing_live_update( 'content_left_nav_as_header', 'content_left', '.main-navigation.has-branding .inside-navigation.grid-container, .main-navigation.has-branding .inside-navigation.grid-container', 'padding-left', false, false, gp_spacing.desktop );
		generate_spacing_live_update( 'content_right_nav_as_header', 'content_right', '.main-navigation.has-branding .inside-navigation.grid-container, .main-navigation.has-branding .inside-navigation.grid-container', 'padding-right', false, false, gp_spacing.desktop );
	}
} );

generate_spacing_live_update( 'one_container_post_content_bottom', 'content_bottom', '.one-container.archive .post:not(:last-child),.one-container.blog .post:not(:last-child)', 'padding-bottom' );

/* Mobile content padding */
generate_spacing_live_update( 'mobile_content_top', 'mobile_content_top', content_areas, 'padding-top', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_content_right', 'mobile_content_right', content_areas, 'padding-right', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_content_bottom', 'mobile_content_bottom', content_areas, 'padding-bottom', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_content_left', 'mobile_content_left', content_areas, 'padding-left', false, false, gp_spacing.mobile );

generate_spacing_live_update( 'content-margin-right', 'content_right', '.one-container.right-sidebar .site-main,.one-container.both-right .site-main', 'margin-right' );
generate_spacing_live_update( 'content-margin-left', 'content_left', '.one-container.left-sidebar .site-main,.one-container.both-left .site-main', 'margin-left' );
generate_spacing_live_update( 'content-margin-right-both', 'content_right', '.one-container.both-sidebars .site-main', 'margin-right' );
generate_spacing_live_update( 'content-margin-left-both', 'content_left', '.one-container.both-sidebars .site-main', 'margin-left' );

/* Content element separator */

generate_spacing_live_update( 'content_element_separator_top', 'content_element_separator', '.post-image:not(:first-child), .page-content:not(:first-child), .entry-content:not(:first-child), .entry-summary:not(:first-child), footer.entry-meta', 'margin-top', false, false, false, 'em' );
generate_spacing_live_update( 'content_element_separator_bottom', 'content_element_separator', '.post-image-above-header .inside-article div.featured-image, .post-image-above-header .inside-article div.post-image', 'margin-bottom', false, false, false, 'em' );

/**
 * Featured image padding
 */
var featured_image_no_padding_x = '.post-image-below-header.post-image-aligned-center .no-featured-image-padding .post-image, \
								.post-image-below-header.post-image-aligned-center .no-featured-image-padding .featured-image';

generate_spacing_live_update( 'featured_image_padding_right', 'content_right', featured_image_no_padding_x, 'margin-right', true, false, gp_spacing.desktop );
generate_spacing_live_update( 'featured_image_padding_left', 'content_left', featured_image_no_padding_x, 'margin-left', true, false, gp_spacing.desktop );
generate_spacing_live_update( 'mobile_featured_image_padding_right', 'mobile_content_right', featured_image_no_padding_x, 'margin-right', true, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_featured_image_padding_left', 'mobile_content_left', featured_image_no_padding_x, 'margin-left', true, false, gp_spacing.mobile );

var featured_image_no_padding_y = '.post-image-above-header.post-image-aligned-center .no-featured-image-padding .post-image, \
								.post-image-above-header.post-image-aligned-center .no-featured-image-padding .featured-image';

generate_spacing_live_update( 'featured_image_padding_top', 'content_top', featured_image_no_padding_y, 'margin-top', true, false, gp_spacing.desktop );
generate_spacing_live_update( 'featured_image_padding_right', 'content_right', featured_image_no_padding_y, 'margin-right', true, false, gp_spacing.desktop );
generate_spacing_live_update( 'featured_image_padding_left', 'content_left', featured_image_no_padding_y, 'margin-left', true, false, gp_spacing.desktop );
generate_spacing_live_update( 'mobile_featured_image_padding_top', 'mobile_content_top', featured_image_no_padding_y, 'margin-top', true, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_featured_image_padding_right', 'mobile_content_right', featured_image_no_padding_y, 'margin-right', true, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_featured_image_padding_left', 'mobile_content_left', featured_image_no_padding_y, 'margin-left', true, false, gp_spacing.mobile );

/**
 * Main navigation spacing
 */
var menu_items = '.main-navigation .main-nav ul li a,\
				.main-navigation .menu-toggle,\
				.main-navigation .mobile-bar-items a,\
				.main-navigation .menu-bar-item > a';

// Menu item width
generate_spacing_live_update( 'menu_item_padding_left', 'menu_item', menu_items + ', .slideout-navigation button.slideout-exit', 'padding-left', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'menu_item_padding_right', 'menu_item', menu_items + ', .slideout-navigation button.slideout-exit', 'padding-right', false, false, gp_spacing.desktop );

// Tablet menu item width
//generate_spacing_live_update( 'tablet_menu_item_padding_left', 'tablet_menu_item', menu_items, 'padding-left', false, false, gp_spacing.tablet );
//generate_spacing_live_update( 'tablet_menu_item_padding_right', 'tablet_menu_item', menu_items, 'padding-right', false, false, gp_spacing.tablet );

// Mobile menu item width
generate_spacing_live_update( 'mobile_menu_item_padding_left', 'mobile_menu_item', '.main-navigation .menu-toggle,.main-navigation .mobile-bar-items a, .main-navigation .menu-bar-item > a', 'padding-left', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_menu_item_padding_right', 'mobile_menu_item', '.main-navigation .menu-toggle,.main-navigation .mobile-bar-items a, .main-navigation .menu-bar-item > a', 'padding-right', false, false, gp_spacing.mobile );

// Menu item height
generate_spacing_live_update( 'menu_item_height', 'menu_item_height', menu_items, 'line-height', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'navigation_logo_height', 'menu_item_height', '.main-navigation .navigation-logo img, .main-navigation .site-logo img', 'height', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'nav_title_height', 'menu_item_height', '.navigation-branding .main-title', 'line-height', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'mobile_header_logo_height', 'menu_item_height', '.mobile-header-navigation .mobile-header-logo img', 'height', false, false, gp_spacing.desktop );

//generate_spacing_live_update( 'tablet_menu_item_height', 'tablet_menu_item_height', menu_items, 'line-height', false, false, gp_spacing.tablet );
//generate_spacing_live_update( 'tablet_navigation_logo_height', 'tablet_menu_item_height', '.main-navigation .navigation-logo img', 'height', false, false, gp_spacing.tablet );
//generate_spacing_live_update( 'tablet_mobile_header_logo_height', 'tablet_menu_item_height', '.mobile-header-navigation .mobile-header-logo img', 'height', false, false, gp_spacing.tablet );

generate_spacing_live_update( 'mobile_menu_item_height', 'mobile_menu_item_height', menu_items, 'line-height', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_navigation_logo_height', 'mobile_menu_item_height', '.main-navigation .site-logo.navigation-logo img, .main-navigation .site-logo img, .main-navigation .navigation-branding img', 'height', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_nav_title_height', 'menu_item_height', '.navigation-branding .main-title', 'line-height', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_mobile_header_logo_height', 'mobile_menu_item_height', '.mobile-header-navigation .site-logo.mobile-header-logo img', 'height', false, false, gp_spacing.mobile );

// Off canvas menu item height
wp.customize( 'generate_spacing_settings[off_canvas_menu_item_height]', function( value ) {
	value.bind( function( newval ) {

		if ( '' == newval ) {
			newval = wp.customize('generate_spacing_settings[menu_item_height]').get();
		}

		jQuery( 'head' ).append( '<style id="off_canvas_menu_item_height">.slideout-navigation.main-navigation .main-nav > ul > li > a{line-height:' + newval + 'px;}</style>' );
		setTimeout(function() {
			jQuery( 'style#off_canvas_menu_item_height' ).not( ':last' ).remove();
		}, 200 );

	} );
} );

/**
 * Main sub-navigation spacing
 */
generate_spacing_live_update( 'sub_menu_item_height_top', 'sub_menu_item_height', '.main-navigation .main-nav ul ul li a', 'padding-top' );
generate_spacing_live_update( 'sub_menu_item_height_right', 'menu_item', '.main-navigation .main-nav ul ul li a', 'padding-right', false, false, gp_spacing.desktop );
//generate_spacing_live_update( 'tablet_sub_menu_item_height_right', 'tablet_menu_item', '.main-navigation .main-nav ul ul li a', 'padding-right', false, false, gp_spacing.tablet );
generate_spacing_live_update( 'sub_menu_item_height_bottom', 'sub_menu_item_height', '.main-navigation .main-nav ul ul li a', 'padding-bottom' );
generate_spacing_live_update( 'sub_menu_item_height_left', 'menu_item', '.main-navigation .main-nav ul ul li a', 'padding-left', false, false, gp_spacing.desktop );
//generate_spacing_live_update( 'tablet_sub_menu_item_height_left', 'tablet_menu_item', '.main-navigation .main-nav ul ul li a', 'padding-left', false, false, gp_spacing.tablet );
generate_spacing_live_update( 'sub_menu_item_offset', 'menu_item_height', '.main-navigation ul ul', 'top' );

/**
 * Main navigation RTL arrow spacing
 */
generate_spacing_live_update( 'dropdown_menu_arrow', 'menu_item', '.menu-item-has-children .dropdown-menu-toggle', 'padding-right', false, false, gp_spacing.desktop );
//generate_spacing_live_update( 'tablet_dropdown_menu_arrow', 'tablet_menu_item', '.menu-item-has-children .dropdown-menu-toggle', 'padding-right', false, false, gp_spacing.tablet );

/**
 * Main sub-navigation arrow spacing
 */
generate_spacing_live_update( 'dropdown_submenu_arrow_top', 'sub_menu_item_height', '.menu-item-has-children ul .dropdown-menu-toggle', 'padding-top' );
generate_spacing_live_update( 'dropdown_submenu_arrow_bottom', 'sub_menu_item_height', '.menu-item-has-children ul .dropdown-menu-toggle', 'padding-bottom' );
generate_spacing_live_update( 'dropdown_submenu_arrow_margin', 'sub_menu_item_height', '.menu-item-has-children ul .dropdown-menu-toggle', 'margin-top', true );

/**
 * Sub-Menu Width
 */
generate_spacing_live_update( 'sub_menu_width', 'sub_menu_width', '.main-navigation ul ul', 'width' );

/**
 - * Sticky menu item height
 - */
wp.customize( 'generate_spacing_settings[sticky_menu_item_height]', function( value ) {
	value.bind( function( newval ) {

		if ( '' == newval ) {
			newval = wp.customize('generate_spacing_settings[menu_item_height]').get();
		}

		jQuery( 'head' ).append( '<style id="sticky_menu_item_height" media="' + gp_spacing.tablet + ',' + gp_spacing.desktop + '">.main-navigation.sticky-navigation-transition .main-nav > ul > li > a,.sticky-navigation-transition .menu-toggle,.main-navigation.sticky-navigation-transition .mobile-bar-items a, .main-navigation.sticky-navigation-transition .menu-bar-item > a, .main-navigation.sticky-navigation-transition .navigation-branding .main-title{line-height:' + newval + 'px;}</style>' );
		jQuery( 'head' ).append( '<style id="sticky_menu_item_logo_height" media="' + gp_spacing.tablet + ',' + gp_spacing.desktop + '">.main-navigation.sticky-navigation-transition .navigation-logo.site-logo img, .main-navigation.sticky-navigation-transition .navigation-branding img{height:' + newval + 'px;}</style>' );
		jQuery( 'head' ).append( '<style id="sticky_menu_item_height_transition">.main-navigation .main-nav ul li a,.menu-toggle,.main-navigation .mobile-bar-items a,.main-navigation .navigation-logo img,.main-navigation .navigation-branding img{transition:0s;}</style>' );
		setTimeout(function() {
			jQuery( 'style#sticky_menu_item_height' ).not( ':last' ).remove();
			jQuery( 'style#sticky_menu_item_logo_height' ).not( ':last' ).remove();
			jQuery( 'style#sticky_menu_item_height_transition' ).remove();
		}, 200 );

	} );
} );

// Disable the transition while we resize
wp.customize( 'generate_spacing_settings[menu_item_height]', function( value ) {
	value.bind( function( newval ) {

		jQuery( 'head' ).append( '<style id="menu_item_height_transition">.main-navigation .main-nav ul li a,.menu-toggle,.main-navigation .mobile-bar-items a,.main-navigation.sticky-navigation-transition .menu-bar-item > a,.main-navigation .navigation-logo img, .main-navigation .navigation-branding > *{transition:0s;}</style>' );
		setTimeout(function() {
			jQuery( 'style#menu_item_height_transition' ).remove();
		}, 200 );

	} );
} );

wp.customize( 'generate_spacing_settings[off_canvas_menu_item_height]', function( value ) {
	value.bind( function( newval ) {

		jQuery( 'head' ).append( '<style id="off_canvas_menu_item_height_transition">.main-navigation .main-nav ul li a,.menu-toggle,.main-navigation .mobile-bar-items a,.main-navigation.sticky-navigation-transition .menu-bar-item > a,.main-navigation .navigation-logo img, .main-navigation .navigation-branding > *{transition:0s;}</style>' );
		setTimeout(function() {
			jQuery( 'style#off_canvas_menu_item_height_transition' ).remove();
		}, 200 );

	} );
} );

/**
 * Widget padding
 */
generate_spacing_live_update( 'widget_top', 'widget_top', '.widget-area .widget, .one-container .widget-area .widget', 'padding-top', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'widget_right', 'widget_right', '.widget-area .widget, .one-container .widget-area .widget', 'padding-right', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'widget_bottom', 'widget_bottom', '.widget-area .widget, .one-container .widget-area .widget', 'padding-bottom', false, false, gp_spacing.desktop );
generate_spacing_live_update( 'widget_left', 'widget_left', '.widget-area .widget, .one-container .widget-area .widget', 'padding-left', false, false, gp_spacing.desktop );

generate_spacing_live_update( 'mobile_widget_top', 'mobile_widget_top', '.widget-area .widget', 'padding-top', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_widget_right', 'mobile_widget_right', '.widget-area .widget', 'padding-right', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_widget_bottom', 'mobile_widget_bottom', '.widget-area .widget', 'padding-bottom', false, false, gp_spacing.mobile );
generate_spacing_live_update( 'mobile_widget_left', 'mobile_widget_left', '.widget-area .widget', 'padding-left', false, false, gp_spacing.mobile );


if ( gp_spacing.isFlex ) {
	/**
	 * Footer widget area
	 */
	generate_spacing_live_update( 'footer_widget_container_top', 'footer_widget_container_top', '.footer-widgets-container', 'padding-top', false, false, gp_spacing.desktop );
	generate_spacing_live_update( 'footer_widget_container_right', 'footer_widget_container_right', '.footer-widgets-container', 'padding-right', false, false, gp_spacing.desktop );
	generate_spacing_live_update( 'footer_widget_container_bottom', 'footer_widget_container_bottom', '.footer-widgets-container', 'padding-bottom', false, false, gp_spacing.desktop );
	generate_spacing_live_update( 'footer_widget_container_left', 'footer_widget_container_left', '.footer-widgets-container', 'padding-left', false, false, gp_spacing.desktop );

	generate_spacing_live_update( 'mobile_footer_widget_container_top', 'mobile_footer_widget_container_top', '.footer-widgets-container', 'padding-top', false, false, gp_spacing.mobile );
	generate_spacing_live_update( 'mobile_footer_widget_container_right', 'mobile_footer_widget_container_right', '.footer-widgets-container', 'padding-right', false, false, gp_spacing.mobile );
	generate_spacing_live_update( 'mobile_footer_widget_container_bottom', 'mobile_footer_widget_container_bottom', '.footer-widgets-container', 'padding-bottom', false, false, gp_spacing.mobile );
	generate_spacing_live_update( 'mobile_footer_widget_container_left', 'mobile_footer_widget_container_left', '.footer-widgets-container', 'padding-left', false, false, gp_spacing.mobile );

	/**
	 * Footer
	 */
	generate_spacing_live_update( 'footer_top', 'footer_top', '.inside-site-info', 'padding-top' );
	generate_spacing_live_update( 'footer_right', 'footer_right', '.inside-site-info', 'padding-right' );
	generate_spacing_live_update( 'footer_bottom', 'footer_bottom', '.inside-site-info', 'padding-bottom' );
	generate_spacing_live_update( 'footer_left', 'footer_left', '.inside-site-info', 'padding-left' );
} else {
	/**
	 * Footer widget area
	 */
	generate_spacing_live_update( 'footer_widget_container_top', 'footer_widget_container_top', '.footer-widgets', 'padding-top', false, false, gp_spacing.desktop );
	generate_spacing_live_update( 'footer_widget_container_right', 'footer_widget_container_right', '.footer-widgets', 'padding-right', false, false, gp_spacing.desktop );
	generate_spacing_live_update( 'footer_widget_container_bottom', 'footer_widget_container_bottom', '.footer-widgets', 'padding-bottom', false, false, gp_spacing.desktop );
	generate_spacing_live_update( 'footer_widget_container_left', 'footer_widget_container_left', '.footer-widgets', 'padding-left', false, false, gp_spacing.desktop );

	generate_spacing_live_update( 'mobile_footer_widget_container_top', 'mobile_footer_widget_container_top', '.footer-widgets', 'padding-top', false, false, gp_spacing.mobile );
	generate_spacing_live_update( 'mobile_footer_widget_container_right', 'mobile_footer_widget_container_right', '.footer-widgets', 'padding-right', false, false, gp_spacing.mobile );
	generate_spacing_live_update( 'mobile_footer_widget_container_bottom', 'mobile_footer_widget_container_bottom', '.footer-widgets', 'padding-bottom', false, false, gp_spacing.mobile );
	generate_spacing_live_update( 'mobile_footer_widget_container_left', 'mobile_footer_widget_container_left', '.footer-widgets', 'padding-left', false, false, gp_spacing.mobile );

	/**
	 * Footer
	 */
	generate_spacing_live_update( 'footer_top', 'footer_top', '.site-info', 'padding-top' );
	generate_spacing_live_update( 'footer_right', 'footer_right', '.site-info', 'padding-right' );
	generate_spacing_live_update( 'footer_bottom', 'footer_bottom', '.site-info', 'padding-bottom' );
	generate_spacing_live_update( 'footer_left', 'footer_left', '.site-info', 'padding-left' );
}


/**
 * Separator
 */

/* Masonry */
if ( jQuery( 'body' ).hasClass( 'masonry-enabled' ) ) {
	generate_spacing_live_update( 'masonry_separator', 'separator', '.masonry-post .inside-article', 'margin-left' );
	generate_spacing_live_update( 'masonry_separator_bottom', 'separator', '.masonry-container > article', 'margin-bottom' );
	generate_spacing_live_update( 'masonry_separator_container', 'separator', '.masonry-container', 'margin-left', 'negative' );
	generate_spacing_live_update( 'masonry_separator_page_header_left', 'separator', '.masonry-enabled .page-header', 'margin-left' );
	generate_spacing_live_update( 'masonry_separator_page_header_bottom', 'separator', '.masonry-enabled .page-header', 'margin-bottom' );
	generate_spacing_live_update( 'masonry_separator_load_more', 'separator', '.separate-containers .site-main > .masonry-load-more', 'margin-bottom' );
}

/* Columns */
if ( jQuery( 'body' ).hasClass( 'generate-columns-activated' ) ) {
	generate_spacing_live_update( 'columns_bottom', 'separator', '.generate-columns', 'margin-bottom' );
	generate_spacing_live_update( 'columns_left', 'separator', '.generate-columns', 'padding-left' );
	generate_spacing_live_update( 'columns_container', 'separator', '.generate-columns-container', 'margin-left', 'negative' );
	generate_spacing_live_update( 'columns_page_header_bottom', 'separator', '.generate-columns-container .page-header', 'margin-bottom' );
	generate_spacing_live_update( 'columns_page_header_left', 'separator', '.generate-columns-container .page-header', 'margin-left' );
	generate_spacing_live_update( 'columns_pagination', 'separator', '.separate-containers .generate-columns-container > .paging-navigation', 'margin-left' );
}

/* Right sidebar */
if ( jQuery( 'body' ).hasClass( 'right-sidebar' ) ) {
	generate_spacing_live_update( 'right_sidebar_sepatator_top', 'separator', '.right-sidebar.separate-containers .site-main', 'margin-top' );
	generate_spacing_live_update( 'right_sidebar_sepatator_right', 'separator', '.right-sidebar.separate-containers .site-main', 'margin-right' );
	generate_spacing_live_update( 'right_sidebar_sepatator_bottom', 'separator', '.right-sidebar.separate-containers .site-main', 'margin-bottom' );
}

/* Left sidebar */
if ( jQuery( 'body' ).hasClass( 'left-sidebar' ) ) {
	generate_spacing_live_update( 'left_sidebar_sepatator_top', 'separator', '.left-sidebar.separate-containers .site-main', 'margin-top' );
	generate_spacing_live_update( 'left_sidebar_sepatator_left', 'separator', '.left-sidebar.separate-containers .site-main', 'margin-left' );
	generate_spacing_live_update( 'left_sidebar_sepatator_bottom', 'separator', '.left-sidebar.separate-containers .site-main', 'margin-bottom' );
}

/* Both sidebars */
if ( jQuery( 'body' ).hasClass( 'both-sidebars' ) ) {
	generate_spacing_live_update( 'both_sidebars_sepatator', 'separator', '.both-sidebars.separate-containers .site-main', 'margin' );
}

/* Both sidebars right */
if ( jQuery( 'body' ).hasClass( 'both-right' ) ) {
	generate_spacing_live_update( 'both_right_sidebar_sepatator_top', 'separator', '.both-right.separate-containers .site-main', 'margin-top' );
	generate_spacing_live_update( 'both_right_sidebar_sepatator_right', 'separator', '.both-right.separate-containers .site-main', 'margin-right' );
	generate_spacing_live_update( 'both_right_sidebar_sepatator_bottom', 'separator', '.both-right.separate-containers .site-main', 'margin-bottom' );

	if ( gp_spacing.isFlex ) {
		generate_spacing_live_update( 'both_right_left_sidebar', 'separator', '.both-right .inside-left-sidebar', 'margin-right', false, true );
		generate_spacing_live_update( 'both_right_right_sidebar', 'separator', '.both-right .inside-right-sidebar', 'margin-left', false, true );
	} else {
		generate_spacing_live_update( 'both_right_left_sidebar', 'separator', '.both-right.separate-containers .inside-left-sidebar', 'margin-right', false, true );
		generate_spacing_live_update( 'both_right_right_sidebar', 'separator', '.both-right.separate-containers .inside-right-sidebar', 'margin-left', false, true );
	}
}

/* Both sidebars left */
if ( jQuery( 'body' ).hasClass( 'both-left' ) ) {
	generate_spacing_live_update( 'both_left_sidebar_sepatator_top', 'separator', '.both-left.separate-containers .site-main', 'margin-top' );
	generate_spacing_live_update( 'both_left_sidebar_sepatator_right', 'separator', '.both-left.separate-containers .site-main', 'margin-bottom' );
	generate_spacing_live_update( 'both_left_sidebar_sepatator_bottom', 'separator', '.both-left.separate-containers .site-main', 'margin-left' );

	if ( gp_spacing.isFlex ) {
		generate_spacing_live_update( 'both_left_left_sidebar', 'separator', '.both-left .inside-left-sidebar', 'margin-right', false, true );
		generate_spacing_live_update( 'both_left_right_sidebar', 'separator', '.both-left .inside-right-sidebar', 'margin-left', false, true );
	} else {
		generate_spacing_live_update( 'both_left_left_sidebar', 'separator', '.both-left.separate-containers .inside-left-sidebar', 'margin-right', false, true );
		generate_spacing_live_update( 'both_left_right_sidebar', 'separator', '.both-left.separate-containers .inside-right-sidebar', 'margin-left', false, true );
	}
}

/* Main element margin */
generate_spacing_live_update( 'site_main_separator_top', 'separator', '.separate-containers .site-main', 'margin-top' );
generate_spacing_live_update( 'site_main_separator_bottom', 'separator', '.separate-containers .site-main', 'margin-bottom' );

/* Page header element */
if ( gp_spacing.isFlex ) {
	generate_spacing_live_update( 'page_header_separator_top', 'separator', '.separate-containers .featured-image', 'margin-top' );
} else {
	generate_spacing_live_update( 'page_header_separator_top', 'separator',
		'.separate-containers .page-header-image, \
		.separate-containers .page-header-contained, \
		.separate-containers .page-header-image-single, \
		.separate-containers .page-header-content-single', 'margin-top' );
}

/* Top and bottom sidebar margin */
generate_spacing_live_update( 'right_sidebar_separator_top', 'separator', '.separate-containers .inside-right-sidebar, .separate-containers .inside-left-sidebar', 'margin-top' );
generate_spacing_live_update( 'right_sidebar_separator_bottom', 'separator', '.separate-containers .inside-right-sidebar, .separate-containers .inside-left-sidebar', 'margin-bottom' );

/* Element separators */
if ( gp_spacing.isFlex ) {
	generate_spacing_live_update( 'content_separator', 'separator',
		'.sidebar .widget, \
		.site-main > *, \
		.page-header, \
		.widget-area .main-navigation', 'margin-bottom' );
} else {
	generate_spacing_live_update( 'content_separator', 'separator',
		'.separate-containers .widget, \
		.separate-containers .site-main > *, \
		.separate-containers .page-header, \
		.widget-area .main-navigation', 'margin-bottom' );
}

/**
 * Right sidebar width
 */
wp.customize( 'generate_spacing_settings[right_sidebar_width]', function( value ) {
	value.bind( function( newval ) {
		var body = jQuery( 'body' );

		if ( jQuery( '#right-sidebar' ).length ) {
			if ( gp_spacing.isFlex ) {
				var contentWidth = 100,
					leftSidebar = jQuery( '#left-sidebar' ).length ? wp.customize.value('generate_spacing_settings[left_sidebar_width]')() : 0;

				if ( body.hasClass( 'right-sidebar' ) ) {
					contentWidth = ( Number( contentWidth ) - Number( newval ) );
				} else if ( ! body.hasClass( 'left-sidebar' ) && ! body.hasClass( 'no-sidebar' ) ) {
					var totalSidebarWidth = ( Number( leftSidebar ) + Number( newval ) );

					contentWidth = ( Number( contentWidth ) - Number( totalSidebarWidth ) );
				}

				jQuery( 'head' ).append( '<style id="right_sidebar_width">.is-right-sidebar{width:' + newval + '%;}.site-content .content-area{width:' + contentWidth + '%;}</style>' );

				setTimeout(function() {
					jQuery( 'style#right_sidebar_width' ).not( ':last' ).remove();
				}, 200 );
			} else {
				// Left sidebar width
				var left_sidebar = ( jQuery( '#left-sidebar' ).length ) ? wp.customize.value('generate_spacing_settings[left_sidebar_width]')() : 0;

				// Right sidebar class
				jQuery( "#right-sidebar" ).removeClass(function (index, css) {
					return (css.match (/(^|\s)grid-\S+/g) || []).join(' ');
				}).removeClass(function (index, css) {
					return (css.match (/(^|\s)tablet-grid-\S+/g) || []).join(' ');
				}).addClass( 'grid-' + newval ).addClass( 'tablet-grid-' + newval ).addClass( 'grid-parent' );

				// Content area class
				jQuery( ".content-area" ).removeClass(function (index, css) {
					return (css.match (/(^|\s)grid-\S+/g) || []).join(' ');
				}).removeClass(function (index, css) {
					return (css.match (/(^|\s)tablet-grid-\S+/g) || []).join(' ');
				}).addClass( 'grid-' + ( 100 - newval - left_sidebar ) ).addClass( 'tablet-grid-' + ( 100 - newval - left_sidebar ) ).addClass( 'grid-parent' );

				if ( body.hasClass( 'both-sidebars' ) ) {
					var content_width = ( 100 - newval - left_sidebar );
					jQuery( '#left-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( content_width ) ).addClass( 'tablet-pull-' + ( content_width ) );
				}

				if ( body.hasClass( 'both-left' ) ) {
					var total_sidebar_width = ( parseInt( left_sidebar ) + parseInt( newval ) );

					jQuery( '#right-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( 100 - total_sidebar_width ) ).addClass( 'tablet-pull-' + ( 100 - total_sidebar_width ) );

					jQuery( '#left-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( 100 - total_sidebar_width ) ).addClass( 'tablet-pull-' + ( 100 - total_sidebar_width ) );

					jQuery( '.content-area' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'push-' + ( total_sidebar_width ) ).addClass( 'tablet-push-' + ( total_sidebar_width ) );
				}
			}
			jQuery('body').trigger('generate_spacing_updated');
		}
	} );
} );

/**
 * Left sidebar width
 */
wp.customize( 'generate_spacing_settings[left_sidebar_width]', function( value ) {
	value.bind( function( newval ) {
		var body = jQuery( 'body' );
		if ( jQuery( '#left-sidebar' ).length ) {
			if ( gp_spacing.isFlex ) {
				var contentWidth = 100,
					rightSidebar = jQuery( '#right-sidebar' ).length ? wp.customize.value('generate_spacing_settings[right_sidebar_width]')() : 0;

				if ( body.hasClass( 'left-sidebar' ) ) {
					contentWidth = ( Number( contentWidth ) - Number( newval ) );
				} else if ( ! body.hasClass( 'right-sidebar' ) && ! body.hasClass( 'no-sidebar' ) ) {
					var totalSidebarWidth = ( Number( rightSidebar ) + Number( newval ) );

					contentWidth = ( Number( contentWidth ) - Number( totalSidebarWidth ) );
				}

				jQuery( 'head' ).append( '<style id="left_sidebar_width">.is-left-sidebar{width:' + newval + '%;}.site-content .content-area{width:' + contentWidth + '%;}</style>' );

				setTimeout(function() {
					jQuery( 'style#left_sidebar_width' ).not( ':last' ).remove();
				}, 200 );
			} else {
				// Right sidebar width
				var right_sidebar = ( jQuery( '#right-sidebar' ).length ) ? wp.customize.value('generate_spacing_settings[right_sidebar_width]')() : 0;

				// Right sidebar class
				jQuery( "#left-sidebar" ).removeClass(function (index, css) {
					return (css.match (/(^|\s)grid-\S+/g) || []).join(' ');
				}).removeClass(function (index, css) {
					return (css.match (/(^|\s)tablet-grid-\S+/g) || []).join(' ');
				}).addClass( 'grid-' + newval ).addClass( 'tablet-grid-' + newval ).addClass( 'grid-parent' );

				// Content area class
				jQuery( ".content-area" ).removeClass(function (index, css) {
					return (css.match (/(^|\s)grid-\S+/g) || []).join(' ');
				}).removeClass(function (index, css) {
					return (css.match (/(^|\s)tablet-grid-\S+/g) || []).join(' ');
				}).addClass( 'grid-' + ( 100 - newval - right_sidebar ) ).addClass( 'tablet-grid-' + ( 100 - newval - right_sidebar ) ).addClass( 'grid-parent' );

				if ( body.hasClass( 'left-sidebar' ) ) {
					jQuery( '#left-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( 100 - newval ) ).addClass( 'tablet-pull-' + ( 100 - newval ) );

					jQuery( '.content-area' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'push-' + newval ).addClass( 'tablet-push-' + newval ).addClass( 'grid-' + ( 100 - newval ) ).addClass( 'tablet-grid-' + ( 100 - newval ) );
				}

				if ( body.hasClass( 'both-sidebars' ) ) {
					var content_width = ( 100 - newval - right_sidebar );
					jQuery( '#left-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( content_width ) ).addClass( 'tablet-pull-' + ( content_width ) );

					jQuery( '.content-area' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'push-' + ( newval ) ).addClass( 'tablet-push-' + ( newval ) );
				}

				if ( body.hasClass( 'both-left' ) ) {
					var content_width = ( 100 - newval - right_sidebar );
					var total_sidebar_width = ( parseInt( right_sidebar ) + parseInt( newval ) );

					jQuery( '#right-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( 100 - total_sidebar_width ) ).addClass( 'tablet-pull-' + ( 100 - total_sidebar_width ) );

					jQuery( '#left-sidebar' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).addClass( 'pull-' + ( 100 - total_sidebar_width ) ).addClass( 'tablet-pull-' + ( 100 - total_sidebar_width ) );

					jQuery( '.content-area' ).removeClass(function (index, css) {
						return (css.match (/(^|\s)pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-pull-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)push-\S+/g) || []).join(' ');
					}).removeClass(function (index, css) {
						return (css.match (/(^|\s)tablet-push-\S+/g) || []).join(' ');
					}).addClass( 'push-' + ( total_sidebar_width ) ).addClass( 'tablet-push-' + ( total_sidebar_width ) );
				}
			}
			jQuery('body').trigger('generate_spacing_updated');
		}
	} );
} );
