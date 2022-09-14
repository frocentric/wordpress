(function( $ ){
	/**
	 * Navigation width
	 */
	wp.customize( 'generate_secondary_nav_settings[secondary_nav_layout_setting]', function( value ) {
		value.bind( function( newval ) {
			var navLocation = wp.customize.value('generate_secondary_nav_settings[secondary_nav_position_setting]')();

			if ( 'secondary-fluid-nav' == newval ) {
				$( '.secondary-navigation' ).removeClass( 'grid-container' ).removeClass( 'grid-parent' );
				if ( 'full-width' !== wp.customize.value('generate_secondary_nav_settings[secondary_nav_inner_width]')() ) {
					$( '.secondary-navigation .inside-navigation' ).addClass( 'grid-container' ).addClass( 'grid-parent' );
				}
			}
			if ( 'secondary-contained-nav' == newval ) {
				if ( generateSecondaryNav.isFlex && ( 'secondary-nav-float-right' === navLocation || 'secondary-nav-float-left' === navLocation ) ) {
					return;
				}

				jQuery( '.secondary-navigation' ).addClass( 'grid-container' ).addClass( 'grid-parent' );
				jQuery( '.secondary-navigation .inside-navigation' ).removeClass( 'grid-container' ).removeClass( 'grid-parent' );
			}
		} );
	} );

	/**
	 * Inner navigation width
	 */
	wp.customize( 'generate_secondary_nav_settings[secondary_nav_inner_width]', function( value ) {
		value.bind( function( newval ) {
			if ( 'full-width' == newval ) {
				$( '.secondary-navigation .inside-navigation' ).removeClass( 'grid-container' ).removeClass( 'grid-parent' );
			}
			if ( 'contained' == newval ) {
				$( '.secondary-navigation .inside-navigation' ).addClass( 'grid-container' ).addClass( 'grid-parent' );
			}
		} );
	} );

	wp.customize( 'generate_secondary_nav_settings[secondary_nav_alignment]', function( value ) {
		value.bind( function( newval ) {
			var classes = [ 'secondary-nav-aligned-left', 'secondary-nav-aligned-center', 'secondary-nav-aligned-right' ];
			$.each( classes, function( i, v ) {
				$( 'body' ).removeClass( v );
			});
			$( 'body' ).addClass( 'secondary-nav-aligned-' + newval );
		} );
	} );

	wp.customize( 'generate_secondary_nav_settings[secondary_menu_item]', function( value ) {
		value.bind( function( newval ) {
			jQuery( 'head' ).append( '<style id="secondary_menu_item">.secondary-navigation .main-nav ul li a, .secondary-navigation .menu-toggle, .secondary-menu-bar-items .menu-bar-item > a{padding: 0 ' + newval + 'px;}.secondary-navigation .menu-item-has-children .dropdown-menu-toggle{padding-right:' + newval + 'px;}</style>' );
			setTimeout(function() {
				jQuery( 'style#secondary_menu_item' ).not( ':last' ).remove();
			}, 50);
		} );
	} );

	wp.customize( 'generate_secondary_nav_settings[secondary_menu_item_height]', function( value ) {
		value.bind( function( newval ) {
			jQuery( 'head' ).append( '<style id="secondary_menu_item_height">.secondary-navigation .main-nav ul li a, .secondary-navigation .menu-toggle, .secondary-navigation .top-bar, .secondary-navigation .menu-bar-item > a{line-height: ' + newval + 'px;}.secondary-navigation ul ul{top:' + newval + 'px;}</style>' );
			setTimeout(function() {
				jQuery( 'style#secondary_menu_item_height' ).not( ':last' ).remove();
			}, 50);
		} );
	} );

	wp.customize( 'generate_secondary_nav_settings[secondary_sub_menu_item_height]', function( value ) {
		value.bind( function( newval ) {
			jQuery( 'head' ).append( '<style id="secondary_sub_menu_item_height">.secondary-navigation .main-nav ul ul li a{padding-top: ' + newval + 'px;padding-bottom: ' + newval + 'px;}</style>' );
			setTimeout(function() {
				jQuery( 'style#secondary_sub_menu_item_height' ).not( ':last' ).remove();
			}, 50);
		} );
	} );

})( jQuery );
