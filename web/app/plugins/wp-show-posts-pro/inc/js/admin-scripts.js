jQuery( document ).ready( function( $ ) {
	// Excerpt or full content
	if ( 'full' !== $( '#wpsp-content-type' ).val() ) {
		$( '#butterbean-control-wpsp_link' ).hide();
		$( '#butterbean-control-wpsp_link_hover' ).hide();
	}
	if ( 'none' == $( '#wpsp-content-type' ).val() ) {
		$( '#butterbean-control-wpsp_text' ).hide();
	} else {
		$( '#butterbean-control-wpsp_text' ).show();
	}
	$( '#wpsp-content-type' ).change(function() {
		if ( 'full' == $( this ).val() ) {
			$( '#butterbean-control-wpsp_link' ).show();
			$( '#butterbean-control-wpsp_link_hover' ).show();
		} else {
			$( '#butterbean-control-wpsp_link' ).hide();
			$( '#butterbean-control-wpsp_link_hover' ).hide();
		}

		if ( 'none' == $( this ).val() ) {
			$( '#butterbean-control-wpsp_text' ).hide();
		} else {
			$( '#butterbean-control-wpsp_text' ).show();
		}
	});

	// Title
	if ( ! $( '#wpsp-include-title' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_title_color' ).hide();
		$( '#butterbean-control-wpsp_title_color_hover' ).hide();
		$( '#butterbean-control-wpsp_title_font_size' ).hide();
	}

	$( '#wpsp-include-title' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_title_color' ).hide();
			$( '#butterbean-control-wpsp_title_color_hover' ).hide();
			$( '#butterbean-control-wpsp_title_font_size' ).hide();
		} else {
			$( '#butterbean-control-wpsp_title_color' ).show();
			$( '#butterbean-control-wpsp_title_color_hover' ).show();
			$( '#butterbean-control-wpsp_title_font_size' ).show();
		}
	});

	// Read more text
	if ( '' == $( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val() ) {
		$( '#butterbean-control-wpsp_read_more_background_color' ).hide();
		$( '#butterbean-control-wpsp_read_more_background_color_hover' ).hide();
		$( '#butterbean-control-wpsp_read_more_text_color' ).hide();
		$( '#butterbean-control-wpsp_read_more_text_color_hover' ).hide();
		$( '#butterbean-control-wpsp_read_more_border_color' ).hide();
		$( '#butterbean-control-wpsp_read_more_border_color_hover' ).hide();
	}

	$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).on( 'change keyup input', function() {
		if ( ! this.value ) {
			$( '#butterbean-control-wpsp_read_more_background_color' ).hide();
			$( '#butterbean-control-wpsp_read_more_background_color_hover' ).hide();
			$( '#butterbean-control-wpsp_read_more_text_color' ).hide();
			$( '#butterbean-control-wpsp_read_more_text_color_hover' ).hide();
			$( '#butterbean-control-wpsp_read_more_border_color' ).hide();
			$( '#butterbean-control-wpsp_read_more_border_color_hover' ).hide();
		} else {
			$( '#butterbean-control-wpsp_read_more_background_color' ).show();
			$( '#butterbean-control-wpsp_read_more_background_color_hover' ).show();
			$( '#butterbean-control-wpsp_read_more_text_color' ).show();
			$( '#butterbean-control-wpsp_read_more_text_color_hover' ).show();
			$( '#butterbean-control-wpsp_read_more_border_color' ).show();
			$( '#butterbean-control-wpsp_read_more_border_color_hover' ).show();
		}
	});

	// Social icons
	if ( ! $( '#wpsp-twitter' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_twitter_color' ).hide();
		$( '#butterbean-control-wpsp_twitter_color_hover' ).hide();
	}

	$( '#wpsp-twitter' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_twitter_color' ).hide();
			$( '#butterbean-control-wpsp_twitter_color_hover' ).hide();
		} else {
			$( '#butterbean-control-wpsp_twitter_color' ).show();
			$( '#butterbean-control-wpsp_twitter_color_hover' ).show();
		}
	});

	if ( ! $( '#wpsp-facebook' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_facebook_color' ).hide();
		$( '#butterbean-control-wpsp_facebook_color_hover' ).hide();
	}

	$( '#wpsp-facebook' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_facebook_color' ).hide();
			$( '#butterbean-control-wpsp_facebook_color_hover' ).hide();
		} else {
			$( '#butterbean-control-wpsp_facebook_color' ).show();
			$( '#butterbean-control-wpsp_facebook_color_hover' ).show();
		}
	});

	if ( ! $( '#wpsp-googleplus' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_googleplus_color' ).hide();
		$( '#butterbean-control-wpsp_googleplus_color_hover' ).hide();
	}

	$( '#wpsp-googleplus' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_googleplus_color' ).hide();
			$( '#butterbean-control-wpsp_googleplus_color_hover' ).hide();
		} else {
			$( '#butterbean-control-wpsp_googleplus_color' ).show();
			$( '#butterbean-control-wpsp_googleplus_color_hover' ).show();
		}
	});

	if ( ! $( '#wpsp-pinterest' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_pinterest_color' ).hide();
		$( '#butterbean-control-wpsp_pinterest_color_hover' ).hide();
	}

	$( '#wpsp-pinterest' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_pinterest_color' ).hide();
			$( '#butterbean-control-wpsp_pinterest_color_hover' ).hide();
		} else {
			$( '#butterbean-control-wpsp_pinterest_color' ).show();
			$( '#butterbean-control-wpsp_pinterest_color_hover' ).show();
		}
	});

	if ( ! $( '#wpsp-love' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_love_color' ).hide();
		$( '#butterbean-control-wpsp_love_color_hover' ).hide();
	}

	$( '#wpsp-love' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_love_color' ).hide();
			$( '#butterbean-control-wpsp_love_color_hover' ).hide();
		} else {
			$( '#butterbean-control-wpsp_love_color' ).show();
			$( '#butterbean-control-wpsp_love_color_hover' ).show();
		}
	});

	// Carousel
	if ( ! $( '#wpsp-carousel' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_carousel_slides' ).hide();
		$( '#butterbean-control-wpsp_carousel_slides_to_scroll' ).hide();
	}

	$( '#wpsp-carousel' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_carousel_slides' ).hide();
			$( '#butterbean-control-wpsp_carousel_slides_to_scroll' ).hide();
		} else {
			$( '#butterbean-control-wpsp_carousel_slides' ).show();
			$( '#butterbean-control-wpsp_carousel_slides_to_scroll' ).show();
		}
	});

	// Cards.
	$( '<div id="wpsp-set-cards"><button style="margin-top: 10px;" class="set-card-styles button">' + wpsp.set_card_style + '</button></div>' ).insertAfter( '#wpsp-cards' );
	$( '<span style="display: inline-block;margin-top: 15px;margin-left: 10px;">' + wpsp.card_style_description + '</span>' ).appendTo( '#wpsp-set-cards' );

	if ( 'none' === $( '#wpsp-cards' ).val() ) {
		$( '#wpsp-set-cards' ).hide();
	}

	$( '#butterbean-control-wpsp_cards .butterbean-label, #butterbean-control-wpsp_cards .butterbean-description' ).attr( 'style', 'display: inline-block !important' );

	var cardValueSlug = $( '#wpsp-cards' ).val().replace( 'wpsp-', '' );
	$( '.set-card-styles' ).attr( 'data-type', cardValueSlug );

	var setDefaults = function() {
		var options = [
			'wpsp_columns',
			'wpsp_columns_gutter',
			'wpsp_content_type',
			'wpsp_date_location',
			'wpsp_image',
			'wpsp_image_alignment',
			'wpsp_include_terms',
			'wpsp_include_author',
			'wpsp_include_date',
			'wpsp_include_comments',
			'wpsp_comments_location',
			'wpsp_read_more_text',
			'wpsp_image_lightbox',
			'wpsp_image_gallery',
			'wpsp_image_overlay_color_static',
			'wpsp_image_overlay_color',
			'wpsp_image_overlay_icon',
			'wpsp_social_sharing',
			'wpsp_social_sharing_alignment',
			'wpsp_twitter',
			'wpsp_twitter_color',
			'wpsp_twitter_color_hover',
			'wpsp_facebook',
			'wpsp_facebook_color',
			'wpsp_facebook_color_hover',
			'wpsp_featured_post',
			'wpsp_image_hover_effect',
			'wpsp_border',
			'wpsp_border_hover',
			'wpsp_background',
			'wpsp_background_hover',
			'wpsp_title_font_size',
			'wpsp_title_color',
			'wpsp_title_color_hover',
			'wpsp_meta_color',
			'wpsp_meta_color_hover',
			'wpsp_text',
			'wpsp_link',
			'wpsp_link_hover',
			'wpsp_padding',
			'wpsp_read_more_background_color',
			'wpsp_read_more_background_color_hover',
			'wpsp_read_more_text_color',
			'wpsp_read_more_text_color_hover',
			'wpsp_read_more_border_color',
			'wpsp_read_more_border_color_hover',
			'wpsp_carousel',
			'wpsp_carousel_slides',
			'wpsp_carousel_slides_to_scroll',
		];

		$.each( options, function( index, value ) {
			var option = $( '*[name="butterbean_wp_show_posts_setting_' + value + '"]' );

			if ( option.is( ':checkbox' ) ) {
				option.prop( 'checked', wpsp.defaults[value] );
			} else {
				option.val( wpsp.defaults[value] );
			}
		} );
	};

	$( '#wpsp-cards' ).on( 'change', function() {
		var cardValueSlug = $( this ).val().replace( 'wpsp-', '' );
		$( '#wpsp-set-cards' ).show();
		$( '.set-card-styles' ).attr( 'data-type', cardValueSlug );
	} );

	$( document ).on( 'click', '.set-card-styles', function( e ) {
		e.preventDefault();

		if ( ! confirm( wpsp.confirm_card_style ) ) {
			return;
		}

		var _this = $( this );

		if ( 'base' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-columns' ).val( 'col-4' );
			$( '#wpsp-padding' ).val( '40px' );
			$( '#wpsp-content-type' ).val( 'excerpt' );

			$( '#wpsp-include-date' ).prop( 'checked', true );
			$( '#wpsp-include-terms' ).prop( 'checked', false );
			$( '#wpsp-include-author' ).prop( 'checked', false );

			$( '#wpsp-date-location' ).val( 'below-title' );

			$( '#wpsp-background' ).val( '#ffffff' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( 'Read more' );
		}

		if ( 'overlap' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-image' ).prop( 'checked', true );
			$( '#wpsp-columns' ).val( 'col-4' );
			$( '#wpsp-padding' ).val( '40px' );
			$( '#wpsp-content-type' ).val( 'none' );

			$( '#wpsp-image-overlay-color-static' ).val( wpsp.defaults.wpsp_image_overlay_color_static );
			$( '#wpsp-image-overlay-color' ).val( wpsp.defaults.wpsp_image_overlay_color );

			$( '#wpsp-include-date' ).prop( 'checked', true );
			$( '#wpsp-include-terms' ).prop( 'checked', true );
			$( '#wpsp-include-author' ).prop( 'checked', true );

			$( '#wpsp-date-location' ).val( 'below-title' );
			$( '#wpsp-terms-location' ).val( 'below-title' );
			$( '#wpsp-author-location' ).val( 'below-post' );

			$( '#wpsp-title-font-size' ).val( '24px' );
			$( '#wpsp-background' ).val( '#ffffff' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( '' );
		}

		if ( 'row' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-columns' ).val( 'col-6' );
			$( '#wpsp-featured-post' ).prop( 'checked', true );
			$( '#wpsp-title-font-size' ).val( '24px' );
			$( '#wpsp-background' ).val( '#ffffff' );
			$( '#wpsp-padding' ).val( '40px' );

			$( '#wpsp-include-terms' ).prop( 'checked', true );
			$( '#wpsp-terms-location' ).val( 'below-post' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( 'Read more' );
		}

		if ( 'polaroid' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-columns' ).val( 'col-12' );
			$( '#wpsp-title-font-size' ).val( '24px' );
			$( '#wpsp-background' ).val( '#ffffff' );
			$( '#wpsp-padding' ).val( '40px' );

			$( '#wpsp-include-date' ).prop( 'checked', true );
			$( '#wpsp-include-terms' ).prop( 'checked', true );
			$( '#wpsp-include-author' ).prop( 'checked', true );

			$( '#wpsp-date-location' ).val( 'below-post' );
			$( '#wpsp-terms-location' ).val( 'below-post' );
			$( '#wpsp-author-location' ).val( 'below-title' );

			$( '#wpsp-social-sharing' ).prop( 'checked', true );
			$( '#wpsp-twitter' ).prop( 'checked', true );
			$( '#wpsp-facebook' ).prop( 'checked', true );
			$( '#wpsp-twitter-color' ).val( '#000000' );
			$( '#wpsp-facebook-color' ).val( '#000000' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( 'Read more' );
		}

		if ( 'overlay' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-columns' ).val( 'col-4' );
			$( '#wpsp-featured-post' ).prop( 'checked', true );
			$( '#wpsp-content-type' ).val( 'none' );
			$( '#wpsp-padding' ).val( '40px' );

			$( '#wpsp-image-overlay-color-static' ).val( 'rgba(0,0,0,0.5)' );
			$( '#wpsp-image-overlay-color' ).val( 'rgba(221,51,51,0.4)' );

			$( '#wpsp-include-date' ).prop( 'checked', true );
			$( '#wpsp-include-terms' ).prop( 'checked', true );
			$( '#wpsp-include-author' ).prop( 'checked', false );

			$( '#wpsp-date-location' ).val( 'below-post' );
			$( '#wpsp-terms-location' ).val( 'below-post' );

			$( '#wpsp-title-color' ).val( '#ffffff' );
			$( '#wpsp-title-font-size' ).val( '24px' );
			$( '#wpsp-meta-color' ).val( '#ffffff' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( '' );
		}

		if ( 'overlay-style-one' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-image' ).prop( 'checked', true );
			$( '#wpsp-columns' ).val( 'col-4' );
			$( '#wpsp-featured-post' ).prop( 'checked', true );
			$( '#wpsp-content-type' ).val( 'excerpt' );
			$( '#wpsp-padding' ).val( '40px' );

			$( '#wpsp-image-overlay-color-static' ).val( '' );
			$( '#wpsp-image-overlay-color' ).val( 'rgba(30,115,190,0.7)' );

			$( '#wpsp-include-date' ).prop( 'checked', true );
			$( '#wpsp-include-terms' ).prop( 'checked', true );
			$( '#wpsp-include-author' ).prop( 'checked', false );

			$( '#wpsp-date-location' ).val( 'below-post' );
			$( '#wpsp-terms-location' ).val( 'below-post' );

			$( '#wpsp-title-color' ).val( '#ffffff' );
			$( '#wpsp-title-font-size' ).val( '24px' );
			$( '#wpsp-meta-color' ).val( '#ffffff' );
			$( '#wpsp-text' ).val( '#ffffff' );

			$( '#wpsp-social-sharing' ).prop( 'checked', true );
			$( '#wpsp-twitter' ).prop( 'checked', true );
			$( '#wpsp-facebook' ).prop( 'checked', true );
			$( '#wpsp-twitter-color' ).val( '#ffffff' );
			$( '#wpsp-twitter-color-hover' ).val( '#ffffff' );
			$( '#wpsp-facebook-color' ).val( '#ffffff' );
			$( '#wpsp-facebook-color-hover' ).val( '#ffffff' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( '' );
		}

		if ( 'overlay-style-two' === _this.data( 'type' ) ) {
			setDefaults();

			$( '#wpsp-image' ).prop( 'checked', true );
			$( '#wpsp-columns' ).val( 'col-4' );
			$( '#wpsp-featured-post' ).prop( 'checked', true );
			$( '#wpsp-content-type' ).val( 'none' );
			$( '#wpsp-padding' ).val( '40px' );

			$( '#wpsp-image-overlay-color-static' ).val( '' );
			$( '#wpsp-image-overlay-color' ).val( 'rgba(0,0,0,0.7)' );

			$( '#wpsp-include-date' ).prop( 'checked', false );
			$( '#wpsp-include-terms' ).prop( 'checked', false );
			$( '#wpsp-include-author' ).prop( 'checked', false );

			$( '#wpsp-author-location' ).val( 'below-post' );

			$( '#wpsp-title-color' ).val( '#ffffff' );
			$( '#wpsp-title-font-size' ).val( '24px' );
			$( '#wpsp-meta-color' ).val( '#ffffff' );
			$( '#wpsp-text' ).val( '#ffffff' );

			$( '#wpsp-read-more-background' ).val( '#ffffff' );
			$( '#wpsp_read_more_background_color_hover' ).val( '#000000' );
			$( '#wpsp_read_more_text_color' ).val( '#000000' );
			$( '#wpsp_read_more_text_color_hover' ).val( '#ffffff' );
			$( '#wpsp_read_more_border_color' ).val( '#ffffff' );
			$( '#wpsp_read_more_border_color_hover' ).val( '#000000' );

			$( 'input[name="butterbean_wp_show_posts_setting_wpsp_read_more_text"]' ).val( 'Read more' );
		}

		if ( 'none' === _this.data( 'type' ) ) {
			setDefaults();
		}
	} );

	// Butterbean prepends each color value with # directly in the template. This removes it.
	$( 'input[data-alpha]' ).each( function() {
		var _this = $( this ),
			val = _this.val();

		if ( val.indexOf( 'rgba' ) >= 0 ) {
			val = val.replace( '#', '' );
		}

		if ( _this.data( 'alpha' ) ) {
			_this.attr( 'maxlength', '' );
		}

		_this.val( val ).trigger( 'change' );
	} );
});
