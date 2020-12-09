jQuery(document).ready(function(jQuery){

	jQuery( '.ninja-forms-mp-page' ).each( function() { 
		if ( jQuery( this ).is( ":visible" ) ) {
			var page = jQuery( this ).attr( 'rel' );
			jQuery( this ).parent().parent().find( "[name='_current_page']" ).val( page );
		}
	});

	jQuery(".ninja-forms-form").on("submitResponse", function(e, response){
		var form_id = response.form_id;
		var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
		if ( typeof mp_settings !== 'undefined' ) {
			return ninja_forms_mp_confirm_error_check( response );
		}
		return true;
	});

	jQuery(".ninja-forms-form").on("submitResponse", function(e, response){
		var form_id = response.form_id;
		var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
		var action = jQuery( document ).data( 'submit_action' );
		if ( typeof mp_settings !== 'undefined' && action == 'submit' ) {
			return ninja_forms_error_change_page( response );
		}
		return true;
	});

	jQuery(".ninja-forms-form").on("beforeSubmit", function(e, formData, jqForm, options){
		// if ( jQuery( document ).data( 'mp_submit' ) == 1 ) {
		// 	jQuery( document ).data( 'submit_action', 'mp_submit' );
		// 	jQuery( document ).data( 'mp_submit', 0 );
		// }
		var form_id = jQuery(jqForm).find("#_form_id").val();
		var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
		if ( typeof mp_settings !== 'undefined' ) {
			return ninja_forms_before_submit_update_progressbar( formData, jqForm, options );
		}
		return true;		
	});

	jQuery(document).on( 'click', '.ninja-forms-mp-confirm-nav', function(e){
		var form_id = ninja_forms_get_form_id( this );
		jQuery("#ninja_forms_form_" + form_id + "_all_fields_wrap").show();
		jQuery("#ninja_forms_form_" + form_id + "_mp_breadcrumbs").show();
		jQuery("#ninja_forms_form_" + form_id + "_progress_bar").show();		
		jQuery("#ninja_forms_form_" + form_id + "_save_progress").show();		

		var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
		js_transition = mp_settings.js_transition;
		if( js_transition == 1 ){
			e.preventDefault();
			var current_page = jQuery("[name='_current_page']").val();
			current_page = parseInt(current_page);
			var page_count = mp_settings.page_count;
			var effect = mp_settings.effect;
			
			var new_page = jQuery(this).attr("rel");
			var dir = '';

			//Check to see if the new page should be shown
			new_page = ninja_forms_mp_page_loop( form_id, new_page, current_page, dir );

			if( current_page != new_page ){
				ninja_forms_mp_change_page( form_id, current_page, new_page, effect );
				ninja_forms_update_progressbar( form_id, new_page );				
			}
		}
		jQuery("#ninja_forms_form_" + form_id + "_confirm_response").remove();
		jQuery("#ninja_forms_form_" + form_id + "_mp_confirm").val(0);
	});	

	jQuery( document ).on( 'click', '.ninja-forms-mp-nav', function( e ){
		//jQuery( document ).data( 'mp_submit', 1 );
		var form_id = ninja_forms_get_form_id(this);

		var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
		js_transition = mp_settings.js_transition;
		if( js_transition == 1 ){
			e.preventDefault();
			var current_page = jQuery("[name='_current_page']").val();
			current_page = parseInt(current_page);
			var page_count = mp_settings.page_count;
			var effect = mp_settings.effect;
			
			if( this.name == '_next' ){
				var new_page = current_page + 1;
				var dir = 'next';
			}else if( this.name == '_prev' ){
				var new_page = current_page - 1;
				var dir = 'prev';
			}else{
				var new_page = jQuery(this).attr("rel");
			}

			//Check to see if the new page should be shown
			new_page = ninja_forms_mp_page_loop( form_id, new_page, current_page, dir );

			if( current_page != new_page ){
				if ( typeof tinyMCE !== 'undefined' ) {
					// Remove any tinyMCE editor         
					for( i in tinyMCE.editors ) {
						if ( typeof tinyMCE.editors[i].id !== 'undefined' ) {
							tinyMCE.editors[i].remove();
						}
					}

				}
				ninja_forms_mp_change_page( form_id, current_page, new_page, effect );
				ninja_forms_update_progressbar( form_id, new_page );				
			}
		}
	});
	
	jQuery(document).on( 'mp_page_change.scroll', function( e, form_id, new_page, old_page ) {
		ninja_forms_init_tinyMCE();
		ninja_forms_scroll_to_top( form_id );
	});

});

function ninja_forms_scroll_to_top( form_id ) {
	jQuery('html, body').animate({
    	scrollTop: jQuery("#ninja_forms_form_" + form_id + "_wrap").offset().top - 300
	}, 1000);
}

function ninja_forms_error_change_page(response){
	var form_id = response.form_id;
	var errors = response.errors;
	if( errors != false && typeof response.errors['confirm-submit'] === 'undefined'){
		var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
		var extras = response.extras;
		var error_page = extras["_current_page"];
		var current_page = jQuery("[name='_current_page']").val();
		current_page = parseInt(current_page);
		var page_count = mp_settings.page_count;
		var effect = mp_settings.effect;

		if( error_page != page_count  ){
			ninja_forms_mp_change_page( form_id, current_page, error_page, effect );
		}
		ninja_forms_update_progressbar( form_id, error_page );
	}

    // Show previously hidden response message in order to show errors
    jQuery("#ninja_forms_form_" + form_id + "_response_msg").css('display', 'block');

	return true;
}

function ninja_forms_mp_change_page( form_id, current_page, new_page, effect ){
	var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
	var effect_direction = mp_settings.direction;
	var page_count = mp_settings.page_count;

	if( current_page > new_page ){
		direction = 'prev';
	}else{
		direction = 'next';
	}

	if( effect_direction == 'ltr' ){
		if( direction == 'next' ){
			var direction_in = 'left';
			var direction_out = 'right';			
		}else{
			var direction_in = 'right';
			var direction_out = 'left';	
		}
	}else if( effect_direction == 'rtl' ){
		if( direction == 'next' ){
			var direction_in = 'right';
			var direction_out = 'left';			
		}else{
			var direction_in = 'left';
			var direction_out = 'right';	
		}
	}else if( effect_direction == 'ttb' ){
		if( direction == 'next' ){
			var direction_in = 'up';
			var direction_out = 'down';			
		}else{
			var direction_in = 'down';
			var direction_out = 'up';	
		}
	}else if( effect_direction == 'btt' ){
		if( direction == 'next' ){
			var direction_in = 'down';
			var direction_out = 'up';			
		}else{
			var direction_in = 'up';
			var direction_out = 'down';	
		}
	}

	jQuery("[name='_current_page']").val(new_page);

	if( new_page == 1 ){
		jQuery("#ninja_forms_form_" + form_id + "_mp_prev").hide();
		jQuery("#ninja_forms_form_" + form_id + "_mp_next").show();
	}else if( new_page < page_count ){
		jQuery("#ninja_forms_form_" + form_id + "_mp_prev").show();
		jQuery("#ninja_forms_form_" + form_id + "_mp_next").show();
	}else{
		jQuery("#ninja_forms_form_" + form_id + "_mp_prev").show();
		jQuery("#ninja_forms_form_" + form_id + "_mp_next").hide();
	}
	

	jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-active").addClass("ninja-forms-form-" + form_id + "-mp-page-list-inactive");
	jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-active").removeClass("ninja-forms-form-" + form_id + "-mp-page-list-active");
	
	jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-inactive[rel=" + new_page + "]").addClass("ninja-forms-form-" + form_id + "-mp-page-list-active");
	jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-inactive[rel=" + new_page + "]").removeClass("ninja-forms-form-" + form_id + "-mp-page-list-inactive");

	jQuery("[name='_mp_page_" + new_page + "']").addClass("ninja-forms-form-" + form_id + "-mp-breadcrumb-active");
	jQuery("[name='_mp_page_" + new_page + "']").addClass("ninja-forms-mp-breadcrumb-active");
	jQuery("[name='_mp_page_" + new_page + "']").removeClass("ninja-forms-form-" + form_id + "-mp-breadcrumb-inactive");
	jQuery("[name='_mp_page_" + new_page + "']").removeClass("ninja-forms-mp-breadcrumb-inactive");

	jQuery("[name='_mp_page_" + current_page + "']").addClass("ninja-forms-form-" + form_id + "-mp-breadcrumb-inactive");
	jQuery("[name='_mp_page_" + current_page + "']").addClass("ninja-forms-mp-breadcrumb-inactive");
	jQuery("[name='_mp_page_" + current_page + "']").removeClass("ninja-forms-form-" + form_id + "-mp-breadcrumb-active");
	jQuery("[name='_mp_page_" + current_page + "']").removeClass("ninja-forms-mp-breadcrumb-active");
	var field_id = jQuery(".ninja-forms-form-" + form_id + "-mp-page-show[rel=" + new_page + "]").prop("id");

	// Hide any response messages we might have.
	jQuery( "#ninja_forms_form_" + form_id + "_response_msg").hide();

	// Disable button clicks.
	jQuery( ":submit" ).attr( 'disabled', true );

   	// run the effect
	jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + current_page).hide( effect, { direction: direction_out }, 300, function(){
		jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + new_page).show( effect, { direction: direction_in }, 200, function() {
			jQuery(document).triggerHandler( 'mp_page_change', [ form_id, new_page, current_page ] );
			// Enable navigation button clicks.
			jQuery( ":submit" ).attr( 'disabled', false );
		});
	});

	ninja_forms_toggle_nav( form_id, field_id );
}

function ninja_forms_before_submit_update_progressbar(formData, jqForm, options){
	var form_id = jQuery(jqForm).prop("id").replace("ninja_forms_form_", "" );
	var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
	js_transition = mp_settings.js_transition
	if ( js_transition == 1 ) {
		var current_page = jQuery("[name='_current_page']").val();
		current_page = parseInt(current_page);
		current_page++;
		ninja_forms_update_progressbar( form_id, current_page );
	}
}

function ninja_forms_update_progressbar( form_id, current_page ){
	var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
	var page_count = mp_settings.page_count;
	if( current_page == 1 ){
		var percent = 0;
	}else if( current_page == ( page_count + 1 ) ){
		percent = 100;
	}else{
		current_page--;
		var percent = current_page / page_count;
		percent = Math.ceil( percent * 100 );		
	}

	jQuery("#ninja_forms_form_" + form_id + "_progress_bar").find("span").css( "width", percent + "%" );
}

function ninja_forms_hide_mp_page( pass, target_field, element ){
	var form_id = ninja_forms_get_form_id(element);
	var page = jQuery("#ninja_forms_field_" + target_field).attr("rel");
	if( pass ){
		// Check to see if we are on the confirmation page. If we are, show or hide the page rather than the breadcrumb.
		if ( jQuery("#mp_confirm_page").val() == 1 ) {
			// This is the confirm page, hide the whole page.
			jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + page).hide();
		} else {
			//Hide the breadcrumb button for this page.
			jQuery("#ninja_forms_field_" + target_field + "_breadcrumb"  ).hide();			
		}

		//Set the page visibility value to hidden, or 0.
		jQuery("#ninja_forms_field_" + target_field).val(0);
	}else{
		// Check to see if we are on the confirmation page. If we are, show or hide the page rather than the breadcrumb.
		if ( jQuery("#mp_confirm_page").val() == 1 ) {
			// This is the confirm page, hide the whole page.
			jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + page).show();
		} else {
			//Show the breadcrumb button for this page.
			jQuery("#ninja_forms_field_" + target_field + "_breadcrumb"  ).show();			
		}
		//Set the page visiblity value to visible, or 1.
		jQuery("#ninja_forms_field_" + target_field).val(1);
	}
	ninja_forms_toggle_nav( form_id, target_field );

}

function ninja_forms_show_mp_page( pass, target_field, element ){
	var form_id = ninja_forms_get_form_id(element);
	var page = jQuery("#ninja_forms_field_" + target_field).attr("rel");
	//console.log('page: ' + page + ' pass: ' + pass );
	if( pass ){
		// Check to see if we are on the confirmation page. If we are, show or hide the page rather than the breadcrumb.
		if ( jQuery("#mp_confirm_page").val() == 1 ) {
			// This is the confirm page, hide the whole page.
			jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + page).show();
		} else {
			//Hide the breadcrumb button for this page.
			jQuery("#ninja_forms_field_" + target_field + "_breadcrumb"  ).show();			
		}
		//Set the page visiblity value to visible, or 1.
		jQuery("#ninja_forms_field_" + target_field).val(1);
	}else{
		// Check to see if we are on the confirmation page. If we are, show or hide the page rather than the breadcrumb.
		if ( jQuery("#mp_confirm_page").val() == 1 ) {
			// This is the confirm page, hide the whole page.
			jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + page).hide();
		} else {
			//Hide the breadcrumb button for this page.
			jQuery("#ninja_forms_field_" + target_field + "_breadcrumb"  ).hide();			
		}
		//Set the page visibility value to hidden, or 0.
		jQuery("#ninja_forms_field_" + target_field).val(0);
	}
	ninja_forms_toggle_nav( form_id, target_field );
}

function ninja_forms_toggle_nav( form_id, target_field ){

	if(jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-inactive").length == 0){
		//Hide both the next and previous buttons
		jQuery("#ninja_forms_form_" + form_id + "_mp_next").hide();
		jQuery("#ninja_forms_form_" + form_id + "_mp_prev").hide();
	}else{
		//Check to see if all the breadcrumbs before this one have been disabled. If they have, remove the previous button.
		if( jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-active").parent().prevAll().find(".ninja-forms-form-" + form_id + "-mp-page-list-inactive[value=1]").length == 0){
			jQuery("#ninja_forms_form_" + form_id + "_mp_prev").hide();
		}else{
			jQuery("#ninja_forms_form_" + form_id + "_mp_prev").show();
		}
		
		//Check to see if all the breadcrumbs after this one have been disabled. If they have, remove the next button.
		if( jQuery(".ninja-forms-form-" + form_id + "-mp-page-list-active").parent().nextAll().find(".ninja-forms-form-" + form_id + "-mp-page-list-inactive[value=1]").length == 0){
			jQuery("#ninja_forms_form_" + form_id + "_mp_next").hide();
		}else{
			jQuery("#ninja_forms_form_" + form_id + "_mp_next").show();
		}
	}
}

//Function to check whether or not a page should be shown.
function ninja_forms_mp_check_page_conditional( form_id, page ){
	if(jQuery(".ninja-forms-form-" + form_id + "-mp-page-show[rel=" + page + "]").val() == 1 ){
		return true;
	}else{
		return false;
	}
}

//Function to set the hidden page visibilty element 1
function ninja_forms_mp_set_page_show( form_id, page ){
	jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + page + "_show").val(1);
}

//Function to set the hidden page visibilty element to 0
function ninja_forms_mp_set_page_hide( form_id, page ){
	jQuery("#ninja_forms_form_" + form_id + "_mp_page_" + page + "_show").val(0);
}


//Function to get the page number by element
function ninja_forms_mp_get_page( element ){
	var page = jQuery(element).closest('.ninja-forms-mp-page').attr("rel");
	return page
}

//Function that loops through all the pages until it finds one that should be shown.
function ninja_forms_mp_page_loop( form_id, new_page, current_page, dir ){
	if( typeof window['ninja_forms_form_' + form_id + '_page_loop'] === 'undefined'){
		window['ninja_forms_form_' + form_id + '_page_loop'] = 1;
	}
	var mp_settings = window['ninja_forms_form_' + form_id + '_mp_settings'];
	var show = ninja_forms_mp_check_page_conditional( form_id, new_page );
	if( !show && window['ninja_forms_form_' + form_id + '_page_loop'] <= mp_settings.page_count ){
		if( new_page == mp_settings.page_count ){
			dir = 'prev';
		}
		//If our new page is less than the page count, increase it by one and check for visibility.
		if( mp_settings.page_count > 1 ){
			if( dir == 'next' ){
				if( new_page < mp_settings.page_count ){
					current_page++;
					new_page++;
				}
			}else{
				current_page--;
				new_page = current_page;
			}	
			window['ninja_forms_form_' + form_id + '_page_loop']++;
			//This page shouldn't be shown, so loop through the rest of the pages until we find one that should be shown.
			new_page = ninja_forms_mp_page_loop( form_id, new_page, current_page, dir );

		}else{
			new_page = 1;
		}

	}

	window['ninja_forms_form_' + form_id + '_page_loop'] = 1;

	return new_page;
}

function ninja_forms_mp_confirm_error_check(response){
	if(typeof response.errors['confirm-submit'] !== 'undefined'){
		jQuery("#ninja_forms_form_" + response.form_id + "_all_fields_wrap").remove();
		jQuery("#ninja_forms_form_" + response.form_id + "_mp_breadcrumbs").hide();
		jQuery("#ninja_forms_form_" + response.form_id + "_progress_bar").hide();
		jQuery("#ninja_forms_form_" + response.form_id + "_save_progress").hide();
		jQuery("#ninja_forms_form_" + response.form_id + "_mp_nav_wrap").hide();
		jQuery("#ninja_forms_form_" + response.form_id + "_mp_confirm").val(1);
		jQuery("#ninja_forms_form_" + response.form_id).prepend('<div id="ninja_forms_form_' + response.form_id + '_confirm_response">' + response.errors['confirm-submit-msg']['msg'] + '</div>');
		ninja_forms_scroll_to_top( response.form_id );
	}
	return true;
}

function ninja_forms_init_tinyMCE() {
	if ( typeof tinyMCE !== 'undefined' ) {
		// Remove any tinyMCE editors
		var init, edId, qtId, firstInit, wrapper;
			if ( typeof tinyMCEPreInit !== 'undefined' ) {
				for ( edId in tinyMCEPreInit.mceInit ) {
					if ( firstInit ) {
						init = tinyMCEPreInit.mceInit[edId] = tinymce.extend( {}, firstInit, tinyMCEPreInit.mceInit[edId] );
					} else {
						init = firstInit = tinyMCEPreInit.mceInit[edId];
					}

					wrapper = tinymce.DOM.select( '#wp-' + edId + '-wrap' )[0];

					if ( ( tinymce.DOM.hasClass( wrapper, 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( edId ) ) &&
						! init.wp_skip_init ) {

						try {
							tinymce.init( init );

							if ( ! window.wpActiveEditor ) {
								window.wpActiveEditor = edId;
							}
						} catch(e){}
					}
				}
			}

			if ( typeof quicktags !== 'undefined' ) {
				for ( qtId in tinyMCEPreInit.qtInit ) {
					try {
						quicktags( tinyMCEPreInit.qtInit[qtId] );

						if ( ! window.wpActiveEditor ) {
							window.wpActiveEditor = qtId;
						}
					} catch(e){};
				}
			}

			if ( typeof jQuery !== 'undefined' ) {
				jQuery('.wp-editor-wrap').on( 'click.wp-editor', function() {
					if ( this.id ) {
						window.wpActiveEditor = this.id.slice( 3, -5 );
					}
				});
			} else {
				for ( qtId in tinyMCEPreInit.qtInit ) {
					document.getElementById( 'wp-' + qtId + '-wrap' ).onclick = function() {
						window.wpActiveEditor = this.id.slice( 3, -5 );
					}
				}
			}
		
	}
}