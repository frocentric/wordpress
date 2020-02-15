<?php
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

$the_URL = isset($_GET['get_url']) ? $_GET['get_url'] : '';
$args	 = apply_filters('mmf_getcontents_args', array('curl' => TRUE));
$content = @WPeMatico::wpematico_get_contents($the_URL, $args);
echo $content;
if(stripos($content, 'jquery') === false) {
	do_action('wpematico_full_extractor_print_scripts');
	wp_print_scripts();
}
?>
<!--config mff path-->
<div class="config_mff_extractor">
	<center>
		<table style="width:50%; margin:0px auto; padding:0px;">
			<tr>
				<td style="width:100px;"><label><?php echo __('Class Items', 'wpematico'); ?></label></td>
				<td><input style="width:97%;" readonly="readonly" type="text" class="mff_class_items_extractor"></td>
			</tr>
			<tr>
				<td  style="width:100px;"><label><?php echo __('XPATH', 'wpematico'); ?></label></td>
				<td><input style="width:97%;" readonly="readonly" type="text" class="mff_id_items_extractor"></td>
			</tr>
			<tr>	
				<td colspan="2">
			<center>
				<br>
				<input type="button" class="mff_button_configure" value="<?php echo __('Set Body', 'wpematico'); ?>" id="mff_getbody">
				<input type="button" class="mff_button_configure" value="<?php echo __('Set Strip', 'wpematico'); ?>" id="mff_getstrip">
				<input type="button" class="mff_button_configure" style="display:none;" value="<?php echo __('Reset', 'wpematico'); ?>" id="mff_firebug">
			</center>
			</td>
			</tr>	
		</table>
	</center>
</div>

<div class="FFSelector_label" style="top: 1159px; left: 0px;">
</div>


<script type="text/javascript">
	jQuery(document).ready(function ($) {
		var elements = 'h1,h2,h3,h4,div,p,a,strong,img,article,section';
		var position;
		var label_class_element = '';
		var label_id_element = '';
		var active_extractor = false;


		String.prototype.replaceAll = function (search, replacement) {
			var target = this;
			return target.split(search).join(replacement);
		};
		//search multiple selector
		function search_multiple_selector(parentselector, selector) {
			temp_selector = selector.toLowerCase();
			tagtemp = selector.toLowerCase().split('.');
			if (tagtemp[0] != 'li') {
				selector_origin = selector.toLowerCase();
			} else {
				selector_origin = parentselector.find(">" + selector.toLowerCase() + "");
			}
			if (selector_origin.length > 1) {
				return "yes";
			} else {
				return "no";
			}
		}



		/*mouseover*/
		$(elements).mouseover(function () {
			if (active_extractor == true)
				return false;
			//clear class
			$(".select_item").removeClass('select_item');

			if ($(this).attr("class") != 'config_mff_extractor') {
				tagname = $(this)[0].tagName;
				temp_class_add = "";
				if (tagname == 'a' || tagname == 'A') {
					$(this).parent().addClass('select_item');
					position = $(this).parent().offset();
					temp_element = $(this).parent();
					/*================CLASS ELEMENT===============*/
					label_class_element = temp_element.attr('class');
					label_class_element = label_class_element.replaceAll('select_item', '');
					if (label_class_element != '') {
						//for class javascript
						tagclass = label_class_element.split(' ');
						for (i = 0; i < tagclass.length; i++) {
							if (tagclass[i] != "") {
								if (search_multiple_selector($(this).parent().parent(), $(this).parent()[0].tagName + '.' + tagclass[i] + ">a") == "yes") {
									temp_class_add += "." + tagclass[i];
								}
							}

						}//closed for
						label_class_element = temp_class_add;
						label_class_element = $(this).parent()[0].tagName + label_class_element + '>a';
						label_class_element = label_class_element.toLowerCase();

						//set css select multiples selectors
						$(label_class_element).addClass('select_item');

					} else {
						label_class_element = $(this).parent()[0].tagName + " " + label_class_element + ' a';
						label_class_element = label_class_element.toLowerCase();
						$(label_class_element).addClass('select_item');

					}
					/*=================CLOSE CLASS ELEMENT===================*/
					/*=================ID ELEMENT==========================*/
					if (temp_element.attr('id')) {
						label_id_element = $(this).parent()[0].tagName + '#' + temp_element.attr('id') + " a";
					} else {
						label_id_element = $(this).parent()[0].tagName;
					}

				} else {

					$(this).addClass('select_item');
					temp_element = $(this);
					label_class_element = temp_element.attr('class');
					label_class_element = label_class_element.replaceAll('select_item', '');
					if (label_class_element != '') {
						label_class_element = label_class_element.substring(0, (label_class_element.length - 1)).replaceAll(' ', '.');
						label_class_element = $(this)[0].tagName + "." + label_class_element;
						label_class_element = label_class_element.toLowerCase();
						$(label_class_element).addClass('select_item');

					} else {
						label_class_element = $(this)[0].tagName;
						label_class_element = label_class_element.toLowerCase();
						$(label_class_element).addClass('select_item');

					}
					if (temp_element.attr('id')) {
						label_id_element = $(this)[0].tagName + '#' + temp_element.attr('id');
					} else {
						label_id_element = $(this)[0].tagName;
					}

					position = $(this).offset();

				}
				//set mff_class_items_extractor and mff_id_items_extractor
				$(".mff_class_items_extractor").val(label_class_element);
				$(".mff_id_items_extractor").val(css2xpath(label_class_element));
				//mostramos el elemento
				$(".FFSelector_label").show(0).css({'top': (position.top - 30), 'left': position.left}).text(label_class_element);
			}
			return false;

		});
		$(elements).mouseleave(function () {
			if (active_extractor == true)
				return false;
			if ($(this).attr("class") != 'config_mff_extractor') {
				tagname = $(this)[0].tagName;
				if (tagname == 'a' || tagname == 'A') {
					$(this).parent().removeClass('select_item');
				} else {
					$(this).removeClass('select_item');
				}
			}
			return false;
		});

		$(elements).click(function (e) {
			active_extractor = true;
			$("#mff_firebug").show();
			return false;
		});

		$("#mff_firebug").click(function () {
			$(".mff_class_items_extractor").val("");
			$(".mff_id_items_extractor").val("");
			$(this).hide();
			active_extractor = false;
			return false;
		});

		//get body click
		$("#mff_getbody,#mff_getstrip").click(function () {
			mff_array_textfile = window.opener.jQuery("textarea[name='textfile']").val().split("\n");
			mff_textfile = window.opener.jQuery("textarea[name='textfile']").val();
			if ($(this).attr("id") == 'mff_getbody') {
				for (var i = 0; i < mff_array_textfile.length; i++) {
					if (mff_array_textfile[i].indexOf('body: //') > (-1)) {
						mff_textfile = mff_textfile.replaceAll(mff_array_textfile[i], 'body: ' + css2xpath(label_class_element));
						window.opener.jQuery("textarea[name='textfile']").val(mff_textfile);
						window.opener.jQuery(".preview_txt").addClass('button-disabled');
						window.close();
						return false;
					} else {
						mff_textfile = '\nbody: ' + css2xpath(label_class_element);
						mff_textfile += window.opener.jQuery("textarea[name='textfile']").val();
						window.opener.jQuery("textarea[name='textfile']").val(mff_textfile);
						window.opener.jQuery(".preview_txt").addClass('button-disabled');
						window.close();
						return false;
					}
				}
			} else {

				for (var i = 0; i < mff_array_textfile.length; i++) {
					if (mff_array_textfile[i].indexOf('body: //') > (-1)) {
						preview_strip = mff_array_textfile[i].split(': ');
						mff_textfile = mff_textfile.replaceAll(preview_strip[1], preview_strip[1] + '\n\nstrip: ' + css2xpath(label_class_element));
						window.opener.jQuery("textarea[name='textfile']").val(mff_textfile);
						window.close();
						return false;
					}
				}
			}
			//set textarea
			window.opener.jQuery("textarea[name='textfile']").val(css2xpath(label_class_element));
			return false;  // e.stopPropagation();*/
		});


		$("a").click(function () {
			return false;
		});
	});

	//xpath
	css2xpath = (function () {
		var re = [
			// add @ for attribs
			/\[([^\]~\$\*\^\|\!]+)(=[^\]]+)?\]/g, "[@$1$2]",
			// multiple queries
			/\s*,\s*/g, "|",
			// , + ~ >
			/\s*(\+|~|>)\s*/g, "$1",
			//* ~ + >
			/([a-zA-Z0-9\_\-\*])~([a-zA-Z0-9\_\-\*])/g, "$1/following-sibling::$2",
			/([a-zA-Z0-9\_\-\*])\+([a-zA-Z0-9\_\-\*])/g, "$1/following-sibling::*[1]/self::$2",
			/([a-zA-Z0-9\_\-\*])>([a-zA-Z0-9\_\-\*])/g, "$1/$2",
			// all unescaped stuff escaped
			/\[([^=]+)=([^'|"][^\]]*)\]/g, "[$1='$2']",
			// all descendant or self to //
			/(^|[^a-zA-Z0-9\_\-\*])(#|\.)([a-zA-Z0-9\_\-]+)/g, "$1*$2$3",
			/([\>\+\|\~\,\s])([a-zA-Z\*]+)/g, '$1//$2',
			/\s+\/\//g, '//',
			// :first-child
			/([a-zA-Z0-9\_\-\*]+):first-child/g, "*[1]/self::$1",
			// :last-child
			/([a-zA-Z0-9\_\-\*]+):last-child/g, "$1[not(following-sibling::*)]",
			// :only-child
			/([a-zA-Z0-9\_\-\*]+):only-child/g, "*[last()=1]/self::$1",
			// :empty
			/([a-zA-Z0-9\_\-\*]+):empty/g, "$1[not(*) and not(normalize-space())]",
			// :not
			/([a-zA-Z0-9\_\-\*]+):not\(([^\)]*)\)/g, function (s, a, b) {
				return a.concat("[not(", css2xpath(b).replace(/^[^\[]+\[([^\]]*)\].*$/g, "$1"), ")]");
			},
			// :nth-child
			/([a-zA-Z0-9\_\-\*]+):nth-child\(([^\)]*)\)/g, function (s, a, b) {
				switch (b) {
					case    "n":
						return a;
					case    "even":
						return "*[position() mod 2=0 and position()>=0]/self::" + a;
					case    "odd":
						return a + "[(count(preceding-sibling::*) + 1) mod 2=1]";
					default:
						b = (b || "0").replace(/^([0-9]*)n.*?([0-9]*)$/, "$1+$2").split("+");
						b[1] = b[1] || "0";
						return "*[(position()-".concat(b[1], ") mod ", b[0], "=0 and position()>=", b[1], "]/self::", a);
				}
			},
			// :contains(selectors)
			/:contains\(([^\)]*)\)/g, function (s, a) {
				/*return "[contains(css:lower-case(string(.)),'" + a.toLowerCase() + "')]"; // it does not work in firefox 3*/
				return "[contains(string(.),'" + a + "')]";
			},
			// |= attrib
			/\[([a-zA-Z0-9\_\-]+)\|=([^\]]+)\]/g, "[@$1=$2 or starts-with(@$1,concat($2,'-'))]",
			// *= attrib
			/\[([a-zA-Z0-9\_\-]+)\*=([^\]]+)\]/g, "[contains(@$1,$2)]",
			// ~= attrib
			/\[([a-zA-Z0-9\_\-]+)~=([^\]]+)\]/g, "[contains(concat(' ',normalize-space(@$1),' '),concat(' ',$2,' '))]",
			// ^= attrib
			/\[([a-zA-Z0-9\_\-]+)\^=([^\]]+)\]/g, "[starts-with(@$1,$2)]",
			// $= attrib
			/\[([a-zA-Z0-9\_\-]+)\$=([^\]]+)\]/g, function (s, a, b) {
				return "[substring(@".concat(a, ",string-length(@", a, ")-", b.length - 3, ")=", b, "]");
			},
			// != attrib
			/\[([a-zA-Z0-9\_\-]+)\!=([^\]]+)\]/g, "[not(@$1) or @$1!=$2]",
			// ids and classes
			/#([a-zA-Z0-9\_\-]+)/g, "[@id='$1']",
			/\.([a-zA-Z0-9\_\-]+)/g, "[contains(concat(' ',normalize-space(@class),' '),' $1 ')]",
			// normalize multiple filters
			/\]\[([^\]]+)/g, " and ($1)"
		],
		  length = re.length
		  ;
		return function css2xpath(s) {
			var i = 0;
			while (i < length)
				s = s.replace(re[i++], re[i++]);
			return "//" + s;
		};
	})();


</script>
<style type="text/css">
	.select_item{
		position: relative;
		cursor: pointer;
		outline: 2px solid #09c;
	}
	.FFSelector_label {
		background: #09c;
		border-radius: 2px;
		color: #fff;
		font: bold 12px/12px Helvetica, sans-serif;
		padding: 4px 6px;
		position: absolute;
		text-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);
		z-index: 88888;
		display: none;
	}
	div.config_mff_extractor{
		padding: 20px 15px;
		background-color: #222222;
		position: fixed;
		width: 100%;
		z-index: 99999999;
		bottom: 0;
		left: 0;

	}
	div.config_mff_extractor label{
		font-weight: bold;
		color: #DDDDDD;
	}
	input.mff_button_configure{
		border: 1px solid transparent;
		background: #666 !important;
		cursor: pointer !important;
		-webkit-appearance: button !important;
		padding: 10px 20px !important;
		color: #FFF !important;
	}
	button:active, button:focus, button:hover, html input[type=button]:active, html input[type=button]:focus, html input[type=button]:hover, input[type=reset]:active, input[type=reset]:focus, input[type=reset]:hover, input[type=submit]:active, input[type=submit]:focus, input[type=submit]:hover {
		background: #606060;
	}

</style>