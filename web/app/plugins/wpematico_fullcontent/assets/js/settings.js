jQuery(document).ready(function ($) {
	$("#testtabs").tabs();

	/**
	 * Form buttons
	 */
	var popup;
	var url_fullcontent_temp = '';
	var editor_fullcontent_temp = '';

	//keyup ESC
	$("#filename").keyup(function (e) {
		if (e.keyCode == 27) {
			//active buttons
			$("#savingas").hide(0);
			if (editor_fullcontent_temp.indexOf(fullcontent_settings_object.fileright) > -1) {
				$(".fullcontent-saveass").find('span').text('New');
				$(".fullcontent-testurl").removeClass('button-disabled');
			} else {
				$(".fullcontent-saveass").find('span').text('New');
				$(".fullcontent-saveass").removeClass('button-disabled');
				$("#mff_inspector").removeClass('button-disabled');
				$("#wpematico-save-fullcontent").removeClass('button-disabled');
				$("#wpematico-save-fullcontent").addClass('button-primary');
				$(".preview_txt").addClass('button-disabled');
				$(".fullcontent-testurl").removeClass('button-disabled');
			}

			$('textarea[name="textfile"]').val(editor_fullcontent_temp);
			$('#filename').val(url_fullcontent_temp).attr('readonly', true);
			$('textarea[name="textfile"]').focus();
		}
	})

	$("a.input-fullcontent").click(function () {
		if ($(this).hasClass('button-disabled')) {
			return false;
		}
		var textbuttonfull = $(this).find('span').text();
		switch (textbuttonfull) {
			case fullcontent_settings_object.new:
				$("a.input-fullcontent").addClass('button-disabled');
				$(".fullcontent-testurl").addClass('button-disabled');
				$(".fullcontent-saveass").find('span').text('Save As');
				$(".fullcontent-saveass").removeClass('button-disabled');
				url_fullcontent_temp = $("#filename").val();
				editor_fullcontent_temp = $('textarea[name="textfile"]').val();
				if ($('textarea[name="textfile"]').val().indexOf(fullcontent_settings_object.fileright) > -1) {
					$('textarea[name="textfile"]').val("body: \n\nstrip: \n\ntidy: no \nautodetect_on_failure: yes \nprune: no \n\ntest_url: ");
				}
				break;
		}

	});
	$("#fullcontent_editor").keyup(function (event) {
		/*$("#wpematico-save-fullcontent").removeClass('button-disabled');
		 $(".savefile-fullcontent").removeClass('button-disabled')*/
		//$("#mff_inspector").addClass('button-disabled');
		$(".preview_txt").addClass('button-disabled');
	});

	$("#mff_inspector").click(function () {
		if ($(this).hasClass('button-disabled'))
			return false;
		//parse textarea and get test url
		var lines = $('textarea[name="textfile"]').val().replace(/\r\n/g, "\n").split("\n");
		var fullcontent_editor = jQuery("textarea[name='textfile']").val();
		mff_array_textfile = jQuery("textarea[name='textfile']").val().split("\n");
		//test url preview
		for (var i = 0; i < mff_array_textfile.length; i++) {
			if (mff_array_textfile[i].indexOf('test_url:') > (-1)) {
				preview_url_true = mff_array_textfile[i].split('test_url:');
				var url_to_preview = preview_url_true.pop();

				if (url_to_preview.trim().indexOf($("#filename").val().trim()) > -1) {
					//not	
				} else {
					alert(fullcontent_settings_object.notmatch);
					return false;
				}
			}
		}
		//closed if url preview

		mff_array_textfile = jQuery("textarea[name='textfile']").val().split("\n");
		for (var i = 0; i < mff_array_textfile.length; i++) {
			if (mff_array_textfile[i].indexOf('test_url') > (-1)) {
				mff_textfile_url = mff_array_textfile[i].split('test_url:');
				for (j = 0; j < mff_textfile_url.length; j++) {
					if (mff_textfile_url[j] != "") {
						single_URL = mff_textfile_url[j].trim().toString();
						popup = window.open(fullcontent_settings_object.popupurl + single_URL, 'Go Extractor', 'width=1024, height=700');
						return false;
					}
				}//closedj
			}//closedi
		}
	});

	//hide and show editor
	$(".fullcontent-testurl").on('click', function () {
		if ($(this).hasClass('button-disabled'))
			return false;

		if ($("#fullcontent_editor").is(':visible')) {
			$("#section_editor_buttons").hide();
			$("#filename").hide();
			$("#single_uri").show();
			$(".span_preview_txt").hide();
			$(".span_preview_uri").show();
			$(".span_preview_uri").removeClass('button-disabled');
			$("#single_uri").focus();
		} else {
			$("#section_editor_buttons").show();
			$("#filename").show();
			$("#single_uri").hide();
			$(".span_preview_txt").show();
			$(".span_preview_uri").hide();
			$(".span_preview_uri").addClass('button-disabled');
			$('textarea[name="textfile"]').focus();
		}
	});
	String.prototype.replaceAll = function (search, replacement) {
		var target = this;
		return target.split(search).join(replacement);
	};
	/**
	 * End form buttons
	 */


	$(document.body).on('click', '.fileonlist', function (e) {
		e.preventDefault();
		var submitButton = $(document.body).find('input[type="submit"]');

		var data = $(this).attr('data');
		var nonce = $(this).attr('nonce');
		//disabled buttons temps
		submitButton.addClass('button-disabled');
		$(".addfile").addClass('button-disabled');
		$(".preview_txt").addClass('button-disabled');
		$("#mff_inspector").addClass('button-disabled');
		$(".fullcontent-testurl").addClass('button-disabled');
		//-----

		$('#statusmessage').hide();
		$("#savingas").hide();
		$('.fileonlist .spinner').remove();
		$("#fullsection_messages #section_spinner .spinner").remove();
		$('#visual').html('');
		$('#text').text('');
		$('#meta_data').html('');

		$(this).prepend('<span class="spinner is-active"></span>');
		$("#fullsection_messages #section_spinner").prepend('<span class="spinner is-active"></span>');
		// start the process
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				data: data,
				_wpnonce: nonce,
				action: 'wpematico_load_custom_txt',
			},
			dataType: "json",
			success: function (response) {
				$("#savingas").val("");
				response = response.data;
				if (response.error) {
					var error_message = response.message;
					console.log(error_message);
					$('#statusmessage').removeClass('notice-success').addClass('notice-error').html('<p>' + fullcontent_settings_object.statusmessage1 + '</p>').show().delay(5000).fadeOut('slow');
					$('input[name="pathfilename"]').val('');
					$('input[name="filename"]').val(error_message);
					$('textarea[name="textfile"]').val(fullcontent_settings_object.selectfile);
					$('.fileonlist .spinner').remove();
					$("#fullsection_messages #section_spinner .spinner").remove();

				} else {
					$('input[name="pathfilename"]').val(response.pathfilename);
					response.filename = response.filename.replace('.txt', '');
					$('input[name="filename"]').val(response.filename);
					$('textarea[name="textfile"]').val(response.textfile);
					$('.fileonlist .spinner').remove();
					$("#fullsection_messages #section_spinner .spinner").remove();
					//	$('#statusmessage').removeClass('notice-error').addClass('notice-success').html('<p>'+fullcontent_settings_object.statusmessage2+'</p>').show().delay(5000).fadeOut('slow');
					$("#statusmessage").appendTo('#fullsection_messages').removeClass('notice-error').addClass('notice-success').html('<p>' + fullcontent_settings_object.statusmessage2 + '</p>').show().delay(5000).fadeOut('slow');
					//active buttons
					$(".fullcontent-saveass").find('span').text('New');
					$(".fullcontent-saveass").removeClass('button-disabled');
					$("#mff_inspector").removeClass('button-disabled');
					$("#wpematico-save-fullcontent").removeClass('button-disabled');
					$("#wpematico-save-fullcontent").addClass('button-primary');
					$(".preview_txt").removeClass('button-disabled');
					$(".fullcontent-testurl").removeClass('button-disabled');
					$("#filename").attr('readonly', true);
				}
				$(".addfile").removeClass('button-disabled');

			}
		}).fail(function (response) {
			if (window.console && window.console.log) {
				console.log(response);
			}
		});
	});

	$(document.body).on('click', '#wpematico-save-fullcontent', function (e) {
		e.preventDefault();
		//var submitButton = $(document.body).find( 'input[type="submit"]' );
		var submitButton = $(this);
		if (!submitButton.hasClass('button-disabled')) {
			submitButton.addClass('button-disabled');
			$('#saving .spinner').remove();
			$("#fullsection_messages #section_spinner .spinner").remove();
			$('#statusmessage').hide();
			//$('#saving').append( '<span class="spinner is-active"></span>' );
			$("#fullsection_messages #section_spinner").append('<span class="spinner is-active"></span>');
			$(".addfile").addClass('button-disabled');


			// start the process
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					pathfilename: $('input[name="pathfilename"]').val(),
					filename: 'NO', //$('input[name="filename"]').val(),
					textfile: $('textarea[name="textfile"]').val(),
					_wpnonce: $('input[name="_wpnonce"]').val(),
					_wp_http_referer: $('input[name="_wp_http_referer"]').val(),
					action: 'wpematico_save_fullcontent',
				},
				dataType: "json",
				success: function (response) {
					$("#savingas").hide();
					response = response.data;
					//$class .= ($mess['below-h2']) ? " below-h2" : "";
					if (response.error) {
						var error_message = response.message;
						console.log('ERROR: ' + error_message);
						$('#savemessage').removeClass('notice-success').addClass('notice-error').html(error_message).show().delay(5000).fadeOut('slow');
						$('#saving .spinner').remove();
						$("#fullsection_messages #section_spinner .spinner").remove();
					} else {
						$('input[name="pathfilename"]').val(response.pathfilename);
						response.filename = response.filename.replace('.txt', '');
						$('input[name="filename"]').val(response.filename);
						$('#savemessage').removeClass('notice-error').addClass('notice-success').html('<p>' + fullcontent_settings_object.savemessage + '</p>').show().delay(5000).fadeOut('slow');
					}
					$('#saving .spinner').remove();
					$("#fullsection_messages #section_spinner .spinner").remove();
					submitButton.removeClass('button-disabled');
					$("#mff_inspector").removeClass('button-disabled');
					$(".preview_txt").removeClass('button-disabled');
					$(".addfile").removeClass('button-disabled');


				}
			}).fail(function (response) {
				if (window.console && window.console.log) {
					console.log(response);
				}
			});
		}

	});

	$(document.body).on('click', '.addfile', function (e) {
		//e.preventDefault();
		//var submitButton = $(document.body).find( 'input[type="submit"]' );
		var submitButton = $(this);
		//submitButton.addClass( 'button-disabled');
		$('#saving .spinner').remove();
		$("#fullsection_messages #section_spinner .spinner").remove();
		$('#statusmessage').hide();
		$('#visual').html('');
		$('#text').text('');
		$('#meta_data').html('');

		if ($('#filename').attr('readonly') == 'readonly') {
			$("#savingas").show();
			$('#savingas').html('<p>' + fullcontent_settings_object.savingas + '</p>').show();
			$(this).attr('title', fullcontent_settings_object.savingastitle).show();
			$('#filename').removeAttr('readonly');
			$('#filename').focus().select();
		} else {
			$("#savingas").show();
			$('#savingas').html('<p>' + fullcontent_settings_object.savingas2 + '</p>');
			$(this).attr('title', '');
			$('#filename').attr('readonly', 'readonly');
			//$('#saving').append( '<span class="spinner is-active"></span>' );
			$("#fullsection_messages #section_spinner").prepend('<span class="spinner is-active" style="float:left;"></span>');
			// start the process

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					pathfilename: $('input[name="pathfilename"]').val(),
					filename: $('input[name="filename"]').val(), // new file
					textfile: $('textarea[name="textfile"]').val(),
					_wpnonce: $('input[name="_wpnonce"]').val(),
					_wp_http_referer: $('input[name="_wp_http_referer"]').val(),
					action: 'wpematico_save_fullcontent',
				},
				dataType: "json",
				success: function (response) {
					response = response.data;
					//$class .= ($mess['below-h2']) ? " below-h2" : "";
					if (response.error) {
						var error_message = response.message;
						console.log('ERROR: ' + error_message);
						$('#savingas').html('<p style="color: red;">ERROR: ' + error_message + '</p>').show().delay(5000).fadeOut('slow');
						$('#saving .spinner').remove();
						$("#fullsection_messages #section_spinner .spinner").remove();
						$("#filename").attr('readonly', false).focus();
					} else {
						$('input[name="pathfilename"]').val(response.pathfilename);
						$('input[name="filename"]').val(response.filename);
						$('#savingas').html('<p style="color: green;">' + fullcontent_settings_object.savemessage + '</p>').show().delay(5000).fadeOut('slow');
						submitButton.removeClass('button-disabled');
						location.reload();
					}
					$('#saving .spinner').remove();
					$("#fullsection_messages #section_spinner .spinner").remove();

				}
			}).fail(function (response) {
				$('#savingas').html('ERROR: ' + fullcontent_settings_object.goeswrong).show().delay(3000).fadeOut('slow');
				if (window.console && window.console.log) {
					console.log(response);
				}
			});
		}
	});

	$(document.body).on('click', '.movetouploads', function (e) {
		e.preventDefault();
		var submitButton = $(document.body).find('input[type="submit"]');

		submitButton.addClass('button-disabled');
		$('#saving .spinner').remove();
		$('#statusmessage').hide();
		$('#visual').html('');
		$('#text').text('');
		$('#meta_data').html('');

		$(this).addClass('button-primary-disabled');
		$(this).after('<span class="spinner is-active"></span>');

		// start the process
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				_wpnonce: $('input[name="_wpnonce"]').val(),
				_wp_http_referer: $('input[name="_wp_http_referer"]').val(),
				action: 'wpematico_movetouploads_fullcontent',
			},
			dataType: "json",
			success: function (response) {
				response = response.data;
				if (response.error) {
					var error_message = response.message;
					console.log('ERROR: ' + error_message);
					$('h2.nav-tab-wrapper').after('<div class="notice notice-error is-dismissible"><p>' + error_message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + fullcontent_settings_object.dismiss + '</span></button></div>');
					$('.spinner').remove();
				} else {
					$('input[name="pathfilename"]').val(response.pathfilename);
					$('input[name="filename"]').val(response.filename);
					$('h2.nav-tab-wrapper').after('<div class="notice notice-success is-dismissible"><p>' + fullcontent_settings_object.filesmoved + ' ' + fullcontent_settings_object.reloadpage + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + fullcontent_settings_object.dismiss + '</span></button></div>');
					$('.movetouploads').remove();
					submitButton.removeClass('button-disabled');
					location.reload();
				}
				$('.spinner').remove();
			}
		}).fail(function (response) {
			$('h2.nav-tab-wrapper').after('<div class="notice notice-error is-dismissible"><p>' + fullcontent_settings_object.goeswrong + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + fullcontent_settings_object.dismiss + '</span></button></div>');
			if (window.console && window.console.log) {
				console.log(response);
			}
		});

	});

	//remove SPACE \n \t
	String.prototype.trim = function () {
		return this.replace(/^\s+|\s+$/g, "");
	};

	$(document.body).on('click', '.preview_txt', function (e) {
		//parse textarea and get test url
		var lines = $('textarea[name="textfile"]').val().replace(/\r\n/g, "\n").split("\n");
		var fullcontent_editor = jQuery("textarea[name='textfile']").val();
		mff_array_textfile = jQuery("textarea[name='textfile']").val().split("\n");
		//test url preview
		for (var i = 0; i < mff_array_textfile.length; i++) {
			if (mff_array_textfile[i].indexOf('test_url:') > (-1)) {
				preview_url_true = mff_array_textfile[i].split('test_url:');
				var url_to_preview = preview_url_true.pop();
				if (url_to_preview.trim().indexOf($("#filename").val().trim()) > -1) {
					//not	
				} else {
					alert(fullcontent_settings_object.notmach);
					return false;
				}
			}
		}
		//closed if url preview

		var test_url = '';
		var i = lines.length;
		while (i--) {
			if (lines[i].toLowerCase().indexOf("test_url") >= 0) {
				test_url = lines[i].substring(9);
				break;
			}
		}
		test_url = test_url.trim();
		//alert(test_url);
		$('#statusmessage').hide();
		$('.spinner').remove();
		//$('#saving').append( '<span class="spinner is-active"></span>' );
		$("#fullsection_messages #section_spinner").prepend('<span class="spinner is-active" style="float:left;"></span>');
		$('#savemessage').removeClass('notice-error').addClass('notice-success').html('<p>' + fullcontent_settings_object.testing + '</p>').show().delay(5000).fadeOut('slow');

		$('#visual').html('');
		$('#text').text('');
		$('#meta_data').html('');

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				test_url: test_url,
				_wpnonce: $('input[name="_wpnonce"]').val(),
				_wp_http_referer: $('input[name="_wp_http_referer"]').val(),
				action: 'wpematico_test_fullcontent',
			},
			dataType: "json",
			success: function (response) {
				response = response.data;
				//$class .= ($mess['below-h2']) ? " below-h2" : "";
				if (response.error) {
					var error_message = response.message;
					console.log('ERROR: ' + error_message);
					$('.spinner').remove();
					//$('#savemessage').removeClass('notice-success').addClass('notice-error').html(error_message).show().delay(15000).fadeOut('slow');
					$('#statusmessage').removeClass('notice-success').addClass('notice-error').html('<p>' + error_message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + fullcontent_settings_object.dismiss + '</span></button>').show();
				} else {
					$('#visual').html(response.htmlcontent);
					$('#text').text(response.htmlcontent);
					$('#meta_data').html(response.html_meta_tags);
					$('#savemessage').removeClass('notice-error').addClass('notice-success').html('<p>' + fullcontent_settings_object.tested + '</p>').show().delay(5000).fadeOut('slow');
					//$('#savingas').html('').fadeOut('slow');
					$('.spinner').remove();
				}
			}
		}).fail(function (response) {
			$('#savemessage').removeClass('notice-success').addClass('notice-error').html('<p>ERROR: ' + fullcontent_settings_object.goeswrong + '</p>').show().delay(5000).fadeOut('slow');
			if (window.console && window.console.log) {
				console.log(response);
			}
			$('.spinner').remove();
		});
	});

	$(document.body).on('click', '.preview_uri', function (e) {
		var test_url = $('#single_uri').val();
		//alert(test_url);
		$('#statusmessage').hide();
		$('.spinner').remove();
		//$('#saving').append( '<span class="spinner is-active"></span>' );
		$("#fullsection_messages #section_spinner").prepend('<span class="spinner is-active" style="float:left;"></span>');
		$('#savemessage').removeClass('notice-error').addClass('notice-success').html('<p>' + fullcontent_settings_object.testing + '</p>').show().delay(5000).fadeOut('slow');
		$('#visual').html('');
		$('#text').text('');
		$('#meta_data').html('');
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				test_url: test_url,
				_wpnonce: $('input[name="_wpnonce"]').val(),
				_wp_http_referer: $('input[name="_wp_http_referer"]').val(),
				action: 'wpematico_test_fullcontent',
			},
			dataType: "json",
			success: function (response) {
				response = response.data;
				//$class .= ($mess['below-h2']) ? " below-h2" : "";
				if (response.error) {
					var error_message = response.message;
					console.log('ERROR: ' + error_message);
					$('.spinner').remove();
					//$('#savemessage').removeClass('notice-success').addClass('notice-error').html(error_message).show().delay(15000).fadeOut('slow');
					$('#statusmessage').removeClass('notice-success').addClass('notice-error').html('<p>' + error_message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + fullcontent_settings_object.dismiss + '</span></button>').show();

				} else {
					$('#visual').html(response.htmlcontent);
					$('#text').text(response.htmlcontent);
					$('#meta_data').html(response.html_meta_tags);
					$('#savemessage').removeClass('notice-error').addClass('notice-success').html(fullcontent_settings_object.tested).show().delay(5000).fadeOut('slow');
					//$('#savingas').html('').fadeOut('slow');
					$('.spinner').remove();
				}
			}
		}).fail(function (response) {
			$('#savemessage').removeClass('notice-success').addClass('notice-error').html('ERROR: ' + fullcontent_settings_object.goeswrong).show().delay(5000).fadeOut('slow');
			if (window.console && window.console.log) {
				console.log(response);
			}
			$('.spinner').remove();
		});
	});

	$(document).ready(function(){
		$('#upload-config-file').change(function(e){
			var fileName = e.target.files[0].name;
			$('#config-file-name').text(fileName);
		});
	});
});