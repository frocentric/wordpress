<?php
// don't load directly 
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

function wpempro_aunx_check_img_ext_enclosure($url) {
	
	$ret = false;
	$allowed = 'jpg,gif,png,tif,bmp,jpeg';
	$allowed = apply_filters('wpematico_allowext', $allowed );
	$extensions_allowed = explode(',', $allowed);
	$others_enclosures_extensions = array('mpeg', 'ogg', 'wav', 'mp4', '3gpp', '3gpp2');
	$img_extension = str_replace( '.', '', strrchr( strtolower($url), '.') );
	// Remove the query of URL if exists.
	if ( stripos($img_extension, '?') !== false ) {
		$pieces_extension = explode('?', $img_extension);
		if ( ! empty($pieces_extension[0]) ) {
			$img_extension = $pieces_extension[0];
		}
	}

	if( in_array( $img_extension, $extensions_allowed ) ) {
		$ret = true;
	} else {
		if ( ! in_array($img_extension, $others_enclosures_extensions) ) {
			$ext_from_header = wpempro_get_img_ext_from_header($url);
			if( in_array( $ext_from_header, $extensions_allowed ) ) {
				$ret = true;
			}
		}
		
	}
	return $ret;
}

function wpempro_get_img_ext_from_header($url) {
	$extension = '';
	stream_context_set_default(array( 'http' => array('method' => 'HEAD'	) ));
	$headers = get_headers($url, 1);
	if ($headers !== false && isset($headers['Content-Type'])) {
		if( strpos($headers['Content-Type'], 'image')!==false ) {
			$parts = explode('/',$headers['Content-Type']);
			$extension = array_pop($parts);
		}
	}
	return $extension;
}

/**
 * wpempro_get_item_tags
 * @param String $selector
 * @param type $item
 * @param type $feed
 * @return type
 * @since 1.8
 */
function wpempro_feed_tags_selector($selector, $item, $feed) {
	$return_value = '';
	$inside_selector = '>';
	$child_selector = '(';
	$attr_selector = '[';
	
	$elements_tags = array();
	$inside_tags = explode($inside_selector, $selector);
	foreach($inside_tags as $tag) {
		$piece_child = explode($child_selector, $tag);
		$new_element = new stdClass();
		$new_element->tag = trim($piece_child[0]);
		$new_element->child_index = 0;
		$new_element->get_attr = '';
		
		$pieces_attr = explode($attr_selector, $new_element->tag);
		$new_element->tag = trim($pieces_attr[0]);
		if (isset($pieces_attr[1])) {
			$new_element->get_attr = str_replace(']', '', $pieces_attr[1]);
			$new_element->get_attr  = trim($new_element->get_attr);
		}
		
		if (isset($piece_child[1])) {
			$new_element->child_index = str_replace(')', '', $piece_child[1]);
			$new_element->child_index  = trim($new_element->child_index);
		}
		$elements_tags[] = $new_element;
	}
	$parent_element = false;
	$current_tag = null;
	foreach ($elements_tags as $element_s) {
		$current_tag = wpempro_get_item_tags($element_s, $item, $feed, $parent_element);
		$parent_element = $current_tag;
	}
	if (is_array($current_tag)) {
		$return_value = $current_tag['data'];
		if (!empty($element_s->get_attr)) {
			$return_value = '';
			if (isset($current_tag['attribs'][''][$element_s->get_attr])) {
				$return_value = $current_tag['attribs'][''][$element_s->get_attr];
			}
		}
	}
	return $return_value;
}
/**
 * wpempro_get_item_tags
 * @param Object $element
 * @param type $item
 * @param type $feed
 * @return type
 * @since 1.8
 */
function wpempro_get_item_tags($element, $item, $feed, $parent_tag = false) {
	$tag = $element->tag;
	$current_item = null;
	$current_ns = '';
	$curr_tag = $tag;
	if (strpos($tag, ':') !== false) { 
		$xml = simplexml_load_string($feed->raw_data);
		$ns = $xml->getNamespaces(true);

		foreach ($ns as $id_ns => $url_ns) {
			$curr_ns = $id_ns;
			if (empty($id_ns)) {
				$curr_ns = $url_ns;
			}
			if (strpos($tag, $curr_ns.':') !== false) {
				$curr_tag = str_replace($curr_ns.':', '', $tag);
				$current_ns = $url_ns;
				break;
			}
		}
	}
	$data_tags_feed = $item->data;
	if (!empty($parent_tag)) {
		$data_tags_feed = $parent_tag;
	}
	if (isset($data_tags_feed['child'][$current_ns][$curr_tag])) {
		$current_item = $data_tags_feed['child'][$current_ns][$curr_tag];
	}

	$current_item = $current_item[$element->child_index];
	return $current_item;
}
/**
 * wpempro_get_feed_tags
 * @param type $tag
 * @param type $item
 * @param type $feed
 * @return type
 * @since 1.8
 * @deprecated this isn't in use.
 */
function wpempro_get_feed_tags($tag, $item, $feed) {
	wpempro_feed_tags_selector($tag, $item, $feed);
	if (strpos($tag, ':') === false) { // It doesn't has an XML namespace.
		return $item->get_item_tags('', $tag);
	}
	$xml = simplexml_load_string($feed->raw_data);
	$ns = $xml->getNamespaces(true);

	foreach ($ns as $id_ns => $url_ns) {
		$curr_ns = $id_ns;
		if (empty($id_ns)) {
			$curr_ns = $url_ns;
		}
		if (strpos($tag, $curr_ns.':') !== false) {
			$curr_tag = str_replace($curr_ns.':', '', $tag);
			return $item->get_item_tags($url_ns, $curr_tag);
		}
	}
	return $tags_value;
}

add_action( 'init', 'wpempro_register_taxonomies', 99 );
function wpempro_register_taxonomies() {
	$taxonomies = get_taxonomies();
	foreach ($taxonomies as $taxonomy) {
		register_taxonomy_for_object_type($taxonomy, 'wpematico');
	}
}	

add_action('admin_menu', 'wpempro_remtax_from_menu',99);
function wpempro_remtax_from_menu() {
	global $submenu, $menu;
	// This needs to be set to the URL for the admin menu section (aka "Menu Page")
	$menu_page = 'edit.php?post_type=wpematico';
	if( !isset($submenu[$menu_page])) {
		foreach($menu as $item=>$arrayval) {
			if($arrayval[0]=="WPeMatico") {
				$menu_page = $arrayval[2];
			}
		}
	}
		
	$taxonomies = get_taxonomies();
	foreach ($taxonomies as $taxonomy) {
		// This needs to be set to the URL for the admin menu option to remove (aka "Submenu Page")
		$taxonomy_admin_page = 'edit-tags.php?taxonomy='.$taxonomy.'&amp;post_type=wpematico';
		// This removes the menu option but doesn't disable the taxonomy
		$submenu[$menu_page] = ( empty($submenu[$menu_page]) ? array() : $submenu[$menu_page]);
		foreach($submenu[$menu_page] as $index => $submenu_item) {
			if ($submenu_item[2]==$taxonomy_admin_page) {
				unset($submenu[$menu_page][$index]);
			}
		}	
	}	

	$cfg = get_option( WPeMaticoPRO :: OPTION_KEY); //PRO settings
	if($cfg['enablepromenu'])
		add_submenu_page(
			'edit.php?post_type=wpematico',
			__( 'PRO Settings', 'wpematico' ),
			'<span class="dashicons dashicons-awards"></span><span>' . __( 'PRO Settings', 'wpematico' ) . "</span>",
			'manage_options',
			'wpematico_settings&tab=prosettings',
			'wpematico_settings&tab=prosettings' 
		);

}

//- Check duplicates by title after change the custom title
//add_filter('wpematico_item_parsers', 'wpematico_check_custom_titles',999,4);
function wpempro_check_custom_titles( $current_item, $campaign, $feed, $item ) {
	global $wpdb;
	$title = $current_item['title'];

	$table_name = $wpdb->prefix . "posts";
	$query="SELECT post_title,id FROM $table_name
				WHERE post_title = '".$title."'
				AND ((`post_status` = 'published') OR (`post_status` = 'publish' ) OR (`post_status` = 'draft' ) OR (`post_status` = 'private' ))";
				//GROUP BY post_title having count(*) > 1" ;
	$row = $wpdb->get_row($query);

	trigger_error(sprintf(__('Checking duplicated title \'%1s\'', 'wpematico' ),$title).': '.((!! $row) ? __('Yes') : __('No')) ,E_USER_NOTICE);
	$dup = !! $row;

	return ($dup) ? -1 : $current_item;
}

/**
 * Checks if all words in array are in a string
 * @param type $string
 * @param array $array
 * @param boolean $anyword to check if a word exist or ALL words exist
 * @return type $boolean True depends $anyword if all words in array are in $string
 */
function wpempro_contains($string, array $array, $anyword = false) {
    $count = 0;
    foreach($array as $value) {
        if (false !== stripos($string,$value)) {
            ++$count;
        };
    }
    return ($anyword) ? $count > 0 : $count == count($array) ;
}

function replace_first_offset($search, $replace, $var, $offset) {
	$pos = strpos($var, $search, $offset);
	$ret = new stdClass();
	$ret->result = $var;
    $ret->pos = $offset;
	if ($pos !== false) {
       $ret->result = substr_replace($var, $replace, $pos, strlen($search));
       $ret->pos = $pos+strlen($replace);
    } 
    return $ret;
}

function wpempro_closetags($html) {
	preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	$openedtags = $result[1];
	preg_match_all('#</([a-z]+)>#iU', $html, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);
	if (count($closedtags) == $len_opened) {
		return $html;
	}
	$openedtags = array_reverse($openedtags);
	for ($i=0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)) {
			$html .= '</'.$openedtags[$i].'>';
		} else {
			unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
	}
	return $html;
} 


// ToDo: deletes after check is working well
// 
//function wpematico_pro_allowext_audio($allowed_audio) {
//	$allowed_audio = 'mp3,m4a,ogg,wav';
//	return $allowed_audio;
//}
//add_filter('wpematico_allowext_audio', 'wpematico_pro_allowext_audio', 10, 1);
//
//
//function wpematico_pro_allowext_video($allowed_video) {
//	$allowed_video = 'mp4,m4v,mov,wmv,avi,mpg,ogv,3gp,3g2';
//	return $allowed_video;
//}
//add_filter('wpematico_allowext_video', 'wpematico_pro_allowext_video', 10, 1);

function wpepro_insert_file_asattach($filename,$postid) {
	$wp_filetype = wp_check_filetype(basename($filename), null );
	$wp_upload_dir = wp_upload_dir();
	$relfilename = $wp_upload_dir['path'] . '/' . basename( $filename );
	$guid = $wp_upload_dir['url'] . '/' . basename( $filename );
	$attachment = array(
	  'guid' => $guid,
	  'post_mime_type' => $wp_filetype['type'],
	  'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
	  'post_content' => '',
	  'post_status' => 'inherit'
	);
	trigger_error(__('Attaching file:').$filename,E_USER_NOTICE);
	$attach_id = wp_insert_attachment( $attachment,  $relfilename, $postid );
	if (!$attach_id)
		trigger_error(__('Sorry, your attach could not be inserted. Something wrong happened.').print_r($filename,true),E_USER_WARNING);
	// must include the image.php file for the function wp_generate_attachment_metadata() to work
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $relfilename );
	wp_update_attachment_metadata( $attach_id,  $attach_data );
	
	return $attach_id;
}

function wpepro_delete_text_from_words($end_line_arr, $string, $keep_word = true) {
	$index_end_line = PHP_INT_MAX;
	$curr_pharse = '';
    foreach ($end_line_arr as $kl => $elval) {
        $index_curr_end_line = stripos($string, $elval);
        if ($index_curr_end_line === false) {
            $index_curr_end_line = PHP_INT_MAX;
        } 
        if ($index_curr_end_line < $index_end_line) {
            $index_end_line = $index_curr_end_line;
            $curr_pharse = $elval;
        }
    }

    if ($index_end_line <  PHP_INT_MAX) {
        $string = substr($string, 0, $index_end_line);
        
        if ($keep_word) {
            $string .= $curr_pharse; // don't uses $phrase to keep Case-sensitive
        }
    
        trigger_error('<strong>'.sprintf(__('Deleting since word: %s.','wpematico'), $curr_pharse).'</strong>', E_USER_NOTICE);
    }
    return $string;
}
if (!function_exists('wpepro_mb_str_word_count')) {
    function wpepro_mb_str_word_count($string, $format = 0, $charlist = '[]') {
    	$string = addslashes($string);
    	$string = addcslashes($string, "?.+*'");
        $tags = preg_split('~[^\p{L}\p{N}\']+~u', $string,-1, PREG_SPLIT_NO_EMPTY);
        return $tags;
    }
}
function wpepro_mb_wordcount($string, $limit = 0, $endstr = ' ...'){
    # strip all html tags
    
	$string = wp_specialchars_decode($string, ENT_QUOTES );
	$text = strip_tags($string);
/*	# remove 'words' that don't consist of alphanumerical characters or punctuation
	$pattern = "#[^(\w|\d|\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]+#";
	$text = trim(preg_replace($pattern, " ", $text));

	# remove one-letter 'words' that consist only of punctuation
	$text = trim(preg_replace("#\s*[(\'|\"|\.|\!|\?|;|,|\\|\/|\-|:|\&|@)]\s*#", " ", $text));

	# remove superfluous whitespace
	$text = preg_replace("/\s\s+/", " ", $text);
 */
	$characterMap = 'áéíóúüñ';
	$words = wpepro_mb_str_word_count($text, 2, $characterMap); 
	# remove empty elements
	$words = array_filter($words);
	
	$count = count($words);
	
    if ($limit > 0) {
	  	$pos = array_keys($words);
      	if ($count > $limit) {
      		$word_search = stripcslashes($words[$pos[$limit]]);
      		$word_search = stripslashes($word_search);
      		
      		$pos_search = mb_strpos($string, $word_search);
      
      		$pieces_word = explode(' ', $string);
      	  	array_splice($pieces_word, $limit+1);

      	  	if (isset($pieces_word[$limit -1])) {
      	  		$last_count = count($pieces_word);
      	  		$offset_search = 0;
      	  		foreach ($pieces_word as $key => $str_word) {
      	  			if ( $key ==  ($last_count - 1) ) {
      	  				break;
      	  			}
      	  			$offset_search += mb_strlen($str_word);
      	  		}
   
      	  		$pos_search_piece = mb_strpos($string, $pieces_word[$last_count -1], $offset_search);
      	  		
      	  	} else {
      	  		$pos_search_piece = 0;
      	  	}

      	  	if ($pos_search_piece > $pos_search) {
      	  		$pos_search = $pos_search_piece;
      	  	}
      	  

      	  	if ($pos_search === false) {

      	  	} else {
      	  		$text = mb_substr($text, 0,  $pos_search) . $endstr;
      	  	}
      }
	}
    return ($limit==0) ? $count : $text;
 }