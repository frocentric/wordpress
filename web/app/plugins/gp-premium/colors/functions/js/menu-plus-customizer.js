/**
 * Main navigation background
 * Empty:  transparent
 */
generate_colors_live_update( 'slideout_background_color', '.main-navigation.slideout-navigation', 'background-color', '' );

/**
 * Primary navigation text color
 * Empty:  link_color
 */
generate_colors_live_update( 'slideout_text_color', '.slideout-navigation.main-navigation .main-nav ul li a, .slideout-navigation a, .slideout-navigation', 'color', '' );

/**
 * Primary navigation text color hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'slideout_text_hover_color',
	'.slideout-navigation.main-navigation .main-nav ul li:hover > a,\
	.slideout-navigation.main-navigation .main-nav ul li:focus > a,\
	.slideout-navigation.main-navigation .main-nav ul li.sfHover > a',
		'color', ''
);

/**
 * Primary navigation menu item hover
 * Empty:  link_color_hover
 */
generate_colors_live_update( 'slideout_background_hover_color',
	'.slideout-navigation.main-navigation .main-nav ul li:hover > a,\
	.slideout-navigation.main-navigation .main-nav ul li:focus > a,\
	.slideout-navigation.main-navigation .main-nav ul li.sfHover > a',
		'background-color', 'transparent'
);

/**
 * Primary sub-navigation color
 * Empty:  transparent
 */
generate_colors_live_update( 'slideout_submenu_background_color', '.slideout-navigation.main-navigation ul ul', 'background-color', '' );

/**
 * Primary sub-navigation text color
 * Empty:  link_color
 */
generate_colors_live_update( 'slideout_submenu_text_color', '.slideout-navigation.main-navigation .main-nav ul ul li a', 'color', '' );

/**
 * Primary sub-navigation hover
 */
var slideout_submenu_hover = '.slideout-navigation.main-navigation .main-nav ul ul li:hover > a,\
	.slideout-navigation.main-navigation .main-nav ul ul li:focus > a,\
	.slideout-navigation.main-navigation .main-nav ul ul li.sfHover > a';

/**
 * Primary sub-navigation text hover
 * Empty: link_color_hover
 */
generate_colors_live_update( 'slideout_submenu_text_hover_color', slideout_submenu_hover, 'color', '' );

/**
 * Primary sub-navigation background hover
 * Empty: transparent
 */
generate_colors_live_update( 'slideout_submenu_background_hover_color', slideout_submenu_hover, 'background-color', '' );

/**
 * Navigation current selectors
 */
var slideout_current = '.slideout-navigation.main-navigation .main-nav ul li[class*="current-menu-"] > a,\
	.slideout-navigation.main-navigation .main-nav ul li[class*="current-menu-"] > a:hover,\
	.slideout-navigation.main-navigation .main-nav ul li[class*="current-menu-"].sfHover > a';

/**
 * Primary navigation current text
 * Empty: link_color
 */
generate_colors_live_update( 'slideout_text_current_color', slideout_current, 'color', '' );

/**
 * Primary navigation current text
 * Empty: transparent
 */
generate_colors_live_update( 'slideout_background_current_color', slideout_current, 'background-color' );

/**
 * Primary sub-navigation current selectors
 */
var slideout_submenu_current = '.slideout-navigation.main-navigation .main-nav ul ul li[class*="current-menu-"] > a,\
	.slideout-navigation.main-navigation .main-nav ul ul li[class*="current-menu-"] > a:hover,\
	.slideout-navigation.main-navigation .main-nav ul ul li[class*="current-menu-"].sfHover > a';

/**
 * Primary sub-navigation current text
 * Empty: link_color
 */
generate_colors_live_update( 'slideout_submenu_text_current_color', slideout_submenu_current, 'color', '' );

/**
 * Primary navigation current item background
 * Empty: transparent
 */
generate_colors_live_update( 'slideout_submenu_background_current_color', slideout_submenu_current, 'background-color' );
