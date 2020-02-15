
var section_word2tax_waiting = new Array();
var section_word2tax_pending_callbacks = new Array();
function section_word2tax_get_terms_from_tax(tax, obj, callback, selected_term) {
	if (tax == '-1') {
		return new Array();
	}

	if (wpepro_word2tax_obj.post_types_terms[tax] != undefined && wpepro_word2tax_obj.post_types_terms[tax].length > 0) {
		return wpepro_word2tax_obj.post_types_terms[tax];
	}
	if (section_word2tax_pending_callbacks[tax] == undefined) {
		section_word2tax_pending_callbacks[tax] = new Array();
	}
	section_word2tax_pending_callbacks[tax].push( new Array(callback, obj, selected_term) );
	if (section_word2tax_waiting[tax] !== undefined) {
    	return false;
	}
	section_word2tax_waiting[tax] = true;

	jQuery.ajax({
		url: ajaxurl, // this is a variable that WordPress has already defined for us
		type: 'POST',
		data: {
			action: 'wpepro_word2tax_terms', // this is the name of our WP AJAX function that we'll set up next
			tax: tax,
			nonce: wpepro_word2tax_obj.get_terms_nonce
		}
	}).done(function( data ) {
    	//console.log(data);
    	if (data.success) {
    		wpepro_word2tax_obj.post_types_terms[tax] = data.data;
    		if (section_word2tax_pending_callbacks[tax] != undefined) {
    			for (var key in section_word2tax_pending_callbacks[tax]) {
    				section_word2tax_pending_callbacks[tax][key][0](section_word2tax_pending_callbacks[tax][key][1], section_word2tax_pending_callbacks[tax][key][2]);
    			}
    		}
    		return true;
    	} else {
    		alert(data.data.message);
    	}
  	});
  	return false;
}

function section_word2tax_on_change_tax(obj, selected_term = '-1') {
	
	
	var id = jQuery(obj).attr('id').replace("section_word2tax_tax_", "");
	var selected_tax = jQuery(obj).val();

	var options_terms =  section_word2tax_get_terms_from_tax(selected_tax, obj, section_word2tax_on_change_tax, selected_tax);
	
	if (options_terms === false) {
		var options_select_term = '<option value="-1">Loading... </option>';
	} else {
		var options_select_term = '<option value="-1">' + wpepro_word2tax_obj.text_select_term + '</option>';
		for (var key in options_terms) {
			options_select_term += '<option value="' + options_terms[key].term_id + '" ' + (selected_term == options_terms[key].term_id ? 'selected ' : '') + ' >' + options_terms[key].name + '</option>';
		}
		
	}
	
	jQuery('#section_word2tax_term_' + id).html(options_select_term);

}

function section_word2tax_events_rows() {
	jQuery('.btn_delete_w2t').click(function (e) {
		jQuery(this).parent().parent().parent().remove();
		e.preventDefault();
	});

	jQuery('.word2tax_post').change(function () {
		var id = jQuery(this).attr('id').replace("section_word2tax_post_", "");
		var selected_post = jQuery(this).val();

		var options_select_tax = '';
		var selected_tax = '-1';
		options_select_tax = '<option value="-1">' + wpepro_word2tax_obj.text_select_tax + '</option>';
		for (var key in wpepro_word2tax_obj.post_types_tax[selected_post]) {
			options_select_tax += '<option value="' + wpepro_word2tax_obj.post_types_tax[selected_post][key] + '" ' + (selected_tax == wpepro_word2tax_obj.post_types_tax[selected_post][key] ? 'selected ' : '') + ' >' + wpepro_word2tax_obj.post_types_tax[selected_post][key] + '</option>';
		}


		jQuery('#section_word2tax_tax_' + id).html(options_select_tax);

		var options_select_term = '<option value="-1">' + wpepro_word2tax_obj.text_select_term + '</option>';
		jQuery('#section_word2tax_term_' + id).html(options_select_term);

	});
	jQuery('.word2tax_tax').change(function () {
		section_word2tax_on_change_tax(this, '-1');
	});



}


function section_word2tax_add_new_input_group(word_value = '', ontitle = false, onregex = false, oncases = false, selected_post = 'post',  selected_tax = '-1', selected_term = '-1') {
	

	var options_select_post_type = '';
	for (var key in wpepro_word2tax_obj.post_types) {
		options_select_post_type += '<option value="' + key + '" ' + (selected_post == key ? 'selected ' : '') + ' >' + wpepro_word2tax_obj.post_types[key] + '</option>';
	}


	var options_select_tax = '';
	
	for (var key in wpepro_word2tax_obj.post_types_tax[selected_post]) {
		options_select_tax += '<option value="' + wpepro_word2tax_obj.post_types_tax[selected_post][key] + '" ' + (selected_tax == wpepro_word2tax_obj.post_types_tax[selected_post][key] ? 'selected ' : '') + ' >' + wpepro_word2tax_obj.post_types_tax[selected_post][key] + '</option>';
	}

	var options_select_term = '<option value="-1">' + wpepro_word2tax_obj.text_select_term + '</option>';
	

	var template = wp.template('word-to-taxonomy-entity');
	var current_id = jQuery('.row_word_to_tax').length;
	jQuery('#container_word_to_taxonomy').append(
	  template(
		{
			ID: current_id,
			word_value: word_value,
			ontitle: ontitle,
			onregex: onregex,
			oncases: oncases,
			options_select_post: options_select_post_type,
			options_select_tax: options_select_tax,
			options_select_term: options_select_term

		}
	  )
	);

	var options_terms =  section_word2tax_get_terms_from_tax(selected_tax, '#section_word2tax_tax_' + current_id , section_word2tax_on_change_tax, selected_term);
	if (options_terms === false) {
		var options_select_term = '<option value="-1">Loading... </option>';
	} else {
		var options_select_term = '<option value="-1">' + wpepro_word2tax_obj.text_select_term + '</option>';
		for (var key in options_terms) {
			options_select_term += '<option value="' + options_terms[key].term_id + '" ' + (selected_term == options_terms[key].term_id ? 'selected ' : '') + ' >' + options_terms[key].name + '</option>';
		}
		
	}
	jQuery('#section_word2tax_term_' + current_id).html(options_select_term);


}