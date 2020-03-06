jQuery(document).ready(function(jQuery) {
	/* * * Begin Post Creation JS * * */

	jQuery("#ninja_forms_post_add_tag").click(function(e){
		e.preventDefault();
		var value = jQuery("#ninja_forms_post_tag").val();
		value = value.split(',');
		for (var i = value.length - 1; i >= 0; i--) {
			var tag = value[i].replace(/^\s\s*/, '').replace(/\s\s*$/, '');
			ninja_forms_post_add_tag(tag);
		};
		
		jQuery("#ninja_forms_post_tag").val('');
		jQuery("#ninja_forms_post_tag").focus();
	});

	jQuery("body").on("click", ".ninja-forms-del-tag", function(e){
		e.preventDefault();
		jQuery(this).parent().fadeOut('fast', function(){
			jQuery(this).remove();
			var tag_list = '';
			var x = 0;
			jQuery(".tagchecklist > span").each(function(){
				if(x > 0){
					tag_list += ', ';
				}
				tag_list += this.id;
				x++;
			});
			jQuery("#ninja_forms_post_tag_hidden").val(tag_list);
		});
	});

	jQuery(".ninja-forms-tag").click(function(e){
		e.preventDefault();
		var tag = this.innerHTML;
		ninja_forms_post_add_tag(tag);
	});

	jQuery("#ninja_forms_show_tag_cloud").click(function(e){
		e.preventDefault();
		jQuery("#ninja_forms_tag_cloud").fadeToggle();
	});

	jQuery("body").on("click", ".ninja-forms-terms-tab", function(e){
		e.preventDefault();
		jQuery(this).parent().parent("> li").addClass("hide-if-no-js");
		jQuery(this).parent().parent("> li").removeClass("tabs");
		jQuery(this).parent().removeClass("hide-if-no-js");
		jQuery(this).parent().addClass("tabs");

		var id = this.id.replace("_link", "");

		jQuery("." + this.name + "-tabs-panel").hide();
		
		jQuery("#" + id).show();

	});

	jQuery(".term-add-toggle").click(function(e){
		e.preventDefault();
		var tax = this.id.replace('_add_toggle', '');
		jQuery("#" + tax + "_add").toggle();
	});

	jQuery(".new-term-label").focus(function(){
		var label = jQuery(".new-term-label").val();
		if(this.value == label){
			this.value = '';
		}
	});	

	jQuery(".new-term-label").blur(function(){
		var id = this.id.replace("_label", "_default");
		var label = jQuery("#" + id).val();
		if(this.value == ''){
			this.value = label;
		}
	});

	jQuery(".term-add-submit").click(function(e){
		e.preventDefault();
		var tax = this.id.replace("_add_submit", "");
		var field_id = this.name.replace("new_", "");
		field_id = field_id.replace("_tax", "");

		jQuery("#" + tax + "_tabs > li").addClass("hide-if-no-js");
		jQuery("#" + tax + "_tabs > li").removeClass("tabs");
		jQuery("#all_" + tax + "_tab").removeClass("hide-if-no-js");
		jQuery("#all_" + tax + "_tab").addClass("tabs");

		jQuery("." + tax + "-tabs-panel").hide();
		jQuery("#" + tax + "_all").show();

		
		var append = true;
		var new_term_name = jQuery("#new_" + tax + "_label").val();
		var count = jQuery(".new-" + tax).length;
		count --;
		jQuery("#" + tax + "_checklist").find("span").each(function(){
			if(this.innerHTML.toLowerCase() == new_term_name.toLowerCase()){
				var checked_class = jQuery(this).prev(":checkbox:first").prop("id");
				jQuery("." + checked_class).prop("checked", "checked");
				append = false;
				return false;
			}
		});

		
		if(append){
			var clone = jQuery("#term_" + field_id + "_li_template").clone();
			var parent_term = jQuery("#" + tax + "_parent").val();
			var current_html = jQuery(clone).find("label").prop("innerHTML");

			jQuery(clone).prop("id", tax + "_new-" + count + "_li");
			jQuery(clone).attr("style", "");
			jQuery(clone).find("span").prop("innerHTML", new_term_name);
			jQuery(clone).find(".term-parent").val(parent_term);

			var hidden_name = jQuery(clone).find(".term-parent").prop("id");
			hidden_name = hidden_name.replace("[]", "[new-" + count + "]");
			jQuery(clone).find(".term-parent").attr("name", hidden_name);

			jQuery(clone).find(":checkbox").val(new_term_name);

			var check_name = jQuery(clone).find(":checkbox").prop("id");
			check_name = check_name.replace("[]", "[new-" + count + "]");
			jQuery(clone).find(":checkbox").attr("name", check_name);
	
			if(parent_term == -1){
				jQuery("#" + tax + "_checklist").append(clone);
				var html = '<option value="new-' + count + '">' + new_term_name + '</option>';
				jQuery("#" + tax + "_parent").append(html);
			}else{
				if(jQuery("#" + tax + "_" + parent_term + "_children").length == 0){
					var html = '<ul class="children termchecklist form-no-clear" id="' + tax + '_' + parent_term + '_children"></ul>';
					jQuery("#" + tax + "_" + parent_term + "_li").append(html);
				}
				jQuery("#" + tax + "_" + parent_term + "_children").append(clone);
			}
			
			jQuery("#" + tax + "_parent").val(-1);
		}

		jQuery("#new_" + tax + "_label").val("").focus();
	
	});

	jQuery("body").on("change", ".cat-checkbox", function(){
		var checked = this.checked;
		jQuery("." + this.id).each(function(){
			this.checked = checked;
		});
	});

	jQuery(".ninja-forms-feditor-delete-sub").click(function(e){
		return confirm( ninja_forms_feditor_settings.delete );
	});

	jQuery(".ninja-forms-feditor-delete-post").click(function(e){
		return confirm( ninja_forms_feditor_settings.delete );
	});

});

function ninja_forms_post_add_tag(tag){
	//var tag = tag;
	var count = jQuery(".tagchecklist > span").length;
	var add = 1;			
	jQuery(".tagchecklist > span").each(function(){
		if(this.id == tag){
			add = 0;
		}
	});
	if(add == 1){
		var tag_id = tag.replace(/[^A-Z0-9ÆØÅã]+/ig,"-");
		// console.log(tag_id);
		var html = '<span id="' + tag_id + '" style="display:none;"><a id="post_tag-' + count + '" class="ninja-forms-del-tag">X</a>&nbsp;' + tag + '</span>';
		jQuery(".tagchecklist").append(html);
		jQuery("#" + tag_id).fadeIn();
		var tag_list = '';
		var x = 0;
		jQuery(".tagchecklist > span").each(function(){
			if(x > 0){
				tag_list += ', ';
			}
			tag_list += this.id;
			x++;
		});
		jQuery("#ninja_forms_post_tag_hidden").val(tag_list);
	}
}