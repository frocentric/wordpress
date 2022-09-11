jQuery( function( $ ) {
	$( '[data-type="overlay_design"]' ).on( 'click', function( e ) {
		e.preventDefault();

		// eslint-disable-next-line no-alert
		if ( ! confirm( gpButtonActions.warning ) ) {
			return;
		}

		( function( api ) {
			'use strict';

			api.instance( 'generate_settings[slideout_background_color]' ).set( gpButtonActions.styling.backgroundColor );
			api.instance( 'generate_settings[slideout_text_color]' ).set( gpButtonActions.styling.textColor );
			api.instance( 'generate_settings[slideout_background_hover_color]' ).set( gpButtonActions.styling.backgroundHoverColor );
			api.instance( 'generate_settings[slideout_background_current_color]' ).set( gpButtonActions.styling.backgroundCurrentColor );

			api.instance( 'generate_settings[slideout_submenu_background_color]' ).set( gpButtonActions.styling.subMenuBackgroundColor );
			api.instance( 'generate_settings[slideout_submenu_text_color]' ).set( gpButtonActions.styling.subMenuTextColor );
			api.instance( 'generate_settings[slideout_submenu_background_hover_color]' ).set( gpButtonActions.styling.subMenuBackgroundHoverColor );
			api.instance( 'generate_settings[slideout_submenu_background_current_color]' ).set( gpButtonActions.styling.subMenuBackgroundCurrentColor );

			api.instance( 'generate_settings[slideout_font_weight]' ).set( gpButtonActions.styling.fontWeight );
			api.instance( 'generate_settings[slideout_font_size]' ).set( gpButtonActions.styling.fontSize );

			$( '.wp-color-picker' ).wpColorPicker().change();
		}( wp.customize ) );
	} );

	$( '[data-type="regenerate_external_css"]' ).on( 'click', function( e ) {
		var $thisButton = $( this ); // eslint-disable-line no-var
		e.preventDefault();

		$thisButton.removeClass( 'success' ).addClass( 'loading' );

		$.post( ajaxurl, {
			action: 'generatepress_regenerate_css_file',
			_nonce: $thisButton.data( 'nonce' ),
		} ).done( function() {
			$thisButton.removeClass( 'loading' ).addClass( 'success' );
		} );
	} );
} );
