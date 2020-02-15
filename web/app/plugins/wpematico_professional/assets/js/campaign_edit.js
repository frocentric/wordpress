jQuery(document).ready(function($) {

	jQuery(document).on('click', '.feedoptionsicon', function(e) {
		var id = jQuery(this).attr('id');
		var feed = id.substring(12);
		var modal = '#modalopt_' + feed;
		jQuery(modal).show();
		e.preventDefault();
	});
	checkboxes_unchecked_forced_events();
	wpematico_pro_default_image_events($);

	jQuery('.modal-close').click(function() {
		jQuery('.modal').hide();
	});
	// If an event gets to the body
	jQuery(".modal").click(function() {
		jQuery(".modal").fadeOut();
	});
	jQuery(".modal-content").click(function(e) {
		e.stopPropagation();
	});

	js_add_filter('wpematico_data_test_feed', add_force_feed_test, 10, 2);
	js_add_filter('wpematico_data_test_feed', sanitize_google_feed_test, 11, 2);
	js_add_filter('wpematico_data_test_feed', add_user_agent_test, 11, 2);
	js_add_filter('wpematico_data_test_feed', add_use_cookies_test, 13, 2);


	js_add_filter('wpematico_checkfields_data', sanitize_google_feed_checkfields, 10, 2);
	js_add_filter('wpematico_checkfields_data', user_agent_feed_checkfields, 11, 2);
	js_add_filter('wpematico_checkfields_data', force_feed_checkfields, 12, 2);
	js_add_filter('wpematico_checkfields_data', use_cookies_checkfields, 13, 2);

	events_multimedia_edit($);

	$('.cpt_radio').click(function() {
		$('#loadingstatus').show();
		var data_request = {
			action: 'wpepro_statuses',
			posttype: $(this).val(),
			nonce: wpepro_object.pro_campaign_edit_nonce
		}
		jQuery.ajax({
			type: "POST",
			url: wpepro_object.ajax_url,
			dataType: "json",
			data: data_request,
			success: function(response) {
				console.log(response);
				$current_status = $('#campaign_statuses').val();
				$('#campaign_statuses').html('');
				$.each(response, function(k, v) {
					if (v.id == 'dis') v.id = '" disabled="disabled';
					var option = '<option value="' + v.id + '">' + v.name + '</option>';
					$("#campaign_statuses").append(option);
				});
				$('#loadingstatus').hide();
				$('#campaign_statuses').val($current_status);
			},
			error: function(error) {
				$('#loadingstatus').hide();
				alert('An error has been occurred refreshing the post type statuses.');
			}
		});
	});


	$('#campaign_ctitlecont').click(function() {
		if (true == $('#campaign_ctitlecont').is(':checked')) {
			$('#ctnocont').fadeIn();
		}
		else {
			$('#ctnocont').fadeOut();
		}
	});

	$('#campaign_enablecustomtitle').click(function() {
		if (true == $('#campaign_enablecustomtitle').is(':checked')) {
			$('#nocustitle').fadeIn();
		}
		else {
			$('#nocustitle').fadeOut();
		}
	});

	$('#campaign_ctitlecont').click(function() {
		if (true == $('#campaign_ctitlecont').is(':checked')) {
			$('#ctnocont').fadeIn();
		}
		else {
			$('#ctnocont').fadeOut();
		}
	});

	$('#campaign_delete_till_ontitle').click(function() {
		if (true == $('#campaign_delete_till_ontitle').is(':checked')) {
			$('#div_delete_till_ontitle').fadeIn();
		}
		else {
			$('#div_delete_till_ontitle').fadeOut();
		}
	});



});

function wpematico_pro_default_image_events($) {
	$('.et_upload_button').click(function() {
		var btnid = $(this).attr('id');
		var field_img = 'default_img_url';
		var field_link = 'default_img_link';
		var field_title = 'default_img_title';
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		ff(field_img, field_link, field_title);
		return false;
	});

	function ff(field_img, field_link, field_title) {
		window.send_to_editor = function(html) { // html = "<img src="http://domain/wp-content/uploads/2015/07/talleres_bandera11.png" alt="" width="170" height="99" class="alignnone size-full wp-image-345" />"
			var linktit = jQuery('img', html).attr('title');
			if (typeof linktit == 'undefined') {
				var linktit = jQuery(html).attr('title');
			}
			var imgurl = jQuery('img', html).attr('src');
			if (typeof imgurl == 'undefined') {
				var imgurl = jQuery(html).attr('src');
			}
			var linkurl = jQuery(html).attr('href');

			var attach_id = 0;

			var class_attr = jQuery('img', html).attr("class");
			if (typeof class_attr == 'undefined') {
				var class_attr = jQuery(html).attr("class");
			}

			var image_clases = class_attr.toString().split(' ');
			jQuery.each(image_clases, function(i, class_name) {
				if (class_name.indexOf('wp-image-') !== -1) {
					attach_id = class_name.replace('wp-image-', '');
				}
			});


			if (attach_id == 0) {
				wpepro_upload_default_image_url(imgurl);
			}

			jQuery('input[name="' + field_img + '"]').val(imgurl);
			jQuery('input[name="' + field_link + '"]').val(linkurl);
			jQuery('input[name="' + field_title + '"]').val(linktit);
			jQuery('input[name="default_img_id"]').val(attach_id);
			jQuery('.et_upload_button').val(wpepro_object.text_change_image);
			jQuery('.default_img_url_openlink').prop('href', imgurl);
			jQuery('#default_img_url_div').fadeIn();
			tb_remove();
		}
	}
}

function checkboxes_unchecked_forced_events() {
	jQuery('input[data-unchecked-forced]').change(function(e) {
		var hidden_element = jQuery(this).data('unchecked-forced');
		var value = jQuery(this).is(":checked") ? jQuery(this).val() : '';
		jQuery('#' + hidden_element).val(value);
	});
}

function add_user_agent_test(data, item) {
	if (item == undefined) {
		return data;
	}
	var id_div_parent = item.parent().parent().attr('id');
	if (id_div_parent != undefined) {
		var index_feed = id_div_parent.replace('feed_ID', '');
	}
	if (data.feed == undefined) {
		data.feed = {};
	}
	if (index_feed != undefined && jQuery('#user_agent_' + index_feed).val() != 'CoreUserAgent') {
		data.feed.user_agent = new Array();
		data.feed.user_agent[0] = jQuery('#user_agent_' + index_feed).val();
	}
	return data;
}

function add_force_feed_test(data, item) {
	if (item == undefined) {
		return data;
	}
	var id_div_parent = item.parent().parent().attr('id');
	if (id_div_parent != undefined) {
		var index_feed = id_div_parent.replace('feed_ID', '');
	}
	if (data.feed == undefined) {
		data.feed = {};
	}
	if (index_feed != undefined && jQuery('#force_feed_checkbox_' + index_feed).is(':checked')) {
		data.feed.force_feed = new Array();
		data.feed.force_feed[0] = 'true';
	}
	return data;
}

function add_use_cookies_test(data, item) {
	if (item == undefined) {
		return data;
	}
	var id_div_parent = item.parent().parent().attr('id');
	if (id_div_parent != undefined) {
		var index_feed = id_div_parent.replace('feed_ID', '');
	}
	if (data.feed == undefined) {
		data.feed = {};
	}
	if (index_feed != undefined && jQuery('#enable_cookies_checkbox_' + index_feed).is(':checked')) {
		data.feed.enable_cookies = new Array();
		data.feed.enable_cookies[0] = 'true';
	}
	return data;
}

function sanitize_google_feed_test(data, item) {
	if (item == undefined) {
		return data;
	}
	if (jQuery('#fix_google_links').is(':checked') && data.url.toLowerCase().indexOf('google') !== -1) {
		// It replace all '%20' per '+' to prevent issues on the path of google web server.
		data.url = data.url.replace(new RegExp('%20', 'g'), '+');
	}

	return data;
}

function sanitize_google_feed_checkfields(data) {
	if (jQuery('#fix_google_links').is(':checked')) {
		data.fix_google_links = true;
	}
	return data;
}

function user_agent_feed_checkfields(data) {
	if (data.feed == undefined) {
		data.feed = {};
	}
	data.feed.user_agent = jQuery('.user_agent').map(function() {
		return jQuery(this).val();
	}).get();

	return data;
}

function force_feed_checkfields(data) {
	if (data.feed == undefined) {
		data.feed = {};
	}

	data.feed.force_feed = jQuery('.force_feed_checkbox').map(function() {
		return (jQuery(this).is(':checked') ? 1 : 0);
	}).get();


	return data;
}

function use_cookies_checkfields(data) {
	if (data.feed == undefined) {
		data.feed = {};
	}
	data.feed.enable_cookies = jQuery('.enable_cookies_checkbox').map(function() {
		return (jQuery(this).is(':checked') ? 1 : 0);
	}).get();

	return data;
}


jQuery(document).on('before_add_more_feed', function(e, feed_new, newval) {
	jQuery('.feedoptionsicon', feed_new).eq(0).attr('id', 'feedoptions_' + newval);
	jQuery('.modal', feed_new).eq(0).attr('id', 'modalopt_' + newval);

	//jQuery('.force_feed', feed_new).eq(0).attr('name', 'force_feed['+newval+']');
	jQuery('.force_feed', feed_new).eq(0).attr('id', 'force_feed_' + newval);
	jQuery('.force_feed_checkbox', feed_new).eq(0).attr('id', 'force_feed_checkbox_' + newval);
	jQuery('.force_feed_checkbox', feed_new).eq(0).attr('data-unchecked-forced', 'force_feed_' + newval);

	//jQuery('.feed_name', feed_new).eq(0).attr('name', 'feed_name['+newval+']');
	jQuery('.feed_name', feed_new).eq(0).attr('id', 'feed_name_' + newval);


	//jQuery('.campaign_user_agent', feed_new).eq(0).attr('name', 'campaign_user_agent['+newval+']');
	jQuery('.user_agent', feed_new).eq(0).attr('id', 'user_agent_' + newval);

	//jQuery('.enable_cookies', feed_new).eq(0).attr('name', 'enable_cookies['+newval+']');
	jQuery('.enable_cookies', feed_new).eq(0).attr('id', 'enable_cookies_' + newval);
	jQuery('.enable_cookies_checkbox', feed_new).eq(0).attr('id', 'enable_cookies_checkbox_' + newval);
	jQuery('.enable_cookies_checkbox', feed_new).eq(0).attr('data-unchecked-forced', 'enable_cookies_' + newval);

	//jQuery('.campaign_input_encoding', feed_new).eq(0).attr('name', 'campaign_input_encoding['+newval+']');
	jQuery('.campaign_input_encoding', feed_new).eq(0).attr('id', 'campaign_input_encoding_' + newval);



	if (wpepro_object.settings.enablemultifeed) {
		//jQuery('.is_multipagefeed', feed_new).eq(0).attr('name', 'is_multipagefeed['+newval+']');
		jQuery('.is_multipagefeed', feed_new).eq(0).attr('id', 'is_multipagefeed_' + newval);
		jQuery('.is_multipagefeed_checkbox', feed_new).eq(0).attr('id', 'is_multipagefeed_checkbox_' + newval);
		jQuery('.is_multipagefeed_checkbox', feed_new).eq(0).attr('data-unchecked-forced', 'is_multipagefeed_' + newval);

		//jQuery('.multifeed_maxpages', feed_new).eq(0).attr('name', 'multifeed_maxpages['+newval+']');
		jQuery('.multifeed_maxpages', feed_new).eq(0).attr('id', 'multifeed_maxpages_' + newval);
	}


	jQuery('.modal-close').click(function() {
		jQuery('.modal').hide();
	});

	// If an event gets to the body
	jQuery(".modal").click(function() {
		jQuery(".modal").fadeOut();
	});

	jQuery(".modal-content").click(function(e) {
		e.stopPropagation();
	});

	checkboxes_unchecked_forced_events();
});

function events_multimedia_edit($) {

	$('#campaign_customupload').click(function() {
		if (true == $('#campaign_customupload').is(':checked')) {
			$('#contanier_image_ren').fadeIn();
		}
		else {
			$('#contanier_image_ren').fadeOut();
		}
	});

	$('#campaign_no_setting_img').click(function() {
		if (true == $('#campaign_no_setting_img').is(':checked')) {
			if (true == $('#campaign_customupload').is(':checked')) {
				$('#contanier_image_ren').fadeIn();
			}

		}
		else {
			$('#contanier_image_ren').fadeOut();
		}
	});



	jQuery('#campaign_imgcache, #campaign_featuredimg').click(function() {
		if (true == jQuery('#campaign_imgcache').is(':checked') && false == jQuery('#campaign_featuredimg').is(':checked') && true == jQuery('#campaign_customupload').is(':checked')) {
			jQuery('#contanier_image_ren').fadeIn();
			return true;
		}
		else {
			jQuery('#contanier_image_ren').fadeOut();
		}

		if (true == jQuery('#campaign_featuredimg').is(':checked') && false == jQuery('#campaign_imgcache').is(':checked') && true == jQuery('#campaign_customupload').is(':checked')) {
			jQuery('#contanier_image_ren').fadeIn();
			return true;
		}
		else {
			jQuery('#contanier_image_ren').fadeOut();
		}

		if (true == jQuery('#campaign_featuredimg').is(':checked') && true == jQuery('#campaign_imgcache').is(':checked') && true == jQuery('#campaign_customupload').is(':checked')) {
			jQuery('#contanier_image_ren').fadeIn();
			return true;
		}
		else {
			jQuery('#contanier_image_ren').fadeOut();
		}
	});


	$('#check_image_content').click(function() {
		if (true == $('#check_image_content').is(':checked')) {
			$('#check_image_content_div').fadeIn();
		}
		else {
			$('#check_image_content_div').fadeOut();
		}
	});

	$('#campaign_rssimg').click(function() {
		if (true == $('#campaign_rssimg').is(':checked')) {
			$('.rssimg_opt').fadeIn();
		}
		else {
			$('.rssimg_opt').fadeOut();
		}
	});

	$('#strip_all_images').click(function() {
		if (true == $('#strip_all_images').is(':checked')) {
			$('#noimages').fadeOut();
		}
		else {
			$('#noimages').fadeIn();
		}
	});

	$('#default_img').click(function() {
		if (true == $('#default_img').is(':checked')) {
			$('#tblupload').fadeIn();
		}
		else {
			$('#tblupload').fadeOut();
		}
	});


	$('#rssimg_featured').click(function() {
		if (true == $('#rssimg_featured').is(':checked')) {
			$('#featured_opt').fadeIn();
		}
		else {
			$('#featured_opt').fadeOut();
		}
	});
	$('#add1stimg').click(function() {
		if (true == $('#add1stimg').is(':checked')) {
			$('#img_permal').fadeIn();
		}
		else {
			$('#img_permal').fadeOut();
		}
	});

	if (wpepro_object.settings.enableimgfilter) {
		$('#addmoreimgf').click(function() {
			$('#imgfilt_max').val(parseInt($('#imgfilt_max').val(), 10) + 1);
			newval = $('#imgfilt_max').val();
			nuevo = $('#nuevoimgfilt').clone();
			$('select', nuevo).eq(0).attr('name', 'campaign_if_allow[' + newval + ']');
			$('select', nuevo).eq(1).attr('name', 'campaign_if_woh[' + newval + ']');
			$('select', nuevo).eq(2).attr('name', 'campaign_if_mol[' + newval + ']');
			$('input', nuevo).eq(0).attr('name', 'campaign_if_value[' + newval + ']');
			//$('select', nuevo).eq(0).val('');
			$('input', nuevo).eq(0).val('');
			nuevo.css("display", "flex");
			$('#imgfilt_edit').append(nuevo);
		});
	}


	$('#addmorefeatimgf').click(function() {
		$('#featimgfilt_max').val(parseInt($('#featimgfilt_max').val(), 10) + 1);
		newval = $('#featimgfilt_max').val();
		nuevo = $('#nuevofeatimgfilt').clone();
		$('select', nuevo).eq(0).attr('name', 'campaign_feat_allow[' + newval + ']');
		$('select', nuevo).eq(1).attr('name', 'campaign_feat_woh[' + newval + ']');
		$('select', nuevo).eq(2).attr('name', 'campaign_feat_mol[' + newval + ']');
		$('input', nuevo).eq(0).attr('name', 'campaign_feat_value[' + newval + ']');
		//$('select', nuevo).eq(0).val('');
		$('input', nuevo).eq(0).val('');
		nuevo.css("display", "flex");
		$('#featimgfilt_edit').append(nuevo);
	});

	if (wpepro_object.settings.enablecfields) {

		$('#addmorecf').click(function() {
			$('#cfield_max').val(parseInt($('#cfield_max').val(), 10) + 1);
			newval = $('#cfield_max').val();
			nuevo = $('#nuevocfield').clone();
			$('input', nuevo).eq(0).attr('name', 'campaign_cf_name[' + newval + ']');
			$('input', nuevo).eq(1).attr('name', 'campaign_cf_value[' + newval + ']');
			$('input', nuevo).eq(0).val('');
			$('input', nuevo).eq(1).val('');
			nuevo.css("display", "flex");
			$('#cfield_edit').append(nuevo);
		});
	}

	$('#campaign_enableimgrename').click(function() {
		if (true == $('#campaign_enableimgrename').is(':checked')) {
			$('#noimgren').fadeIn();
		}
		else {
			$('#noimgren').fadeOut();
		}
	});


	$('#campaign_customupload_audio').click(function() {
		if (true == $('#campaign_customupload_audio').is(':checked')) {
			$('#contanier_audio_ren').fadeIn();
		}
		else {
			$('#contanier_audio_ren').fadeOut();
		}
	});
	jQuery('#campaign_audio_cache').click(function() {
		if (true == jQuery('#campaign_audio_cache').is(':checked') && true == jQuery('#campaign_customupload_audio').is(':checked')) {
			jQuery('#contanier_audio_ren').fadeIn();
		}
		else {
			jQuery('#contanier_audio_ren').fadeOut();
		}
		if (true == jQuery('#campaign_audio_cache').is(':checked')) {
			jQuery('#audio_upload_ranges_div').fadeIn();
		}
		else {
			jQuery('#audio_upload_ranges_div').fadeOut();
		}
	});

	jQuery('#audio_upload_ranges').click(function() {
		if (true == jQuery('#audio_upload_ranges').is(':checked')) {
			jQuery('#audio_upload_range_mb_div').fadeIn();
		}
		else {
			jQuery('#audio_upload_range_mb_div').fadeOut();
		}
	});


	$('#enable_audio_rename').click(function() {
		if (true == $('#enable_audio_rename').is(':checked')) {
			$('#no_audio_ren').fadeIn();
		}
		else {
			$('#no_audio_ren').fadeOut();
		}
	});

	$('#rss_audio').click(function() {
		if (true == $('#rss_audio').is(':checked')) {
			$('.rss_audio_opt').fadeIn();
		}
		else {
			$('.rss_audio_opt').fadeOut();
		}
	});


	$('#campaign_customupload_video').click(function() {
		if (true == $('#campaign_customupload_video').is(':checked')) {
			$('#contanier_video_ren').fadeIn();
		}
		else {
			$('#contanier_video_ren').fadeOut();
		}
	});
	jQuery('#campaign_video_cache').click(function() {
		if (true == jQuery('#campaign_video_cache').is(':checked') && true == jQuery('#campaign_customupload_video').is(':checked')) {
			jQuery('#contanier_video_ren').fadeIn();
		}
		else {
			jQuery('#contanier_video_ren').fadeOut();
		}
		if (true == jQuery('#campaign_video_cache').is(':checked')) {
			jQuery('#video_upload_ranges_div').fadeIn();
		}
		else {
			jQuery('#video_upload_ranges_div').fadeOut();
		}
	});

	jQuery('#video_upload_ranges').click(function() {
		if (true == jQuery('#video_upload_ranges').is(':checked')) {
			jQuery('#video_upload_range_mb_div').fadeIn();
		}
		else {
			jQuery('#video_upload_range_mb_div').fadeOut();
		}
	});


	$('#enable_video_rename').click(function() {
		if (true == $('#enable_video_rename').is(':checked')) {
			$('#no_video_ren').fadeIn();
		}
		else {
			$('#no_video_ren').fadeOut();
		}
	});

	$('#rss_video').click(function() {
		if (true == $('#rss_video').is(':checked')) {
			$('.rss_video_opt').fadeIn();
		}
		else {
			$('.rss_video_opt').fadeOut();
		}
	});
}

function wpepro_upload_default_image_url(image_url) {
	var data_request = {
		action: 'wpepro_upload_default_image',
		img_url: image_url,
		nonce: wpepro_object.pro_campaign_edit_nonce
	}

	jQuery.post(wpepro_object.ajax_url, data_request, function(response) {
		console.log(response)
		if (response.success) {

			jQuery('input[name="default_img_url"]').val(response.data.new_image);
			jQuery('input[name="default_img_link"]').val(response.data.new_image);
			jQuery('input[name="default_img_title"]').val('');
			jQuery('input[name="default_img_id"]').val(response.data.new_id);

		}
		else {
			alert('An error has been occurred uploading the default image URL. Please upload from your computer.');
		}

	});
}
var array_taxonomy_div_ids = { post_format: 'post_format-box', category: 'category-box', post_tag: 'post_tag-box' };

function wpepro_update_taxonomy_id($) {

	for (var i in wpepro_object.taxonomy_post_type['wpematico']) {
		if ($('#' + wpepro_object.taxonomy_post_type['wpematico'][i]).length > 0 && array_taxonomy_div_ids[wpepro_object.taxonomy_post_type['wpematico'][i]] == undefined) {
			array_taxonomy_div_ids[wpepro_object.taxonomy_post_type['wpematico'][i]] = $('#' + wpepro_object.taxonomy_post_type['wpematico'][i]).closest('.postbox').attr('id');
		};
		if ($('#taxonomy-' + wpepro_object.taxonomy_post_type['wpematico'][i]).length > 0 && array_taxonomy_div_ids[wpepro_object.taxonomy_post_type['wpematico'][i]] == undefined) {
			array_taxonomy_div_ids[wpepro_object.taxonomy_post_type['wpematico'][i]] = $('#taxonomy-' + wpepro_object.taxonomy_post_type['wpematico'][i]).closest('.postbox').attr('id');
		};
	}
}



var campaign_word2tax_waiting = new Array();
var campaign_word2tax_pending_callbacks = new Array();

function campaign_word2tax_get_terms_from_tax(tax, obj, callback, selected_term) {
	if (tax == '-1') {
		return new Array();
	}

	if (wpepro_object.post_types_terms[tax] !== undefined && wpepro_object.post_types_terms[tax].length >= 0) {
		return wpepro_object.post_types_terms[tax];
	}
	if (campaign_word2tax_pending_callbacks[tax] == undefined) {
		campaign_word2tax_pending_callbacks[tax] = new Array();
	}
	campaign_word2tax_pending_callbacks[tax].push(new Array(callback, obj, selected_term));
	if (campaign_word2tax_waiting[tax] !== undefined) {
		return false;
	}
	campaign_word2tax_waiting[tax] = true;

	jQuery.ajax({
		url: ajaxurl, // this is a variable that WordPress has already defined for us
		type: 'POST',
		data: {
			action: 'wpepro_word2tax_terms', // this is the name of our WP AJAX function that we'll set up next
			tax: tax,
			nonce: wpepro_object.get_terms_nonce
		}
	}).done(function(data) {
		//console.log(data);
		if (data.success) {
			wpepro_object.post_types_terms[tax] = data.data;
			if (campaign_word2tax_pending_callbacks[tax] != undefined) {
				for (var key in campaign_word2tax_pending_callbacks[tax]) {
					campaign_word2tax_pending_callbacks[tax][key][0](campaign_word2tax_pending_callbacks[tax][key][1], campaign_word2tax_pending_callbacks[tax][key][2]);
				}
			}
			return true;
		}
		else {
			alert(data.data.message);
		}
	});
	return false;
}

function campaign_word2tax_on_change_tax(obj, selected_term = '-1') {


	var id = jQuery(obj).attr('id').replace("campaign_word2tax_tax_", "");
	var selected_tax = jQuery(obj).val();

	var options_terms = campaign_word2tax_get_terms_from_tax(selected_tax, obj, campaign_word2tax_on_change_tax, selected_tax);

	if (options_terms === false) {
		var options_select_term = '<option value="-1">Loading... </option>';
	}
	else {
		var options_select_term = '<option value="-1">' + wpepro_object.text_select_term + '</option>';
		for (var key in options_terms) {
			options_select_term += '<option value="' + options_terms[key].term_id + '" ' + (selected_term == options_terms[key].term_id ? 'selected ' : '') + ' >' + options_terms[key].name + '</option>';
		}

	}

	jQuery('#campaign_word2tax_term_' + id).html(options_select_term);

}

function word2tax_events_rows() {
	jQuery('.btn_delete_w2t').click(function(e) {
		jQuery(this).parent().parent().parent().remove();
		e.preventDefault();
	});
	jQuery('.word2tax_tax').change(function() {
		campaign_word2tax_on_change_tax(this, '-1');


	});


}

function add_new_input_group(word_value = '', ontitle = false, onregex = false, oncases = false, selected_tax = '-1', selected_term = '-1') {
	var currentPostType = jQuery('input[name="campaign_customposttype"]:checked').val();

	var options_select_tax = '';
	for (var key in wpepro_object.taxonomy_post_type[currentPostType]) {
		options_select_tax += '<option value="' + wpepro_object.taxonomy_post_type[currentPostType][key] + '" ' + (selected_tax == wpepro_object.taxonomy_post_type[currentPostType][key] ? 'selected ' : '') + ' >' + wpepro_object.taxonomy_post_type[currentPostType][key] + '</option>';
	}


	var options_select_term = '<option value="-1">' + wpepro_object.text_select_term + '</option>';

	var template = wp.template('word-to-taxonomy-entity');
	var current_id = jQuery('.row_word_to_tax').length;
	jQuery('#container_word_to_taxonomy').append(
		template({
			ID: jQuery('.row_word_to_tax').length,
			word_value: word_value,
			ontitle: ontitle,
			onregex: onregex,
			oncases: oncases,
			options_select_tax: options_select_tax,
			options_select_term: options_select_term

		})
	);


	var options_terms = campaign_word2tax_get_terms_from_tax(selected_tax, '#campaign_word2tax_tax_' + current_id, campaign_word2tax_on_change_tax, selected_term);
	if (options_terms === false) {
		var options_select_term = '<option value="-1">Loading... </option>';
	}
	else {
		var options_select_term = '<option value="-1">' + wpepro_object.text_select_term + '</option>';
		for (var key in options_terms) {
			options_select_term += '<option value="' + options_terms[key].term_id + '" ' + (selected_term == options_terms[key].term_id ? 'selected ' : '') + ' >' + options_terms[key].name + '</option>';
		}

	}
	jQuery('#campaign_word2tax_term_' + current_id).html(options_select_term);
}
