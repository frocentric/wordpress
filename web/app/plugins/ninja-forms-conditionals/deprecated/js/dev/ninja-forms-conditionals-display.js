// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};

jQuery(document).ready(function(jQuery) {
	
	/* * * Begin Conditional Logic JS * * */
	jQuery(document).on( 'change', '.ninja-forms-field-conditional-listen', function(){
		ninja_forms_check_conditional(this, true);
	});	
	
	jQuery(document).on( 'keyup', '.ninja-forms-field-conditional-listen', function(){
		ninja_forms_check_conditional(this, true);
	});

	/*
	Prevent submit on enter if the submit button is hidden.
	 */
	jQuery( ".ninja-forms-form" ).on( 'beforeSubmit.clPreventSubmit', function( e, formData, jqForm, options ) {
		var form_id = jQuery( jqForm ).prop( 'id' ).replace( 'ninja_forms_form_', '' );
		if ( true != jQuery( '#nf_submit_' + form_id ).parent().data( 'visible' ) ) {
			jQuery( '#nf_processing_' + form_id ).hide();
			jQuery( '#nf_submit_' + form_id ).show();
			return false;
		}
	} );

	/* * * End Conditional Logic JS * * */

});
function ninja_forms_check_conditional(element, action_value){
	
	var form_id = ninja_forms_get_form_id(element);
	var conditional = eval( 'ninja_forms_form_' + form_id + '_conditionals_settings' );
	conditional = conditional.conditionals;
	
	var field_id = jQuery(element).attr("rel");
	for(field in conditional){

		var target_field = field.replace("field_", "");
		var conditional_length = jQuery(conditional[field]['conditional']).length;
		for (i = 0; i < conditional_length; i++){
			if ( typeof conditional[field]['conditional'][i] !== 'undefined' ) {
				var cr_length = jQuery(conditional[field]['conditional'][i]['cr']).length;
				for (x = 0; x < cr_length; x++){
					if ( typeof conditional[field]['conditional'][i] !== 'undefined' ) {
						if(conditional[field]['conditional'][i]['cr'][x]['field'] == field_id){
							var action_value = conditional[field]['conditional'][i]['cr'][x]['value'];
							ninja_forms_conditional_change(element, target_field, action_value); //target_field, change value?
						}								
					}
				}				
			}
		}
	}
}

function ninja_forms_conditional_change(element, target_field, action_value){
	var form_id = ninja_forms_get_form_id(element);
	var conditional = eval( 'ninja_forms_form_' + form_id + '_conditionals_settings' );
	conditional = conditional.conditionals;

	var cond = conditional["field_" + target_field]['conditional'];
	conditional_length = jQuery(cond).length;
	var pass_array = new Array();
	var value_array = new Array();
	// We need to check our "actions" to make sure that if multiple actions are added with different conditions, any evaluating to true will fire.
	var action_pass = new Object();

	for (i = 0; i < conditional_length; i++){
		var connector = cond[i]['connector'];
		var cr_row = cond[i]['cr'];
		value_array[i] = cond[i]['value'];
		cr_length = jQuery(cr_row).length;
		var action = cond[i]['action'];

		if(connector == 'and'){
			pass_array[i] = true;
		}else if(connector == 'or'){
			pass_array[i] = false;				
		}

		for (x = 0; x < cr_length; x++){

			cr_field = cr_row[x]['field'];
			cr_operator = cr_row[x]['operator'];
			cr_value = cr_row[x]['value'];
			cr_type = jQuery("#ninja_forms_field_" + cr_field + "_type").val();
			cr_visible = jQuery("#ninja_forms_field_" + cr_field + "_div_wrap").data("visible");
			if(cr_type == 'list'){
				// We are either dealing with a checkbox or radio list.
				if(jQuery("#ninja_forms_field_" + cr_field + "_list_type").val() == "checkbox" ){ //This is a checkbox list.
					if(jQuery(".ninja_forms_field_" + cr_field + "[value='" + cr_value + "']").prop("checked")){
						var field_value = cr_value;
					}else{
						var field_value = '';
					}
					jQuery(".ninja_forms_field_" + cr_field + "[value='" + cr_value + "']").each( function(){
						if(!cr_visible){
							//cr_visible = jQuery(this).is(":visible");
						}
					});
				}else if( jQuery("#ninja_forms_field_" + cr_field + "_list_type").val() == "radio" ){ //This is a radio list.
					var field_value = jQuery("input[name='ninja_forms_field_" + cr_field + "']:checked").val();
					jQuery("input[name='ninja_forms_field_" + cr_field + "']").each( function(){
						if(!cr_visible){
							//cr_visible = jQuery(this).is(":visible");
						}
					});
				}else{
					field_value = jQuery("#ninja_forms_field_" + cr_field).val(); // This is a dropdown list.
					//cr_visible = jQuery("#ninja_forms_field_" + cr_field).is(":visible");
				}
			}else if(cr_type == 'checkbox'){
				if(jQuery("#ninja_forms_field_" + cr_field).prop("checked")){
					var field_value = 'checked';
				}else{
					var field_value = 'unchecked';
				}
				//cr_visible = jQuery("#ninja_forms_field_" + cr_field).is(":visible");
			}else{
				field_value = jQuery("#ninja_forms_field_" + cr_field).val();
				//cr_visible = jQuery("#ninja_forms_field_" + cr_field).is(":visible");
			}

            if(is_numeric(field_value)){
                field_value = ( field_value % 1 === 0 ) ? parseInt(field_value) : parseFloat(field_value);
            }

            if(is_numeric(cr_value)){
                cr_value = ( cr_value % 1 === 0 ) ? parseInt(cr_value) : parseFloat(cr_value);
            }

			var tmp = ninja_forms_conditional_compare(field_value, cr_value, cr_operator);

			if( cr_visible != 1 ){
				tmp = false;
			}

			if(connector == 'and'){
				if(!tmp){
					pass_array[i] = false;
				}
			}else if(connector == 'or'){
				if(tmp){
					pass_array[i] = true;
				}
			}

		}

		if ( typeof action_pass[action] === 'undefined' ) {
			action_pass[action] = new Object();
		}

		if ( action == 'add_value' ) {
			var value = value_array[i];
						
			if(typeof value.value === "undefined" || value.value == "_ninja_forms_no_value"){
				value.value = value.label;
			}
			action_pass[action][value.value] = pass_array[i];
		} else {
			if ( typeof action_pass[action][cond[i]['value']] === 'undefined' || action_pass[action][cond[i]['value']] === false ) {
				if ( pass_array[i] ) {
					action_pass[action][cond[i]['value']] = true;
				} else {
					action_pass[action][cond[i]['value']] = false;
				}
			}			
		}
	}

	for (i = 0; i < conditional_length; i++){
		if ( typeof cond[i] === 'undefined' ) continue;
		var action = cond[i]['action'];
		value = value_array[i];
		if ( action == 'add_value' ) {
			if(typeof value.value === "undefined" || value.value == "_ninja_forms_no_value"){
				value.value = value.label;
			}
			pass = action_pass[action][value.value];
		} else {
			pass = action_pass[action][value];
		}
		
		var input_type = jQuery("#ninja_forms_field_" + target_field + "_type").val();
		var list_type = '';
		var list = false;
		if(input_type == "list"){
			input_type = jQuery("#ninja_forms_field_" + target_field + "_list_type").val();
			list_type = input_type;
			list = true;
		}
		
		if(action == 'show'){
			if(pass){

				var was_visible = jQuery( "#ninja_forms_field_" + target_field + "_div_wrap" ).data( "visible" );
				//var was_visible = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").is(":visible");
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").show(10, function(e){ jQuery(document).triggerHandler('ninja_forms_conditional_show'); });
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").data("visible", true);
				if ( list ) {
					if ( input_type == 'checkbox' || input_type == 'radio' ) {
						var target_element = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field:visible:first");
					} else {
						var target_element = jQuery("#ninja_forms_field_" + target_field);
					}
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}

				if ( !was_visible ) {
					// Check to see if we're working with a field that's listening for a calculation.
					if ( jQuery( target_element ).hasClass("ninja-forms-field-calc-listen") ) {
						// Since we are going to be hiding a field upon which a calculation is based, we need to set the oldValue of our calculation to the current field's value.
						jQuery(target_element).data( "oldValue", '' );
						// Now we need to prevent the value from being re-added.
						// If we're working with a list, target every input
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).removeClass('ninja-forms-field-calc-no-new-op');
								});
						} else {
							jQuery(target_element).removeClass('ninja-forms-field-calc-no-new-op');
						}
					}
					
					if ( jQuery( target_element ).attr('type') != 'file' ) {
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).change();
									jQuery(this).removeClass('ninja-forms-field-calc-no-new-op');
								});
						} else {
							jQuery(target_element).change();
							jQuery(target_element).removeClass('ninja-forms-field-calc-no-new-op');
						}
						
					}
				}
				
			}else{
				if ( list ) {
					if ( input_type == 'checkbox' || input_type == 'radio' ) {
						var target_element = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field:visible:first");
					} else {
						var target_element = jQuery("#ninja_forms_field_" + target_field);
					}
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}
				var was_visible = jQuery( "#ninja_forms_field_" + target_field + "_div_wrap" ).data( "visible" );
				//var was_visible = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").is(":visible");
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").hide(10, function(e){ jQuery(document).triggerHandler('ninja_forms_conditional_hide'); });
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").data("visible", false);
				if ( was_visible ) {
					// Check to see if we're working with a field that's listening for a calculation.
					if ( jQuery( target_element ).hasClass("ninja-forms-field-calc-listen") ) {
						// Since we are going to be hiding a field upon which a calculation is based, we need to set the oldValue of our calculation to the current field's value.
						jQuery(target_element).data( "oldValue", jQuery(target_element).val() );
						// Now we need to prevent the value from being re-added.
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).addClass('ninja-forms-field-calc-no-new-op');
								});
						} else {
							jQuery(target_element).addClass('ninja-forms-field-calc-no-new-op');
						}
						
					}

					if ( jQuery( target_element ).attr('type') != 'file' ) {
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).change();
									jQuery(this).addClass('ninja-forms-field-calc-no-old-op');
								});
						} else {
							jQuery(target_element).change();
							jQuery(target_element).addClass('ninja-forms-field-calc-no-old-op');
						}
					}
				}
			}

		}else if(action == 'hide'){
			if(pass){
				if ( list ) {
					if ( input_type == 'checkbox' || input_type == 'radio' ) {
						var target_element = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field:visible:first");
					} else {
						var target_element = jQuery("#ninja_forms_field_" + target_field);
					}
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}
				var was_visible = jQuery( "#ninja_forms_field_" + target_field + "_div_wrap" ).data( "visible" );
				//var was_visible = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").is(":visible");
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").hide();
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").data("visible", false);
				if ( was_visible ) {
					// Check to see if we're working with a field that's listening for a calculation.
					if ( jQuery( target_element ).hasClass("ninja-forms-field-calc-listen") ) {
						// Since we are going to be hiding a field upon which a calculation is based, we need to set the oldValue of our calculation to the current field's value.
						jQuery(target_element).data( "oldValue", jQuery(target_element).val() );
						// Now we need to prevent the value from being re-added.
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).addClass('ninja-forms-field-calc-no-new-op');
								});
						} else {
							jQuery(target_element).addClass('ninja-forms-field-calc-no-new-op');
						}
						
					}
					
					if ( jQuery( target_element ).attr('type') != 'file' ) {
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).change();
								});
						} else {
							jQuery(target_element).change();
						}
					}
				}
			}else{
				var was_visible = jQuery( "#ninja_forms_field_" + target_field + "_div_wrap" ).data( "visible" );
				//var was_visible = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").is(":visible");
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").show();
				jQuery("#ninja_forms_field_" + target_field + "_div_wrap").data("visible", true);
				if ( list ) {
					if ( input_type == 'checkbox' || input_type == 'radio' ) {
						var target_element = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field:visible:first");
					} else {
						var target_element = jQuery("#ninja_forms_field_" + target_field);
					}
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}
				
				if ( !was_visible ) {
					// Check to see if we're working with a field that's listening for a calculation.
					if ( jQuery( target_element ).hasClass("ninja-forms-field-calc-listen") ) {
						// Since we are going to be hiding a field upon which a calculation is based, we need to set the oldValue of our calculation to the current field's value.
						jQuery(target_element).data( "oldValue", '' );
						// Now we need to prevent the value from being re-added.
						// If we're working with a list, target every input
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).removeClass('ninja-forms-field-calc-no-new-op');
								});
						} else {
							jQuery(target_element).removeClass('ninja-forms-field-calc-no-new-op');
						}
					}
					
					if ( jQuery( target_element ).attr('type') != 'file' ) {
						if ( list && ( input_type == 'checkbox' || input_type == 'radio' ) ) {
								jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field").each(function(){
									jQuery(this).change();
									jQuery(this).removeClass('ninja-forms-field-calc-no-new-op');
								});
						} else {
							jQuery(target_element).change();
							jQuery(target_element).removeClass('ninja-forms-field-calc-no-new-op');
						}
						
					}
				}
			}
		}else if(action == 'change_value'){

			var was_checked = jQuery( "#ninja_forms_field_" + target_field + "_div_wrap" ).data( "checked" );
						
			if(input_type == 'checkbox'){
				if ( list ) {
					var checked_now = jQuery("[name='ninja_forms_field_" + target_field + "\\[\\]'][value='" + value + "']").prop("checked");
					if(pass){
						jQuery("[name='ninja_forms_field_" + target_field + "\\[\\]'][value='" + value + "']").prop("checked", true);
                        console.log("test");
					}else{
						jQuery("[name='ninja_forms_field_" + target_field + "\\[\\]'][value='" + value + "']").prop("checked", false);
					}
				} else {
					var checked_now = jQuery("#ninja_forms_field_" + target_field).prop("checked");
					if( pass ){
						if(value == 'checked'){
							jQuery("#ninja_forms_field_" + target_field).prop("checked", true);
						}else if(value == 'unchecked'){
							jQuery("#ninja_forms_field_" + target_field).prop("checked", false);					
						}
                        // Manually trigger change
                        jQuery("#ninja_forms_field_" + target_field).trigger( "change" );
					}						
				}

			}else if(input_type == 'radio'){
				if(pass){
					jQuery("[name='ninja_forms_field_" + target_field + "'][value='" + value + "']").prop("checked", true);
				}else{
					jQuery("[name='ninja_forms_field_" + target_field + "'][value='" + value + "']").prop("checked", false);
				}
				
			}else{
				if(pass){
					jQuery("#ninja_forms_field_" + target_field).val(value);
				}
			}

			if ( list ) {
				if ( input_type == 'checkbox' || input_type == 'radio' ) {
					var target_element = jQuery("[name='ninja_forms_field_" + target_field + "\\[\\]'][value='" + value + "']");
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}
			} else {
				var target_element = jQuery("#ninja_forms_field_" + target_field);
				
			}

			jQuery( "#ninja_forms_field_" + target_field + "_div_wrap" ).data( "checked", checked_now );

			if ( i == conditional_length - 1 ) {
				jQuery( target_element ).change();
			}
			
			
		}else if(action == 'remove_value'){
			if(input_type == 'dropdown'){
				if(pass){
					var selected_var = jQuery("#ninja_forms_field_" + target_field).val();
					if(selected_var == value){
						var next_val = jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").next().val();
						jQuery("#ninja_forms_field_" + target_field).val(next_val);
					}
					jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").hide();
					jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").attr("disabled", true);
				}else{
					jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").show();
					jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").attr("disabled", false);
				}
			}else if(input_type == 'multi'){
				if(pass){
					var selected_var = jQuery("#ninja_forms_field_" + target_field).val();
					if(!!selected_var){
						var index = selected_var.indexOf(value);
						if(index != -1){
							selected_var.splice(index, 1);
							jQuery("#ninja_forms_field_" + target_field).val(selected_var);
						}
					}
					var opt_index = jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").prop("index");
					var clone = jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").clone();
					jQuery(clone).attr("title", opt_index);
					jQuery("#ninja_forms_field_" + target_field + "_clone").append(clone);
					jQuery("#ninja_forms_field_" + target_field + " option[value='" + value + "']").remove();

				}else{
					var clone = jQuery("#ninja_forms_field_" + target_field + "_clone option[value='" + value + "']");
					var opt_index = jQuery(clone).attr("title");
					opt_index++;
					var selected_var = jQuery("#ninja_forms_field_" + target_field).val();
					jQuery("#ninja_forms_field_" + target_field + " option:nth-child(" + opt_index + ")").before(clone);
					jQuery("#ninja_forms_field_" + target_field).val(selected_var);
				}
			}else if(input_type == 'checkbox' || input_type == 'radio'){
				if(pass){
					jQuery("input[name^=ninja_forms_field_" + target_field + "][value='" + value + "']").attr("checked", false);
					jQuery("input[name^=ninja_forms_field_" + target_field + "][value='" + value + "']").parent().hide();
				}else{
					jQuery("input[name^=ninja_forms_field_" + target_field + "][value='" + value + "']").parent().show();
				}
			}
			if ( list ) {
				if ( input_type == 'checkbox' || input_type == 'radio' ) {
					var target_element = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field:visible:first");
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}
			} else {
				var target_element = jQuery("#ninja_forms_field_" + target_field);
			}
			//jQuery(target_element).change();
		}else if(action == 'add_value'){
			if( typeof value !== "undefined" ){
				if(typeof value.value === "undefined" || value.value == "_ninja_forms_no_value"){
					value.value = value.label;
				}
				var form_id = ninja_forms_get_form_id( jQuery("#ninja_forms_field_" + target_field ) );
				if ( typeof window['ninja_forms_form_' + form_id + '_calc_settings'].calc_value[ target_field ] !== 'undefined' ) {
					window['ninja_forms_form_' + form_id + '_calc_settings'].calc_value[ target_field ][ value.value ] = value.calc;
				}
				
				if(input_type == "dropdown" || input_type == "multi"){
					if(pass){
						var current_count = jQuery("#ninja_forms_field_" + target_field + " option[value='" + value.value + "']").length;
						if(current_count == 0){
							jQuery("#ninja_forms_field_" + target_field).append("<option value='" + value.value + "'>" + value.label + "</option>");
						}
					}else{
						jQuery("#ninja_forms_field_" + target_field + " option[value='" + value.value + "']").remove();
					}
				}else if(input_type == "checkbox" || input_type == "radio"){
					if(pass){
						var current_count = jQuery("input[name^=ninja_forms_field_" + target_field + "][value='" + value.value + "']").length;
						if(current_count == 0){
							var clone = jQuery("#ninja_forms_field_" + target_field + "_template").clone();
							var count = jQuery(".ninja-forms-field-" + target_field + "-options").length;
							var label_id = jQuery(clone).prop("id").replace("template", count);
							jQuery(clone).prop("id", label_id);
							var checkbox_id = jQuery(clone).find(":checkbox").prop("id") + count;
							if(input_type == "checkbox"){
								var checkbox_name = "ninja_forms_field_" + target_field + "[]";
							}else{
								var checkbox_name = "ninja_forms_field_" + target_field;
							}
							
							jQuery(clone).find(":" + input_type).prop("id", checkbox_id);
							jQuery(clone).find(":" + input_type).attr("name", checkbox_name);
							jQuery(clone).find(":" + input_type).val(value.value);
							jQuery(clone).find(":" + input_type).after(value.label);
							jQuery(clone).attr("style", "");

							jQuery("#ninja_forms_field_" + target_field + "_options_span").find("ul").append(clone);
						}
					}else{
						jQuery("input[name^=ninja_forms_field_" + target_field + "][value='" + value.value + "']").parent().remove();
					}
				}
			}
			if ( list ) {
				if ( input_type == 'checkbox' || input_type == 'radio' ) {
					var target_element = jQuery("#ninja_forms_field_" + target_field + "_div_wrap").find(".ninja-forms-field:visible:first");
				} else {
					var target_element = jQuery("#ninja_forms_field_" + target_field);
				}
			} else {
				var target_element = jQuery("#ninja_forms_field_" + target_field);
			}
			//jQuery(target_element).change();
		}else{
			//Put code here to call javascript function.
			pass = pass_array[i];
			result = window[action](pass, target_field, element);
		}
	}
}

function ninja_forms_conditional_compare(param1, param2, op){
	
	switch(op) {
		case "==":
			return param1 == param2;
		case "!=":
			return param1 != param2;
		case "<":
			return param1 < param2;
		case ">":
			return param1 > param2;
	}
}

function is_numeric (mixed_var) {
  return (typeof(mixed_var) === 'number' || typeof(mixed_var) === 'string') && mixed_var !== '' && !isNaN(mixed_var);
}