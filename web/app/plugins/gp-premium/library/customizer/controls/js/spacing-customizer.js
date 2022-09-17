( function( $, api ) {
	// No longer needed as of 1.2.95
	// Keeping it here just in case
	api.controlConstructor.spacing = api.Control.extend( {
		ready() {
			var control = this;
			$( '.generate-number-control', control.container ).on( 'change keyup',
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		},
	} );

	api.controlConstructor[ 'gp-spacing-slider' ] = api.Control.extend( {
		ready() {
			var control = this;
			$( '.slider-input', control.container ).on( 'change keyup',
				function() {
					control.setting.set( $( this ).val() );
				}
			);
		},
	} );

	api.controlConstructor[ 'generatepress-spacing' ] = api.Control.extend( {
		ready() {
			var control = this,
				controlClass = '.customize-control-generatepress-spacing',
				footerActions = jQuery( '#customize-footer-actions' );

			// Figure out which device icon to make active on load
			jQuery( controlClass + ' .gp-spacing-control-section' ).each( function() {
				var _this = jQuery( this );
				_this.find( '.gp-device-controls' ).children( 'span:first-child' ).addClass( 'selected' );
				_this.find( '.spacing-values-area:first-child' ).show();
			} );

			// Do stuff when device icons are clicked
			jQuery( controlClass + ' .gp-device-controls > span' ).on( 'click', function( event ) {
				var device = jQuery( this ).data( 'option' );

				jQuery( controlClass + ' .gp-device-controls span' ).each( function() {
					var _this = jQuery( this );

					if ( device === _this.attr( 'data-option' ) ) {
						_this.addClass( 'selected' );
						_this.siblings().removeClass( 'selected' );
					}
				} );

				jQuery( controlClass + ' .spacing-values-container .spacing-values-area' ).each( function() {
					var _this = jQuery( this );

					if ( device === _this.attr( 'data-option' ) ) {
						_this.show();
						_this.siblings().hide();
					}
				} );

				// Set the device we're currently viewing
				wp.customize.previewedDevice.set( jQuery( event.currentTarget ).data( 'option' ) );
			} );

			// Set the selected devices in our control when the Customizer devices are clicked
			footerActions.find( '.devices button' ).on( 'click', function() {
				var device = jQuery( this ).data( 'device' );
				jQuery( controlClass + ' .gp-device-controls span' ).each( function() {
					var _this = jQuery( this );

					if ( device === _this.attr( 'data-option' ) ) {
						_this.addClass( 'selected' );
						_this.siblings().removeClass( 'selected' );
					}
				} );

				jQuery( controlClass + ' .spacing-values-container .spacing-values-area' ).each( function() {
					var _this = jQuery( this );

					if ( device === _this.attr( 'data-option' ) ) {
						_this.show();
						_this.siblings().hide();
					}
				} );
			} );

			control.container.on( 'change keyup', '.spacing-top',
				function() {
					control.settings.desktop_top.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.spacing-right',
				function() {
					control.settings.desktop_right.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.spacing-bottom',
				function() {
					control.settings.desktop_bottom.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.spacing-left',
				function() {
					control.settings.desktop_left.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.tablet-spacing-top',
				function() {
					control.settings.tablet_top.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.tablet-spacing-right',
				function() {
					control.settings.tablet_right.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.tablet-spacing-bottom',
				function() {
					control.settings.tablet_bottom.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.tablet-spacing-left',
				function() {
					control.settings.tablet_left.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.mobile-spacing-top',
				function() {
					control.settings.mobile_top.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.mobile-spacing-right',
				function() {
					control.settings.mobile_right.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.mobile-spacing-bottom',
				function() {
					control.settings.mobile_bottom.set( jQuery( this ).val() );
				}
			);

			control.container.on( 'change keyup', '.mobile-spacing-left',
				function() {
					control.settings.mobile_left.set( jQuery( this ).val() );
				}
			);
		},
	} );
}( jQuery, wp.customize ) );

jQuery( function( $ ) {
	$( '.gp-link-spacing' ).on( 'click', function( e ) {
		e.preventDefault();

		// Set up variables
		var _this = $( this ),
			element = _this.data( 'element' );

		// Add our linked-values class to the next 4 elements
		_this.parent( '.gp-spacing-section' ).prevAll().slice( 0, 4 ).find( 'input' ).addClass( 'linked-values' ).attr( 'data-element', element );

		// Change our link icon class
		_this.hide();
		_this.next( 'span' ).show();
	} );

	$( '.gp-unlink-spacing' ).on( 'click', function( e ) {
		e.preventDefault();

		// Set up variables
		var _this = $( this );

		// Remove our linked-values class to the next 4 elements
		_this.parent( '.gp-spacing-section' ).prevAll().slice( 0, 4 ).find( 'input' ).removeClass( 'linked-values' ).attr( 'data-element', '' );

		// Change our link icon class
		_this.hide();
		_this.prev( 'span' ).show();
	} );

	$( '.gp-spacing-section' ).on( 'input', '.linked-values', function() {
		var _this = $( this ),
			data = _this.attr( 'data-element' ),
			val = _this.val(),
			targetElements = _this.closest( '.spacing-values-area' ).find( '.linked-values[ data-element="' + data + '" ]' );

		targetElements.each( function() {
			var element = $( this );
			element.val( val ).change();
		} );
	} );
} );
