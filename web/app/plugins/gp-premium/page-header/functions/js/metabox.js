jQuery(document).ready(function($) {
	$( '#generate-tabs-container input[type="checkbox"]' ).lc_switch( '', '' );

	$( '.generate-tabs-menu a' ).on( 'click', function( event ) {
		event.preventDefault();
		$( this ).parent().addClass( 'generate-current' );
		$( this ).parent().siblings().removeClass( 'generate-current' );
		var tab = $( this ).attr( 'href' );
		$( '.generate-tab-content' ).not(tab).css( 'display', 'none' );
		$( tab ).fadeIn( 100 );

		if ( '#generate-image-tab' == tab || '#generate-content-tab' == tab ) {
			$( '.show-in-excerpt' ).show();
		} else {
			$( '.show-in-excerpt' ).hide();
		}
	} );

	$( '#_meta-generate-page-header-content' ).on( 'input change', function() {
		if ( this.value.length ) {
			$( '.page-header-content-required' ).hide();
		} else {
			$( '.page-header-content-required' ).show();
		}
	});
});

jQuery(window).on('load', function() {
	if ( jQuery( '#_meta-generate-page-header-enable-image-crop' ).val() == 'enable' ) {
		jQuery( '#crop-enabled' ).show();
	}

	jQuery( '#_meta-generate-page-header-enable-image-crop' ).change(function () {
		if ( jQuery( this ).val() === 'enable' ) {
			jQuery( '#crop-enabled' ).show();
		} else {
			jQuery( '#crop-enabled' ).hide();
		}
	});

	if ( jQuery( '#_meta-generate-page-header-image-background' ).is( ':checked' ) ) {
		jQuery( '.parallax' ).show();
	} else {
		jQuery( '.parallax' ).hide();
	}

	jQuery('body').delegate('.image-background', 'lcs-statuschange', function() {
		if (jQuery(this).is(":checked")) {
			jQuery('.parallax').show();
		} else {
			jQuery('.parallax').hide();
			jQuery('#_meta-generate-page-header-image-background-fixed').lcs_off();
		}
	});

	if ( jQuery('#_meta-generate-page-header-full-screen').is(':checked')) {
		jQuery('.vertical-center').show();
	} else {
		jQuery('.vertical-center').hide();
	}

	jQuery('body').delegate('#_meta-generate-page-header-full-screen', 'lcs-statuschange', function() {
		if (jQuery(this).is(":checked")) {
			jQuery('.vertical-center').show();
		} else {
			jQuery('.vertical-center').hide();
			jQuery('#_meta-generate-page-header-vertical-center').lcs_off();
		}
	});

	if ( jQuery('#_meta-generate-page-header-transparent-navigation').is(':checked')) {
		jQuery('.navigation-colors').show();
	} else {
		jQuery('.navigation-colors').hide();
	}

	jQuery('body').delegate('#_meta-generate-page-header-transparent-navigation', 'lcs-statuschange', function() {
		if (jQuery(this).is(":checked")) {
			jQuery('.navigation-colors').show();
		} else {
			jQuery('.navigation-colors').hide();
		}
	});

	if ( jQuery('#_meta-generate-page-header-combine').is(':checked')) {
		jQuery('.combination-options').show();
	} else {
		jQuery('.combination-options').hide();
	}

	jQuery('body').delegate('#_meta-generate-page-header-combine', 'lcs-statuschange', function() {
		if (jQuery(this).is(":checked")) {
			jQuery('.combination-options').show();
		} else {
			jQuery('.combination-options').hide();
		}
	});

	if ( jQuery('#_meta-generate-page-header-image-background-type').val() == '' ) {
		jQuery('.vertical-center').hide();
		jQuery('.fullscreen').hide();
	}
	jQuery('#_meta-generate-page-header-image-background-type').change(function () {
		if (jQuery(this).val() === '') {
			jQuery('.vertical-center').hide();
			jQuery('#_meta-generate-page-header-vertical-center').lcs_off();
			jQuery('.fullscreen').hide();
			jQuery('#_meta-generate-page-header-full-screen').lcs_off();
		} else {
			//jQuery('.vertical-center').show();
			jQuery('.fullscreen').show();
		}
	});

	var $set_button = jQuery('.generate-upload-file');
	/**
	 * open the media manager
	 */
	$set_button.click(function (e) {
		e.preventDefault();

		var $thisbutton = jQuery(this);
		var frame = wp.media({
			title : $thisbutton.data('title'),
			multiple : false,
			library : { type : $thisbutton.data('type') },
			button : { text : $thisbutton.data('insert') }
		});
		// close event media manager
		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			// set the file
			//set_dfi(attachment.url);
			$thisbutton.prev('input').val(attachment.url);
			$thisbutton.nextAll('input.image-id').val(attachment.id);
			if ( $thisbutton.data('prev') === true ) {
				$thisbutton.prev('input').prevAll('#preview-image').children('.saved-image').remove();
				$thisbutton.prev('input').prevAll('#preview-image').append('<img src="' + attachment.url + '" width="100" class="saved-image" style="margin-bottom:12px;" />');
			}
			$thisbutton.nextAll( '.generate-page-header-remove-image' ).show();
			if ( 'upload_image' == $thisbutton.prev( 'input' ).attr( 'id' ) ) {
				jQuery( '.featured-image-message' ).hide();
				jQuery( '.page-header-image-settings' ).show();
				jQuery( '.generate-page-header-set-featured-image' ).hide();
			}
		});

		// everthing is set open the media manager
		frame.open();
	});
});
jQuery(document).ready(function($) {
	$('#generate-tabs-container .color-picker').wpColorPicker();

	jQuery( '.generate-page-header-remove-image' ).on( 'click', function( e ) {
		e.preventDefault();
		var input = jQuery( this ).data( 'input' );
		var input_id = jQuery( this ).data( 'input-id' );
		var preview = jQuery( this ).data( 'prev' );
		jQuery( input ).attr( 'value', '' );
		jQuery( input_id ).attr( 'value', '' );
		jQuery( preview ).children( '.saved-image' ).remove();
		jQuery( this ).hide();
		if ( '-1' == jQuery( '#_thumbnail_id' ).attr( 'value' ) ) {
			jQuery( '.page-header-image-settings' ).hide();
			jQuery( '.generate-page-header-set-featured-image' ).show();

		} else {
			jQuery( '.generate-page-header-set-featured-image' ).hide();
			jQuery( '.page-header-image-settings' ).show();
			jQuery( '.featured-image-message' ).show();
		}
		return false;
	});

	$('#postimagediv').on( 'click', '#remove-post-thumbnail', function() {
		// The featured image is gone, so we can hide the message
		jQuery( '.featured-image-message' ).hide();

		// If there's no other image set, we can hide the image settings
		if ( '' == jQuery( '#_meta-generate-page-header-image-id' ).attr( 'value' ) ) {
			jQuery( '.page-header-image-settings' ).hide();
			jQuery( '.generate-page-header-set-featured-image' ).show();
		}

		// No more featured image means we can show the 'show excerpt' option
		jQuery( '.show-in-excerpt' ).show();

		return false;
	});

	wp.media.featuredImage.frame().on('select', function() {

		// We have a featured image, so the 'show excerpt' function isn't necessary
		jQuery( '.show-in-excerpt' ).hide();

		// We can stop here if we have a custom image set
		if ( '' !== jQuery( '#_meta-generate-page-header-image-id' ).attr( 'value' ) )
			return;

		// Hide the set your featured image message
		jQuery( '.generate-page-header-set-featured-image' ).hide();

		// Show the "using feaured image" message
		jQuery( '.featured-image-message' ).show();

		// Show the image settings (image link, resizing etc..)
		jQuery( '.page-header-image-settings' ).show();
	});

	$( '.generate-featured-image, .generate-page-header-set-featured-image a' ).on( 'click', function( event ) {
		event.preventDefault();

		// Stop propagation to prevent thickbox from activating.
		event.stopPropagation();

		// Open the featured image modal
		wp.media.featuredImage.frame().open();
	});
});
