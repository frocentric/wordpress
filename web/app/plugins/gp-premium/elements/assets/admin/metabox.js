jQuery( function( $ ) {
	if ( $( '.element-settings' ).hasClass( 'header' ) || $( '.element-settings' ).hasClass( 'hook' ) ) {
		$( function() {
			if ( elements.settings ) {
				wp.codeEditor.initialize( 'generate-element-content', elements.settings );
			}
		} );
	}

	$( '#_generate_block_type' ).on( 'change', function() {
		var _this = $( this ).val();

		if ( 'hook' === _this ) {
			$( '.hook-row' ).removeClass( 'hide-hook-row' );
		} else {
			$( '.hook-row' ).addClass( 'hide-hook-row' );
		}

		$( 'body' ).removeClass( 'right-sidebar-block-type' );
		$( 'body' ).removeClass( 'left-sidebar-block-type' );
		$( 'body' ).removeClass( 'header-block-type' );
		$( 'body' ).removeClass( 'footer-block-type' );

		$( 'body' ).addClass( _this + '-block-type' );

		if ( 'left-sidebar' === _this || 'right-sidebar' === _this ) {
			$( '.sidebar-notice' ).show();
		} else {
			$( '.sidebar-notice' ).hide();
		}
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
		width: '100%',
	} );

	$( '.element-metabox-tabs li' ).on( 'click', function() {
		var _this = $( this ),
			tab = _this.data( 'tab' );

		_this.siblings().removeClass( 'is-selected' );
		_this.addClass( 'is-selected' );
		$( '.generate-elements-settings' ).hide();
		$( '.generate-elements-settings[data-tab="' + tab + '"]' ).show();

		if ( $( '.element-settings' ).hasClass( 'block' ) && 'hook-settings' === tab ) {
			$( '.generate-elements-settings[data-tab="display-rules"]' ).show();
		}

		if ( $( '.element-settings' ).hasClass( 'header' ) ) {
			if ( 'hero' !== tab ) {
				$( '#generate-element-content' ).next( '.CodeMirror' ).removeClass( 'gpp-elements-show-codemirror' );
				$( '#generate_page_hero_template_tags' ).css( 'display', '' );
			} else {
				$( '#generate-element-content' ).next( '.CodeMirror' ).addClass( 'gpp-elements-show-codemirror' );
				$( '#generate_page_hero_template_tags' ).css( 'display', 'block' );
			}
		}
	} );

	var select2Init = function() {
		var selects = $( '.generate-element-row-content .condition:not(.hidden) select:not(.select2-init)' );

		selects.each( function() {
			var select = $( this ),
				config = {
					width: 'style',
				};

			select.select2( config );
			select.addClass( 'select2-init' );
		} );
	};

	select2Init();

	$( '.add-condition' ).on( 'click', function() {
		var _this = $( this );

		var row = _this.closest( '.generate-element-row-content' ).find( '.condition.hidden.screen-reader-text' ).clone( true );
		row.removeClass( 'hidden screen-reader-text' );
		row.insertBefore( _this.closest( '.generate-element-row-content' ).find( '.condition:last' ) );

		select2Init();

		return false;
	} );

	$( '.remove-condition' ).on( 'click', function() {
		$( this ).parents( '.condition' ).remove();

		select2Init();

		return false;
	} );

	var getLocationObjects = function( _this, onload = false, data = '' ) {
		var select = _this,
			parent = select.parent(),
			location = select.val(),
			objectSelect = parent.find( '.condition-object-select' ),
			locationType = '',
			actionType = 'terms';

		if ( '' === location ) {
			parent.removeClass( 'generate-elements-rule-objects-visible' );
			select.closest( '.generate-element-row-content' ).find( '.generate-element-row-loading' ).remove();
		} else {
			if ( location.indexOf( ':taxonomy:' ) > 0 ) {
				locationType = 'taxonomy';
			} else {
				locationType = location.substr( 0, location.indexOf( ':' ) );
			}

			var locationID = location.substr( location.lastIndexOf( ':' ) + 1 );

			if ( 'taxonomy' === locationType || 'post' === locationType ) {
				if ( ! ( '.generate-element-row-loading' ).length ) {
					select.closest( '.generate-element-row-content' ).prepend( '<div class="generate-element-row-loading"></div>' );
				}

				var fillObjects = function( response ) {
					var objects = response[ locationID ].objects;

					var blank = {
						id: '',
						name: 'All ' + response[ locationID ].label,
					};

					if ( location.indexOf( ':taxonomy:' ) > 0 ) {
						blank.name = elements.choose;
					}

					objectSelect.empty();

					objectSelect.append( $( '<option>', {
						value: blank.id,
						label: blank.name,
						text: blank.name,
					} ) );

					$.each( objects, function( key, value ) {
						objectSelect.append( $( '<option>', {
							value: value.id,
							label: elements.showID && value.id ? value.name + ': ' + value.id : value.name,
							text: elements.showID && value.id ? value.name + ': ' + value.id : value.name,
						} ) );
					} );

					parent.addClass( 'generate-elements-rule-objects-visible' );

					if ( onload ) {
						objectSelect.val( objectSelect.attr( 'data-saved-value' ) );
					}

					select.closest( '.generate-element-row-content' ).find( '.generate-element-row-loading' ).remove();
				};

				if ( data && onload ) {
					// Use pre-fetched data if we just loaded the page.
					fillObjects( data );
				} else {
					if ( 'post' === locationType ) {
						if ( 'taxonomy' === locationType ) {
							actionType = 'terms';
						} else {
							actionType = 'posts';
						}
					}

					$.post( ajaxurl, {
						action: 'generate_elements_get_location_' + actionType,
						id: locationID,
						nonce: elements.nonce,
					}, function( response ) {
						response = JSON.parse( response );
						fillObjects( response );
					} );
				}
			} else {
				parent.removeClass( 'generate-elements-rule-objects-visible' );
				select.closest( '.generate-element-row-content' ).find( '.generate-element-row-loading' ).remove();
				objectSelect.empty().append( '<option value="0"></option>' );
				objectSelect.val( '0' );
			}
		}
	};

	$( '.condition select.condition-select' ).on( 'change', function() {
		getLocationObjects( $( this ) );

		$( '.elements-no-location-error' ).hide();
	} );

	var postObjects = [];
	var termObjects = [];

	$( '.generate-elements-rule-objects-visible' ).each( function() {
		var _this = $( this ),
			select = _this.find( 'select.condition-select' ),
			location = select.val(),
			locationID = location.substr( location.lastIndexOf( ':' ) + 1 ),
			locationType = '';

		if ( location.indexOf( ':taxonomy:' ) > 0 ) {
			locationType = 'taxonomy';
		} else {
			locationType = location.substr( 0, location.indexOf( ':' ) );
		}

		if ( 'post' === locationType ) {
			if ( ! postObjects.includes( locationID ) ) {
				postObjects.push( locationID );
			}
		} else if ( 'taxonomy' === locationType && ! termObjects.includes( locationID ) ) {
			termObjects.push( locationID );
		}
	} );

	if ( postObjects.length > 0 || termObjects.length > 0 ) {
		$.post( ajaxurl, {
			action: 'generate_elements_get_location_objects',
			posts: postObjects,
			terms: termObjects,
			nonce: elements.nonce,
		}, function( response ) {
			response = JSON.parse( response );

			$( '.generate-elements-rule-objects-visible' ).each( function() {
				var _this = $( this ),
					select = _this.find( 'select.condition-select' );

				$( '<div class="generate-element-row-loading"></div>' ).insertBefore( _this );

				getLocationObjects( select, true, response );
			} );
		} );
	}

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
	} );

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
			library: { type: _this.data( 'type' ) },
			button: { text: _this.data( 'insert' ) },
		} );

		frame.on( 'select', function() {
			var attachment = frame.state().get( 'selection' ).first().toJSON();

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

		if ( 'featured-image' === _this.val() ) {
			$( '.image-text' ).text( elements.fallback_image );
		}

		if ( 'custom-image' === _this.val() ) {
			$( '.image-text' ).text( elements.custom_image );
		}
	} );

	// Responsive controls in our settings.
	$( '.responsive-controls a' ).on( 'click', function( e ) {
		e.preventDefault();

		var _this = $( this ),
			control = _this.attr( 'data-control' ),
			controlArea = _this.closest( '.generate-element-row-content' );

		controlArea.find( '.padding-container' ).hide();
		controlArea.find( '.padding-container.' + control ).show();
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
