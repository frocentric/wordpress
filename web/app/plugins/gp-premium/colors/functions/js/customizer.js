/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
function generate_colors_live_update( id, selector, property, default_value, get_value, settings ) {
	default_value = typeof default_value !== 'undefined' ? default_value : 'initial';
	get_value = typeof get_value !== 'undefined' ? get_value : '';
	settings = typeof settings !== 'undefined' ? settings : 'generate_settings';
	wp.customize( settings + '[' + id + ']', function( value ) {
		value.bind( function( newval ) {

			// Stop the header link color from applying to the site title.
			if ( 'header_link_color' === id || 'header_link_color' === id ) {
				jQuery( '.site-header a' ).addClass( 'header-link' );
				jQuery( '.site-header .main-title a' ).removeClass( 'header-link' );
			}

			if ( 'content_link_color' === id || 'content_link_color_hover' === id || 'entry_meta_link_color' === id || 'blog_post_title_color' === id ) {
				var content_link = jQuery( '.inside-article a' );
				var meta = jQuery( '.entry-meta a' );
				var title = jQuery( '.entry-title a' );

				content_link.attr( 'data-content-link-color', true );

				if ( '' !== wp.customize.value('generate_settings[entry_meta_link_color]')() ) {
					meta.attr( 'data-content-link-color', '' );
				} else {
					meta.attr( 'data-content-link-color', true );
				}

				if ( '' !== wp.customize.value('generate_settings[blog_post_title_color]')() ) {
					title.attr( 'data-content-link-color', '' );
				} else {
					title.attr( 'data-content-link-color', true );
				}
			}

			default_value = ( '' !== get_value ) ? wp.customize.value('generate_settings[' + get_value + ']')() : default_value;
			newval = ( '' !== newval ) ? newval : default_value;
			var unique_id = ( 'generate_secondary_nav_settings' == settings ) ? 'secondary_' : '';
			if ( jQuery( 'style#' + unique_id + id ).length ) {
				jQuery( 'style#' + unique_id + id ).html( selector + '{' + property + ':' + newval + ';}' );
			} else {
				jQuery( 'head' ).append( '<style id="' + unique_id + id + '">' + selector + '{' + property + ':' + newval + '}</style>' );
				setTimeout(function() {
					jQuery( 'style#' + id ).not( ':last' ).remove();
				}, 1000);
			}

		} );
	} );
}

/**
 * Header background color
 * Empty: transparent
 */
generate_colors_live_update( 'top_bar_background_color', '.top-bar', 'background-color', 'transparent' );

/**
 * Header text color
 * Empty: text_color
 */
generate_colors_live_update( 'top_bar_text_color', '.top-bar', 'color', '', 'text_color' );

/**
 * Header link color
 * Empty: link_color
 */
generate_colors_live_update( 'top_bar_link_color', '.top-bar a, .top-bar a:visited', 'color', '', 'link_color' );

/**
 * Header link color hover
 * Empty: link_color_hover
 */
generate_colors_live_update( 'top_bar_link_color_hover', '.top-bar a:hover', 'color', '', 'link_color_hover' );


/**
 * Header background color
 * Empty:  transparent
 */
generate_colors_live_update( 'header_background_color', '.site-header', 'background-color', 'transparent' );

/**
 * Header text color
 * Empty:  text_color
 */
generate_colors_live_update( 'header_text_color', '.site-header', 'color', '', 'text_color' );

/**
 * Header link color
 * Empty:  link_color
 */
generate_colors_live_update( 'header_link_color', '.site-header a.header-link, .site-header a.header-link:visited', 'color', '', 'link_color' );

/**
 * Header link color hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'header_link_hover_color', '.site-header a.header-link:hover', 'color', '', 'link_color_hover' );

/**
 * Site title color
 * Empty:  link_color
 */
generate_colors_live_update( 'site_title_color', '.main-title a,.main-title a:hover,.main-title a:visited,.header-wrap .navigation-stick .main-title a, .header-wrap .navigation-stick .main-title a:hover, .header-wrap .navigation-stick .main-title a:visited', 'color', '', 'link_color' );

/**
 * Site tagline color
 * Empty:  text_color
 */
generate_colors_live_update( 'site_tagline_color', '.site-description', 'color', '', 'text_color' );

/**
 * Main navigation background
 * Empty:  transparent
 */
generate_colors_live_update( 'navigation_background_color', '.main-navigation', 'background-color', 'transparent' );

/**
 * Primary navigation text color
 * Empty:  link_color
 */
generate_colors_live_update( 'navigation_text_color',
	'.main-navigation .main-nav ul li a,\
	.menu-toggle,button.menu-toggle:hover,\
	button.menu-toggle:focus,\
	.main-navigation .mobile-bar-items a,\
	.main-navigation .mobile-bar-items a:hover,\
	.main-navigation .mobile-bar-items a:focus,\
	.main-navigation .menu-bar-items',
		'color', '', 'link_color'
);

/**
 * Primary navigation text color hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'navigation_text_hover_color',
	'.navigation-search input[type="search"],\
	.navigation-search input[type="search"]:active,\
	.navigation-search input[type="search"]:focus,\
	.main-navigation .main-nav ul li:hover > a,\
	.main-navigation .main-nav ul li:focus > a,\
	.main-navigation .main-nav ul li.sfHover > a,\
	.main-navigation .menu-bar-item:hover a',
		'color', '', 'link_color_hover'
);

/**
 * Primary navigation menu item hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'navigation_background_hover_color',
	'.navigation-search input[type="search"],\
	.navigation-search input[type="search"]:focus,\
	.main-navigation .main-nav ul li:hover > a,\
	.main-navigation .main-nav ul li:focus > a,\
	.main-navigation .main-nav ul li.sfHover > a,\
	.main-navigation .menu-bar-item:hover a',
		'background-color', 'transparent'
);

/**
 * Primary sub-navigation color
 * Empty:  transparent
 */
generate_colors_live_update( 'subnavigation_background_color', '.main-navigation ul ul', 'background-color', 'transparent' );

/**
 * Primary sub-navigation text color
 * Empty:  link_color
 */
generate_colors_live_update( 'subnavigation_text_color', '.main-navigation .main-nav ul ul li a', 'color', 'link_color' );

/**
 * Primary sub-navigation hover
 */
var subnavigation_hover = '.main-navigation .main-nav ul ul li:hover > a, \
	.main-navigation .main-nav ul ul li:focus > a, \
	.main-navigation .main-nav ul ul li.sfHover > a';

/**
 * Primary sub-navigation text hover
 * Empty: link_color_hover
 */
generate_colors_live_update( 'subnavigation_text_hover_color', subnavigation_hover, 'color', '', 'link_color_hover' );

/**
 * Primary sub-navigation background hover
 * Empty: transparent
 */
generate_colors_live_update( 'subnavigation_background_hover_color', subnavigation_hover, 'background-color', 'transparent' );

/**
 * Navigation current selectors
 */
var navigation_current = '.main-navigation .main-nav ul li[class*="current-menu-"] > a, \
	.main-navigation .main-nav ul li[class*="current-menu-"]:hover > a, \
	.main-navigation .main-nav ul li[class*="current-menu-"].sfHover > a';

/**
 * Primary navigation current text
 * Empty: link_color
 */
generate_colors_live_update( 'navigation_text_current_color', navigation_current, 'color', '', 'link_color' );

/**
 * Primary navigation current text
 * Empty: transparent
 */
generate_colors_live_update( 'navigation_background_current_color', navigation_current, 'background-color', 'transparent' );

/**
 * Primary sub-navigation current selectors
 */
var subnavigation_current = '.main-navigation .main-nav ul ul li[class*="current-menu-"] > a,\
	.main-navigation .main-nav ul ul li[class*="current-menu-"]:hover > a, \
	.main-navigation .main-nav ul ul li[class*="current-menu-"].sfHover > a';

/**
 * Primary sub-navigation current text
 * Empty: link_color
 */
generate_colors_live_update( 'subnavigation_text_current_color', subnavigation_current, 'color', '', 'link_color' );

/**
 * Primary navigation current item background
 * Empty: transparent
 */
generate_colors_live_update( 'subnavigation_background_current_color', subnavigation_current, 'background-color', 'transparent' );

/**
 * Secondary navigation background
 * Empty:  transparent
 */
generate_colors_live_update( 'navigation_background_color', '.secondary-navigation', 'background-color', 'transparent', '', 'generate_secondary_nav_settings' );

/**
 * Secondary navigation text color
 * Empty:  link_color
 */
generate_colors_live_update( 'navigation_text_color',
	'.secondary-navigation .main-nav ul li a,\
	.secondary-navigation .menu-toggle,\
	button.secondary-menu-toggle:hover,\
	button.secondary-menu-toggle:focus, \
	.secondary-navigation .top-bar, \
	.secondary-navigation .top-bar a,\
	.secondary-menu-bar-items,\
	.secondary-menu-bar-items .menu-bar-item > a',
		'color', '', 'link_color', 'generate_secondary_nav_settings'
);

/**
 * Navigation search
 */
wp.customize( 'generate_settings[navigation_search_background_color]', function( value ) {
	value.bind( function( newval ) {
		if ( jQuery( 'style#navigation_search_background_color' ).length ) {
			jQuery( 'style#navigation_search_background_color' ).html( '.navigation-search input[type="search"],.navigation-search input[type="search"]:active, .navigation-search input[type="search"]:focus, .main-navigation .main-nav ul li.search-item.active > a, .main-navigation .menu-bar-items .search-item.active > a{background-color:' + newval + ';}' );
		} else {
			jQuery( 'head' ).append( '<style id="navigation_search_background_color">.navigation-search input[type="search"],.navigation-search input[type="search"]:active, .navigation-search input[type="search"]:focus, .main-navigation .main-nav ul li.search-item.active > a, .main-navigation .menu-bar-items .search-item.active > a{background-color:' + newval + ';}</style>' );
			setTimeout(function() {
				jQuery( 'style#navigation_search_background_color' ).not( ':last' ).remove();
			}, 1000);
		}

	if ( jQuery( 'style#navigation_search_background_opacity' ).length ) {
		if ( newval ) {
			jQuery( 'style#navigation_search_background_opacity' ).html( '.navigation-search input{opacity: 1;}' );
		} else {
			jQuery( 'style#navigation_search_background_opacity' ).html( '.navigation-search input{opacity: 0.9;}' );
		}
	} else {
			if ( newval ) {
				jQuery( 'head' ).append( '<style id="navigation_search_background_opacity">.navigation-search input{opacity: 1;}</style>' );
			}

			setTimeout(function() {
				jQuery( 'style#navigation_search_background_opacity' ).not( ':last' ).remove();
			}, 1000);
		}
	} );
} );

generate_colors_live_update( 'navigation_search_text_color', '.navigation-search input[type="search"],.navigation-search input[type="search"]:active, .navigation-search input[type="search"]:focus, .main-navigation .main-nav ul li.search-item.active > a, .main-navigation .menu-bar-items .search-item.active > a', 'color', '' );

/**
 * Secondary navigation text color hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'navigation_text_hover_color',
	'.secondary-navigation .main-nav ul li:hover > a, \
	.secondary-navigation .main-nav ul li:focus > a, \
	.secondary-navigation .main-nav ul li.sfHover > a,\
	.secondary-menu-bar-items .menu-bar-item:hover > a',
		'color', '', 'link_color_hover', 'generate_secondary_nav_settings'
);

/**
 * Secondary navigation menu item hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'navigation_background_hover_color',
	'.secondary-navigation .main-nav ul li:hover > a, \
	.secondary-navigation .main-nav ul li:focus > a, \
	.secondary-navigation .main-nav ul li.sfHover > a, \
	.secondary-menu-bar-items .menu-bar-item:hover > a',
		'background-color', 'transparent', '', 'generate_secondary_nav_settings'
);

/**
 * Secondary navigation top bar link hover
 */
wp.customize( 'generate_secondary_nav_settings[navigation_background_hover_color]', function( value ) {
	value.bind( function( newval ) {
		if ( jQuery( 'style#secondary_nav_top_bar_hover' ).length ) {
			jQuery( 'style#secondary_nav_top_bar_hover' ).html( '.secondary-navigation .top-bar a:hover,.secondary-navigation .top-bar a:focus{color:' + newval + ';}' );
		} else {
			jQuery( 'head' ).append( '<style id="secondary_nav_top_bar_hover">.secondary-navigation .top-bar a:hover,.secondary-navigation .top-bar a:focus{color:' + newval + ';}</style>' );
			setTimeout(function() {
				jQuery( 'style#secondary_nav_top_bar_hover' ).not( ':last' ).remove();
			}, 1000);
		}
	} );
} );

generate_colors_live_update( 'navigation_top_bar_hover_color',
	'.secondary-navigation .top-bar a:hover, \
	.secondary-navigation .top-bar a:focus',
		'color', 'transparent', '', 'generate_secondary_nav_settings'
);

/**
 * Secondary sub-navigation color
 * Empty:  transparent
 */
generate_colors_live_update( 'subnavigation_background_color', '.secondary-navigation ul ul', 'background-color', 'transparent', '', 'generate_secondary_nav_settings' );

/**
 * Secondary sub-navigation text color
 * Empty:  link_color
 */
generate_colors_live_update( 'subnavigation_text_color', '.secondary-navigation .main-nav ul ul li a', 'color', '', 'link_color', 'generate_secondary_nav_settings' );

/**
 * Secondary sub-navigation hover
 */
var secondary_subnavigation_hover = '.secondary-navigation .main-nav ul ul li > a:hover, \
	.secondary-navigation .main-nav ul ul li:focus > a, \
	.secondary-navigation .main-nav ul ul li.sfHover > a';

/**
 * Secondary sub-navigation text hover
 * Empty: link_color_hover
 */
generate_colors_live_update( 'subnavigation_text_hover_color', secondary_subnavigation_hover, 'color', '', 'link_color_hover', 'generate_secondary_nav_settings' );

/**
 * Secondary sub-navigation background hover
 * Empty: transparent
 */
generate_colors_live_update( 'subnavigation_background_hover_color', secondary_subnavigation_hover, 'background-color', 'transparent', '', 'generate_secondary_nav_settings' );

/**
 * Secondary navigation current selectors
 */
var secondary_navigation_current = '.secondary-navigation .main-nav ul li[class*="current-menu-"] > a, \
	.secondary-navigation .main-nav ul li[class*="current-menu-"]:hover > a, \
	.secondary-navigation .main-nav ul li[class*="current-menu-"].sfHover > a';

/**
 * Secondary navigation current text
 * Empty: link_color
 */
generate_colors_live_update( 'navigation_text_current_color', secondary_navigation_current, 'color', '', 'link_color', 'generate_secondary_nav_settings' );

/**
 * Secondary navigation current text
 * Empty: transparent
 */
generate_colors_live_update( 'navigation_background_current_color', secondary_navigation_current, 'background-color', 'transparent', '', 'generate_secondary_nav_settings' );

/**
 * Secondary sub-navigation current selectors
 */
var secondary_subnavigation_current = '.secondary-navigation .main-nav ul ul li[class*="current-menu-"] > a,\
	.secondary-navigation .main-nav ul ul li[class*="current-menu-"]:hover > a, \
	.secondary-navigation .main-nav ul ul li[class*="current-menu-"].sfHover > a';

/**
 * Secondary sub-navigation current text
 * Empty: link_color
 */
generate_colors_live_update( 'subnavigation_text_current_color', secondary_subnavigation_current, 'color', '', 'link_color', 'generate_secondary_nav_settings' );

/**
 * Primary navigation current item background
 * Empty: transparent
 */
generate_colors_live_update( 'subnavigation_background_current_color', secondary_subnavigation_current, 'background-color', 'transparent', '', 'generate_secondary_nav_settings' );

/**
 * Content selectors
 */
var content = '.separate-containers .inside-article,\
	.separate-containers .comments-area,\
	.separate-containers .page-header,\
	.one-container .container,\
	.separate-containers .paging-navigation,\
	.inside-page-header';

/**
 * Content background
 * Empty: transparent
 */
generate_colors_live_update( 'content_background_color', content, 'background-color', 'transparent' );

/**
 * Content text color
 * Empty: text_color
 */
generate_colors_live_update( 'content_text_color', content, 'color', '', 'text_color' );

/**
 * Content links
 * Empty: link_color
 */
generate_colors_live_update( 'content_link_color',
	'.inside-article a:not(.button):not(.wp-block-button__link)[data-content-link-color=true], \
	.inside-article a:not(.button):not(.wp-block-button__link)[data-content-link-color=true]:visited,\
	.paging-navigation a,\
	.paging-navigation a:visited,\
	.comments-area a,\
	.comments-area a:visited,\
	.page-header a,\
	.page-header a:visited',
		'color', '', 'link_color'
);

/**
 * Content links on hover
 * Empty: link_color_hover
 */
generate_colors_live_update( 'content_link_hover_color',
	'.inside-article a:not(.button):not(.wp-block-button__link)[data-content-link-color=true]:hover,\
	.paging-navigation a:hover,\
	.comments-area a:hover,\
	.page-header a:hover',
		'color', '', 'link_color_hover'
);

generate_colors_live_update( 'content_title_color', '.entry-header h1,.page-header h1', 'color', 'inherit', 'text_color' );
generate_colors_live_update( 'blog_post_title_color', '.entry-title a,.entry-title a:visited', 'color', '', 'link_color' );
generate_colors_live_update( 'blog_post_title_hover_color', '.entry-title a:hover', 'color', '', 'link_color_hover' );
generate_colors_live_update( 'entry_meta_text_color', '.entry-meta', 'color', '', 'text_color' );
generate_colors_live_update( 'entry_meta_link_color', '.entry-meta a, .entry-meta a:visited', 'color', '', 'link_color' );
generate_colors_live_update( 'entry_meta_link_color_hover', '.entry-meta a:hover', 'color', '', 'link_color_hover' );
generate_colors_live_update( 'h1_color', 'h1', 'color', '', 'text_color' );
generate_colors_live_update( 'h2_color', 'h2', 'color', '', 'text_color' );
generate_colors_live_update( 'h3_color', 'h3', 'color', '', 'text_color' );
generate_colors_live_update( 'h4_color', 'h4', 'color', '', 'text_color' );
generate_colors_live_update( 'h5_color', 'h5', 'color', '', 'text_color' );
generate_colors_live_update( 'sidebar_widget_background_color', '.sidebar .widget', 'background-color', 'transparent' );
generate_colors_live_update( 'sidebar_widget_text_color', '.sidebar .widget', 'color', '', 'text_color' );
generate_colors_live_update( 'sidebar_widget_link_color', '.sidebar .widget a, .sidebar .widget a:visited', 'color', '', 'link_color' );
generate_colors_live_update( 'sidebar_widget_link_hover_color', '.sidebar .widget a:hover', 'color', '', 'link_color_hover' );
generate_colors_live_update( 'sidebar_widget_title_color', '.sidebar .widget .widget-title', 'color', '', 'text_color' );
generate_colors_live_update( 'footer_widget_background_color', '.footer-widgets', 'background-color', 'transparent' );
generate_colors_live_update( 'footer_widget_text_color', '.footer-widgets', 'color', '', 'text_color' );
generate_colors_live_update( 'footer_widget_link_color', '.footer-widgets a, .footer-widgets a:visited', 'color', '', 'link_color' );
generate_colors_live_update( 'footer_widget_link_hover_color', '.footer-widgets a:hover', 'color', '', 'link_color_hover' );
generate_colors_live_update( 'footer_widget_title_color', '.footer-widgets .widget-title', 'color', '', 'text_color' );
generate_colors_live_update( 'footer_background_color', '.site-info', 'background-color', 'transparent' );
generate_colors_live_update( 'footer_text_color', '.site-info', 'color', '', 'text_color' );
generate_colors_live_update( 'footer_link_color', '.site-info a, .site-info a:visited', 'color', '', 'link_color' );
generate_colors_live_update( 'footer_link_hover_color', '.site-info a:hover', 'color', '', 'link_color_hover' );

/**
 * Form selectors
 */
var forms = 'input[type="text"], \
	input[type="email"], \
	input[type="url"], \
	input[type="password"], \
	input[type="search"], \
	input[type="number"], \
	input[type="tel"], \
	textarea, \
	select';

/**
 * Form background
 * Empty: inherit
 */
generate_colors_live_update( 'form_background_color', forms, 'background-color', 'inherit' );

/**
 * Border color
 * Empty: inherit
 */
generate_colors_live_update( 'form_border_color', forms, 'border-color' );

/**
 * Form text color
 * Empty: text_color
 */
generate_colors_live_update( 'form_text_color', forms, 'color', '', 'text_color' );

/**
 * Form background on focus selectors
 * Empty: inherit
 */
var forms_focus = 'input[type="text"]:focus, \
	input[type="email"]:focus, \
	input[type="url"]:focus, \
	input[type="password"]:focus, \
	input[type="search"]:focus,\
	input[type="number"]:focus,\
	input[type="tel"]:focus, \
	textarea:focus, \
	select:focus';

/**
 * Form background color on focus
 * Empty: initial
 */
generate_colors_live_update( 'form_background_color_focus', forms_focus, 'background-color' );

/**
 * Form text color on focus
 * Empty: initial
 */
generate_colors_live_update( 'form_text_color_focus', forms_focus, 'color' );

/**
 * Form border color on focus
 * Empty: initial
 */
generate_colors_live_update( 'form_border_color_focus', forms_focus, 'border-color' );

/**
 * Button selectors
 */
var button = 'button, \
	html input[type="button"], \
	input[type="reset"], \
	input[type="submit"],\
	a.button,\
	a.button:visited,\
	a.wp-block-button__link:not(.has-background)';

/**
 * Button background
 * Empty: initial
 */
generate_colors_live_update( 'form_button_background_color', button, 'background-color' );

/**
 * Button text
 * Empty: initial
 */
generate_colors_live_update( 'form_button_text_color', button, 'color' );

/**
 * Button on hover/focus selectors
 * Empty: initial
 */
var button_hover = 'button:hover, \
	html input[type="button"]:hover, \
	input[type="reset"]:hover, \
	input[type="submit"]:hover,\
	a.button:hover,\
	button:focus, \
	html input[type="button"]:focus, \
	input[type="reset"]:focus, \
	input[type="submit"]:focus,\
	a.button:focus,\
	a.wp-block-button__link:not(.has-background):active,\
	a.wp-block-button__link:not(.has-background):focus,\
	a.wp-block-button__link:not(.has-background):hover';

/**
 * Button color on hover
 * Empty: initial
 */
generate_colors_live_update( 'form_button_background_color_hover', button_hover, 'background-color' );

/**
 * Button text color on hover
 * Empty: initial
 */
generate_colors_live_update( 'form_button_text_color_hover', button_hover, 'color' );

/**
 * Back to top background color
 * Empty: transparent
 */
generate_colors_live_update( 'back_to_top_background_color', 'a.generate-back-to-top', 'background-color', 'transparent' );

/**
 * Back to top text color
 * Empty: text_color
 */
generate_colors_live_update( 'back_to_top_text_color', 'a.generate-back-to-top', 'color', '', 'text_color' );

/**
 * Back to top background color hover
 * Empty: transparent
 */
generate_colors_live_update( 'back_to_top_background_color_hover', 'a.generate-back-to-top:hover,a.generate-back-to-top:focus', 'background-color', 'transparent' );

/**
 * Back to top text color hover
 * Empty: text_color
 */
generate_colors_live_update( 'back_to_top_text_color_hover', 'a.generate-back-to-top:hover,a.generate-back-to-top:focus', 'color', '', 'text_color' );
