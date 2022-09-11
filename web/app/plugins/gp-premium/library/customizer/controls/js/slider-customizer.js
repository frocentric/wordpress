wp.customize.controlConstructor[ 'generatepress-pro-range-slider' ] = wp.customize.Control.extend( {
	ready() {
		'use strict';

		var control = this,
			value,
			controlClass = '.customize-control-generatepress-pro-range-slider',
			footerActions = jQuery( '#customize-footer-actions' );

		// Set up the sliders
		jQuery( '.generatepress-slider' ).each( function() {
			var _this = jQuery( this );
			var _input = _this.closest( 'label' ).find( 'input[type="number"]' );
			var _text = _input.next( '.value' );

			_this.slider( {
				value: _input.val(),
				min: _this.data( 'min' ),
				max: _this.data( 'max' ),
				step: _this.data( 'step' ),
				slide( event, ui ) {
					_input.val( ui.value ).change();
					_text.text( ui.value );
				},
			} );
		} );

		// Update the range value based on the input value
		jQuery( controlClass + ' .gp_range_value input[type=number]' ).on( 'input', function() {
			value = jQuery( this ).attr( 'value' );

			if ( '' == value ) {
				value = -1;
			}

			jQuery( this ).closest( 'label' ).find( '.generatepress-slider' ).slider( 'value', parseFloat( value ) ).change();
		} );

		// Handle the reset button
		jQuery( controlClass + ' .generatepress-reset' ).on( 'click', function() {
			var icon = jQuery( this ),
				visibleArea = icon.closest( '.gp-range-title-area' ).next( '.gp-range-slider-areas' ).children( 'label:visible' ),
				input = visibleArea.find( 'input[type=number]' ),
				sliderValue = visibleArea.find( '.generatepress-slider' ),
				visualValue = visibleArea.find( '.gp_range_value' ),
				resetValue = input.attr( 'data-reset_value' );

			input.val( resetValue ).change();
			visualValue.find( 'input' ).val( resetValue );
			visualValue.find( '.value' ).text( resetValue );

			if ( '' == resetValue ) {
				resetValue = -1;
			}

			sliderValue.slider( 'value', parseFloat( resetValue ) );
		} );

		// Figure out which device icon to make active on load
		jQuery( controlClass + ' .generatepress-range-slider-control' ).each( function() {
			var _this = jQuery( this );

			_this.find( '.gp-device-controls' ).children( 'span:first-child' ).addClass( 'selected' );
			_this.find( '.range-option-area:first-child' ).show();
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

			jQuery( controlClass + ' .gp-range-slider-areas label' ).each( function() {
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

			jQuery( controlClass + ' .gp-range-slider-areas label' ).each( function() {
				var _this = jQuery( this );

				if ( device === _this.attr( 'data-option' ) ) {
					_this.show();
					_this.siblings().hide();
				}
			} );
		} );

		// Apply changes when desktop slider is changed
		control.container.on( 'input change', '.desktop-range',
			function() {
				control.settings.desktop.set( jQuery( this ).val() );
			}
		);

		// Apply changes when tablet slider is changed
		control.container.on( 'input change', '.tablet-range',
			function() {
				control.settings.tablet.set( jQuery( this ).val() );
			}
		);

		// Apply changes when mobile slider is changed
		control.container.on( 'input change', '.mobile-range',
			function() {
				control.settings.mobile.set( jQuery( this ).val() );
			}
		);
	},
} );
