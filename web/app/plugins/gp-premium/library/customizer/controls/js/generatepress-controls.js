( function( $, api ) {

	/**
	 * Set some controls when we're using the navigation as a header.
	 *
	 * @since 1.8
	*/
	api( 'generate_menu_plus_settings[navigation_as_header]', function( value ) {
		value.bind( function( newval ) {
			var navAlignmentSetting = api.instance( 'generate_settings[nav_alignment_setting]' ),
				navAlignment = gpControls.navigationAlignment,
				siteTitleFontSizeSetting = api.instance( 'generate_settings[site_title_font_size]' ),
				mobileSiteTitleFontSizeSetting = api.instance( 'generate_settings[mobile_site_title_font_size]' ),
				siteTitleFontSize = gpControls.siteTitleFontSize,
				mobileSiteTitleFontSize = gpControls.mobileSiteTitleFontSize,
				mobileHeader = api.instance( 'generate_menu_plus_settings[mobile_header]' ).get(),
				navTextColorSetting = api.instance( 'generate_settings[navigation_text_color]' ),
				navTextColor = gpControls.navigationTextColor,
				headerTextColorSetting = api.instance( 'generate_settings[header_text_color]' ),
				headerTextColor = gpControls.headerTextColor;

			if ( ! siteTitleFontSizeSetting._dirty && 25 !== siteTitleFontSizeSetting.get() ) {
				siteTitleFontSize = siteTitleFontSizeSetting.get();
			}

			if ( ! mobileSiteTitleFontSizeSetting._dirty && 20 !== mobileSiteTitleFontSizeSetting.get() ) {
				mobileSiteTitleFontSize = mobileSiteTitleFontSizeSetting.get();
			}

			if ( ! navTextColorSetting._dirty ) {
				navTextColor = navTextColorSetting.get();
			}

			if ( ! headerTextColorSetting._dirty ) {
				headerTextColor = headerTextColorSetting.get();
			}

			if ( newval ) {
				navAlignmentSetting.set( 'right' );
				siteTitleFontSizeSetting.set( 25 );
				api.instance( 'generate_settings[site_title_color]' ).set( navTextColor );

				if ( 'enable' !== mobileHeader ) {
					mobileSiteTitleFontSizeSetting.set( 20 );
				}
			} else {
				navAlignmentSetting.set( navAlignment );
				siteTitleFontSizeSetting.set( siteTitleFontSize );
				api.instance( 'generate_settings[site_title_color]' ).set( headerTextColor );

				if ( 'enable' !== mobileHeader ) {
					mobileSiteTitleFontSizeSetting.set( mobileSiteTitleFontSize );
				}
			}
		} );

		var showRegularHeader,
			showRegularHeaderCallback,
			showNavHeader,
			showNavHeaderCallback;

		/**
		 * Determine whether we should display our header controls.
		 *
		 * @returns {boolean}
		 */
		showRegularHeader = function() {
			if ( value.get() ) {
				return false;
			}

			return true;
		};

		/**
		 * Determine whether our navigation is our header.
		 *
		 * @returns {boolean}
		 */
		showNavHeader = function() {
			if ( value.get() ) {
				return true;
			}

			return false;
		};

		/**
		 * Update a control's active state according to the navigation as header option.
		 *
		 * @param {wp.customize.Control} control
		 */
		showRegularHeaderCallback = function( control ) {
			var setActiveState = function() {
				control.active.set( showRegularHeader() );
			};

			control.active.validate = showRegularHeader;
			setActiveState();
			value.bind( setActiveState );
		};

		/**
		 * Update a control's active state according to the navigation as header option.
		 *
		 * @param {wp.customize.Control} control
		 */
		showNavHeaderCallback = function( control ) {
			var setActiveState = function() {
				control.active.set( showNavHeader() );
			};

			control.active.validate = showNavHeader;
			setActiveState();
			value.bind( setActiveState );
		};

		api.control( 'generate_header_helper', showRegularHeaderCallback );
		api.control( 'generate_settings[header_layout_setting]', showRegularHeaderCallback );
		api.control( 'generate_settings[header_inner_width]', showRegularHeaderCallback );
		api.control( 'generate_settings[header_alignment_setting]', showRegularHeaderCallback );
		api.control( 'header_spacing', showRegularHeaderCallback );
		api.control( 'generate_settings[header_background_color]', showRegularHeaderCallback );
		api.control( 'header_text_color', showRegularHeaderCallback );
		api.control( 'header_link_color', showRegularHeaderCallback );
		api.control( 'header_link_hover_color', showRegularHeaderCallback );
		api.control( 'site_tagline_color', showRegularHeaderCallback );
		api.control( 'font_site_tagline_control', showRegularHeaderCallback );
		api.control( 'generate_settings[site_tagline_font_size]', showRegularHeaderCallback );
		api.control( 'generate_settings[nav_position_setting]', showRegularHeaderCallback );
		api.control( 'generate_settings[logo_width]', showRegularHeaderCallback );
	} );

	/**
	 * Set the navigation branding font size label on mobile header branding change.
	 *
	 * @since 1.8
	*/
	api( 'generate_menu_plus_settings[mobile_header_branding]', function( value ) {
		value.bind( function( newval ) {
			if ( 'title' === newval ) {
				api.instance( 'generate_settings[mobile_site_title_font_size]' ).set( 20 );
			}
		} );
	} );

	/**
	 * Set the navigation branding font size label on mobile header change.
	 *
	 * @since 1.8
	*/
	api( 'generate_menu_plus_settings[mobile_header]', function( value ) {
		value.bind( function( newval ) {
			var mobileSiteTitleFontSizeSetting = api.instance( 'generate_settings[mobile_site_title_font_size]' ),
				mobileSiteTitleFontSize = gpControls.mobileSiteTitleFontSize;

			if ( ! mobileSiteTitleFontSizeSetting._dirty && 20 !== mobileSiteTitleFontSizeSetting.get() ) {
				mobileSiteTitleFontSize = mobileSiteTitleFontSizeSetting.get();
			}

			if ( 'enable' === newval ) {
				api.instance( 'generate_settings[mobile_site_title_font_size]' ).set( 20 );
			} else {
				api.instance( 'generate_settings[mobile_site_title_font_size]' ).set( mobileSiteTitleFontSize );
			}
		} );
	} );

} )( jQuery, wp.customize );
