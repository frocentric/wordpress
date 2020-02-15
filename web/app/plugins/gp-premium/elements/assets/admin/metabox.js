jQuery(document).ready(function( $ ) {
	if ( $( '.element-settings' ).hasClass( 'header' ) || $( '.element-settings' ).hasClass( 'hook' ) ) {
		$( function() {
			if ( elements.settings) {
				wp.codeEditor.initialize( "generate-element-content", elements.settings );
			}
		} );
	}

	if ( $( '.choose-element-type-parent' ).is( ':visible' ) ) {
		$( '.select-type' ).focus();
	}

	$( 'select[name="_generate_element_type"]' ).on( 'change', function() {
		var _this = $( this ),
			element = _this.val();

		if ( '' == element ) {
			return;
		}

		$( '.element-settings' ).addClass( element ).removeClass( 'no-element-type' ).css( 'opacity', '' );
		$( 'body' ).removeClass( 'no-element-type' );

		var active_tab = $( '.element-metabox-tabs' ).find( 'li:visible:first' );
		active_tab.addClass( 'is-selected' );
		$( '.generate-elements-settings[data-tab="' + active_tab.attr( 'data-tab' ) + '"]' ).show();

		if ( 'layout' === element ) {
			$( '#generate-element-content' ).hide();
		}

		if ( 'header' === element ) {
			$( 'body' ).addClass( 'header-element-type' );
		}

		if ( elements.settings && 'layout' !== element ) {
			$( function() {
				wp.codeEditor.initialize( "generate-element-content", elements.settings );
			} );
		}

		_this.closest( '.choose-element-type-parent' ).hide();
	} );

	$( '#_generate_hook' ).on( 'change', function() {
		var _this = $( this );

		$( '.disable-header-hook' ).hide();
		$( '.disable-footer-hook' ).hide();
		$( '.custom-hook-name' ).hide();

		if ( 'generate_header' === _this.val() ) {
			$( '.disable-header-hook' ).show();
		}

		if ( 'generate_footer' === _this.val() ) {
			$( '.disable-footer-hook' ).show();
		}

		if ( 'custom' === _this.val() ) {
			$( '.custom-hook-name' ).show();
		}
	} );

	$( '#_generate_hook' ).select2( {
		width: '100%'
	} );

	$( '.element-metabox-tabs li' ).on( 'click', function() {
		var _this = $( this ),
			tab = _this.data( 'tab' );

		_this.siblings().removeClass( 'is-selected' );
		_this.addClass( 'is-selected' );
		$( '.generate-elements-settings' ).hide();
		$( '.generate-elements-settings[data-tab="' + tab + '"]' ).show();

		if ( $( '.element-settings' ).hasClass( 'header' ) ) {
			if ( 'hero' !== tab ) {
				$( '#generate-element-content' ).next( '.CodeMirror' ).hide();
			} else {
				$( '#generate-element-content' ).next( '.CodeMirror' ).show();
			}
		}
	} );

	var select2Init = function() {
		var selects = $( '.generate-element-row-content .condition:not(.hidden) select:not(.select2-init)' );

		selects.each( function() {
			var select = $( this ),
				config = {
					width: 'style'
				};

			select.select2( config );
			select.addClass( 'select2-init' );
		} );
	};

	select2Init();

	$( '.add-condition' ).on( 'click', function() {
		var _this = $( this );

		var row = _this.closest( '.generate-element-row-content' ).find( '.condition.hidden.screen-reader-text' ).clone(true);
		row.removeClass( 'hidden screen-reader-text' );
		row.insertBefore( _this.closest( '.generate-element-row-content' ).find( '.condition:last' ) );

		select2Init();

		return false;
	});

	$( '.remove-condition' ).on('click', function() {
		$(this).parents('.condition').remove();

		select2Init();

		return false;
	});

	var get_location_objects = function( _this, onload = false ) {
		var select         = _this,
			parent         = select.parent(),
			location       = select.val(),
			object_select  = parent.find( '.condition-object-select' ),
			locationString = '',
			actionType     = 'terms';

		if ( '' == location ) {

			parent.removeClass( 'generate-elements-rule-objects-visible' );
			select.closest( '.generate-element-row-content' ).find( '.generate-element-row-loading' ).remove();

		} else {
			if ( location.indexOf( ':taxonomy:' ) > 0 ) {
				var locationType = 'taxonomy';
			} else {
				var locationType = location.substr( 0, location.indexOf( ':' ) );
			}

			var locationID = location.substr( location.lastIndexOf( ':' ) + 1 );

			locationString = location;

			if ( 'taxonomy' == locationType || 'post' == locationType ) {

				if ( ! ( '.generate-element-row-loading' ).length ) {
					select.closest( '.generate-element-row-content' ).prepend( '<div class="generate-element-row-loading"></div>' );
				}

				if ( 'post' == locationType ) {
					if ( 'taxonomy' == locationType ) {
						actionType  = 'terms';
					} else {
						actionType  = 'posts';
					}
				}

				$.post( ajaxurl, {
					action : 'generate_elements_get_location_' + actionType,
					id     : locationID,
					nonce  : elements.nonce
				}, function( response ) {
					response = $.parseJSON( response );
					var objects = response.objects;

					var blank = {
						'id': '',
						'name': 'All ' + response.label,
					};

					if ( location.indexOf( ':taxonomy:' ) > 0 ) {
						blank.name = elements.choose;
					}

					objects.unshift( blank );
					object_select.empty();
					$.each( objects, function( key, value ) {
						object_select.append( $( '<option>', {
							value: value.id,
							label: elements.showID && value.id ? value.name + ': ' + value.id : value.name,
							text: elements.showID && value.id ? value.name + ': ' + value.id : value.name,
						} ) );
					} );

					parent.addClass( 'generate-elements-rule-objects-visible' );

					if ( onload ) {
						object_select.val( object_select.attr( 'data-saved-value' ) );
					}

					select.closest( '.generate-element-row-content' ).find( '.generate-element-row-loading' ).remove();
				} );

			} else {
				parent.removeClass( 'generate-elements-rule-objects-visible' );
				select.closest( '.generate-element-row-content' ).find( '.generate-element-row-loading' ).remove();
				object_select.empty().append( '<option value="0"></option>' );
				object_select.val( '0' );
			}

			//remove.show();
		}
	};

	$( '.condition select.condition-select' ).on( 'change', function() {
		get_location_objects( $( this ) );
	} );

	$( '.generate-elements-rule-objects-visible' ).each( function() {
		var _this = $( this ),
			select = _this.find( 'select.condition-select' );

		$( '<div class="generate-element-row-loading"></div>' ).insertBefore( _this );

		get_location_objects( select, true );
	} );

	$( '.set-featured-image a, .change-featured-image a:not(.remove-image)' ).on( 'click', function( event ) {
		event.preventDefault();

		// Stop propagation to prevent thickbox from activating.
		event.stopPropagation();

		// Open the featured image modal
		wp.media.featuredImage.frame().open();
	} );

	wp.media.featuredImage.frame().on( 'select', function() {
		$( '.set-featured-image' ).hide();
		$( '.change-featured-image' ).show();

		setTimeout( function() {
			$( '.image-preview' ).empty();
			$( '#postimagediv img' ).appendTo( '.image-preview' );
		}, 500 );
	} );

	$( '#postimagediv' ).on( 'click', '#remove-post-thumbnail', function() {
		$( '.set-featured-image' ).show();
		$( '.change-featured-image' ).hide();
		$( '.image-preview' ).empty();
		return false;
	});

	$( '.remove-image' ).on( 'click', function( e ) {
		e.preventDefault();

		$( '#remove-post-thumbnail' ).trigger( 'click' );
	} );

	$( '.generate-upload-file' ).on( 'click', function() {
		if ( frame ) {
			frame.open();
			return;
		}

		var _this = $( this ),
			container = _this.closest( '.media-container' );

		var frame = wp.media( {
			title: _this.data( 'title' ),
			multiple: false,
			library: { type : _this.data( 'type' ) },
			button: { text : _this.data( 'insert' ) }
		} );

		frame.on( 'select', function() {
			var attachment = frame.state().get('selection').first().toJSON();

			container.find( '.media-field' ).val( attachment.id );
			container.find( '.remove-field' ).show();

			if ( _this.data( 'preview' ) ) {
				container.find( '.gp-media-preview' ).empty().append( '<img src="' + attachment.url + '" width="50" />' ).show();
			}
		} );

		frame.open();
	} );

	$( '.remove-field' ).on( 'click', function() {
		var _this = $( this ),
			container = _this.closest( '.media-container' );

		_this.hide();
		container.find( '.media-field' ).val( '' );
		container.find( '.gp-media-preview' ).empty();
	} );

	$( '#_generate_hero_background_image' ).on( 'change', function() {
		var _this = $( this );

		if ( '' !== _this.val() ) {
			$( '.requires-background-image' ).show();
		} else {
			$( '.requires-background-image' ).hide();
		}

		if ( 'featured-image' == _this.val() ) {
			$( '.image-text' ).text( elements.fallback_image );
		}

		if ( 'custom-image' == _this.val() ) {
			$( '.image-text' ).text( elements.custom_image );
		}
	} );

	// Responsive controls in our settings.
	$( '.responsive-controls a' ).on( 'click', function( e ) {
		e.preventDefault();

		var _this = $( this ),
			control = _this.attr( 'data-control' ),
			control_area = _this.closest( '.generate-element-row-content' );

		control_area.find( '.padding-container' ).hide();
		control_area.find( '.padding-container.' + control ).show();
		_this.siblings().removeClass( 'is-selected' );
		_this.addClass( 'is-selected' );
	} );

	$( '#_generate_site_header_merge' ).on( 'change', function() {
		var _this = $( this );

		if ( '' !== _this.val() ) {
			$( '.requires-header-merge' ).show();

			if ( $( '#_generate_navigation_colors' ).is( ':checked' ) ) {
				$( '.requires-navigation-colors' ).show();
			}

			if ( $( '#_generate_hero_full_screen' ).is( ':checked' ) ) {
				$( '.requires-full-screen' ).show();
			}
		} else {
			$( '.requires-header-merge' ).hide();
			$( '.requires-navigation-colors' ).hide();
			$( '.requires-full-screen' ).hide();
		}
	} );

	$( '#_generate_navigation_colors' ).on( 'change', function() {
		var _this = $( this );

		if ( _this.is( ':checked' ) ) {
			$( '.requires-navigation-colors' ).show();
		} else {
			$( '.requires-navigation-colors' ).hide();
		}
	} );

	$( '#_generate_hero_full_screen' ).on( 'change', function() {
		var _this = $( this );

		if ( _this.is( ':checked' ) ) {
			$( '.requires-full-screen' ).show();
		} else {
			$( '.requires-full-screen' ).hide();
		}
	} );

	$( '#_generate_hero_background_parallax' ).on( 'change', function() {
		var _this = $( this );

		if ( _this.is( ':checked' ) ) {
			$( '#_generate_hero_background_position' ).val( '' ).change();
			$( '#_generate_hero_background_position option[value="left center"]' ).attr( 'disabled', true );
			$( '#_generate_hero_background_position option[value="left bottom"]' ).attr( 'disabled', true );
			$( '#_generate_hero_background_position option[value="right center"]' ).attr( 'disabled', true );
			$( '#_generate_hero_background_position option[value="right bottom"]' ).attr( 'disabled', true );
			$( '#_generate_hero_background_position option[value="center center"]' ).attr( 'disabled', true );
			$( '#_generate_hero_background_position option[value="center bottom"]' ).attr( 'disabled', true );
		} else {
			$( '#_generate_hero_background_position option[value="left center"]' ).attr( 'disabled', false );
			$( '#_generate_hero_background_position option[value="left bottom"]' ).attr( 'disabled', false );
			$( '#_generate_hero_background_position option[value="right center"]' ).attr( 'disabled', false );
			$( '#_generate_hero_background_position option[value="right bottom"]' ).attr( 'disabled', false );
			$( '#_generate_hero_background_position option[value="center center"]' ).attr( 'disabled', false );
			$( '#_generate_hero_background_position option[value="center bottom"]' ).attr( 'disabled', false );
		}
	} );
} );
