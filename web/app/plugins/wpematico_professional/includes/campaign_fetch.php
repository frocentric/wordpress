<?php
/**
* It will be used to manage all feature on the campaign fetching.
* @package     WPeMatico Professional
* @subpackage  Campaign fetch.
* @since       1.7.5
*/
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
* Campaign Fetch Class 
* @since 1.7.5
*/
if (!class_exists('WPeMaticoPro_Campaign_Fetch')) :
class WPeMaticoPro_Campaign_Fetch {
	public static $current_options = null;
	public static $current_options_core = null;
	public static $options_images = null;
	public static $options_audios = null;
	public static $options_videos = null;
	public static $current_user_agent = null;
	public static $current_input_encoding = null;
	public static $fetching_campaign = array();

	public static $assig_taxonomies = array();

	public static $current_key_feed = -1;
	/**
	* Static function before_fetching
	* This function is executed before @hooks
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function before_fetching() {

		add_filter('wpematico_check_fetch_feed_params', array(__CLASS__, 'force_feed'), 11, 3);
		add_filter('wpematico_fetch_feed_params_test', array(__CLASS__, 'force_feed'), 11, 3);

		add_filter('wpematico_check_fetch_feed_params', array(__CLASS__, 'sanitize_google_feed_url'), 10, 3);
		
		add_filter('wpematico_fetch_feed_params_test', array(__CLASS__, 'user_agent'), 10, 3);
		add_filter('wpematico_simplepie_user_agent', array(__CLASS__, 'add_user_agent'), 10, 2);
		add_filter('wpematico_check_fetch_feed_params', array(__CLASS__, 'user_agent'), 11, 3);
		
		
		add_filter('wpematico_fetch_feed_params_test', array(__CLASS__, 'feed_param_cookies'), 10, 3);
		add_filter('wpematico_check_fetch_feed_params', array(__CLASS__, 'feed_param_cookies'), 11, 3);
		add_filter('wpematico_preview_fetch_feed_params', array(__CLASS__, 'feed_param_cookies'), 11, 3);
		add_filter('wpematico_preview_item_fetch_params', array(__CLASS__, 'feed_param_cookies'), 11, 3);
		add_filter('wpematico_get_contents_request_params', array(__CLASS__, 'add_get_contents_cookies'), 10, 2);
		add_filter('wpematico_save_file_from_url_params', array(__CLASS__, 'add_save_file_cookies'), 10, 2);
		
		add_filter('wpematico_fetchfeed', array(__CLASS__, 'add_simplepie_cookies'), 10, 2);
		

		
	}
	
	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.7.5
	*/
	public static function fetching($campaign) {

		
		if (is_null(self::$current_options)) {
			self::$current_options = get_option(WPeMaticoPRO::OPTION_KEY);
		}
		if (is_null(self::$current_options_core)) {
			self::$current_options_core = get_option('WPeMatico_Options');
			self::$current_options_core = apply_filters('wpematico_check_options', self::$current_options_core);
		}
		self::$fetching_campaign = $campaign;

		if (is_null(self::$options_images)) {
			self::$options_images = WPeMatico::get_images_options(self::$current_options_core, $campaign);
		}
		if (is_null(self::$options_audios)) {
			self::$options_audios = WPeMatico::get_audios_options(self::$current_options_core, $campaign);
		}
		if (is_null(self::$options_videos)) {
			self::$options_videos = WPeMatico::get_videos_options(self::$current_options_core, $campaign);
		}
		add_filter('wpematico_simplepie_url' , array( __CLASS__, 'assign_feed_key'), 10, 3);
		if (@self::$current_options['enablemultifeed']) {					
			add_filter('wpematico_simplepie_url' , array( __CLASS__, 'multifeed_urls'), 30, 3);
		}

		add_filter('wpematico_fetch_feed_params', array(__CLASS__, 'force_feed'), 10, 3);
		add_filter('wpematico_fetch_feed_params', array(__CLASS__, 'sanitize_google_feed_url'), 11, 3);
		
		
		add_filter('wpematico_fetch_feed_params', array(__CLASS__, 'user_agent'), 12, 3);
		add_filter('wpematico_fetch_feed_params', array(__CLASS__, 'input_encoding'), 13, 3);
		
		add_filter('wpematico_fetchfeed', array(__CLASS__, 'set_input_encoding'), 20, 2);
		add_filter('wpematico_custom_chrset', array(__CLASS__, 'set_input_encoding_funct'), 20, 1);

		if (@self::$current_options['enable_filter_per_author']) {
			add_filter('wpematico_excludes', array(__CLASS__, 'filter_authors'), 11, 4);
		}

		if( (isset($campaign['campaign_delfphrase']) && !empty($campaign['campaign_delfphrase'])) ) {
			add_filter('wpematico_item_parsers', array(__CLASS__, 'strip_lastphrasetoend'),28,4 );
		}


		if( isset(self::$options_audios['strip_all_audios']) && self::$options_audios['strip_all_audios'] ){
			add_filter('wpematico_item_filters_pre_audio', array(__CLASS__, 'strip_audio_tags_content'),10, 2);
		}
		if( isset(self::$options_videos['strip_all_videos']) && self::$options_videos['strip_all_videos'] ){
			add_filter('wpematico_item_filters_pre_video', array(__CLASS__, 'strip_video_tags_content'),10, 2);
		}

		if( isset(self::$options_audios['clean_audios_urls']) && self::$options_audios['clean_audios_urls']) {
			add_filter('wpematico_audio_src_url',	array(__CLASS__, 'audio_src_cleaner'),10,1 );
		}
		if( isset(self::$options_videos['clean_videos_urls']) && self::$options_videos['clean_videos_urls']) {
			add_filter('wpematico_video_src_url',	array(__CLASS__, 'video_src_cleaner'),10,1 );
		}

		if( isset(self::$options_audios['enable_audio_rename']) && self::$options_audios['enable_audio_rename'] ) {
			add_filter('wpematico_new_audio_name',	array(__CLASS__, 'audio_rename'),10,3 );
		}

		if( isset(self::$options_videos['enable_video_rename']) && self::$options_videos['enable_video_rename'] ) {
			add_filter('wpematico_new_video_name',	array(__CLASS__, 'video_rename'),10,3 );
		} 
		
		if( isset($campaign['campaign_enableimgrename']) && $campaign['campaign_enableimgrename'] ) {
			add_filter('wpematico_newimgname',	array(__CLASS__, 'image_rename'),10,3 );
		}

		if( isset($campaign['clean_images_urls']) && $campaign['clean_images_urls'] ) {
			add_filter('wpematico_img_src_url',	array(__CLASS__, 'img_src_cleaner'),10,1 );
		}
				
		if (self::$current_options['enable_custom_feed_tags']) {
			add_filter('wpematico_add_template_vars', array(__CLASS__, 'template_custom_feed_tags'), 20, 5);
		}

		if (self::$current_options['enable_word_to_taxonomy']) {
			add_filter('wpematico_pre_insert_post', array(__CLASS__, 'assign_taxonomies'), 10, 2);
			add_action('wpematico_inserted_post', array(__CLASS__, 'insert_word2taxonomies'), 10, 3);
		}

		if( isset($campaign['video_decode_html_ent_url']) && $campaign['video_decode_html_ent_url'] ) {
			add_filter('wpematico_video_src_url',	array(__CLASS__, 'media_html_entities_url'), 5, 1);
		}
		if( isset($campaign['video_follow_redirection']) && $campaign['video_follow_redirection'] ) {
			add_filter('wpematico_video_src_url',	array(__CLASS__, 'media_follow_redirection_url'), 6, 1);
		}

		if( isset($campaign['audio_decode_html_ent_url']) && $campaign['audio_decode_html_ent_url'] ) {
			add_filter('wpematico_audio_src_url',	array(__CLASS__, 'media_html_entities_url'), 5, 1);
		}
		if( isset($campaign['audio_follow_redirection']) && $campaign['audio_follow_redirection'] ) {
			add_filter('wpematico_audio_src_url',	array(__CLASS__, 'media_follow_redirection_url'), 6, 1);
		}

		add_filter('wpematico_add_template_vars', array(__CLASS__, 'template_feed_sitename'), 20, 5);

		add_filter('wpematico_item_pre_media', array(__CLASS__, 'exclude_filters'), 10, 4);
		
		
	}
	public static function get_author_from_feed($current_item, $campaign, $feed, $item) {
		$fauthor = $item->get_author();
		if (!empty($fauthor)) {
			$feed_name_author = '';
			if (!empty($fauthor->name)) {
				$feed_name_author = $fauthor->name;
			}
			if (!empty($fauthor->email) && empty($feed_name_author)) {
				$feed_name_author = $fauthor->email;
			}

			if (!empty($feed_name_author)) {
				$args = array(
			  		'search' => $feed_name_author, 
			  		'search_fields' => array('user_login','user_nicename','display_name')
				);
				$user_query = new WP_User_Query($args);
				$user_result = $user_query->get_results();
				if (empty($user_result)) {

					$userdata = array(
						//Filter to allow an external parser for the author name 
					    'user_login'  => apply_filters('wpempro_feed_name_author', $feed_name_author),
					    'user_pass'   =>  md5($feed_name_author.time()),
					    'display_name'=>  $feed_name_author,
					    'role'		  => 'author',
					);
					$user_id = wp_insert_user($userdata) ;
					if (!is_wp_error($user_id)) {
					    $current_item['author'] = $user_id;
					}

				} else {

					if (isset($user_result[0]->data->ID)) {
						$current_item['author'] = $user_result[0]->data->ID;
					}
					
				}
				
			}
			
		}
		return $current_item;
	}
	public static function assign_feed_key($feed, $kf, $campaign) {
		self::$current_key_feed = $kf;
		return $feed;
	}

	//* Return TRUE if skip the item 
	public static function exclude_filters($current_item, $campaign, $feed, $item ) {
		if ($current_item == -1) {
			return -1;
		}
		if (self::$current_options['enablekwordf']) {
			$campaign_kwordfinc=(isset($campaign['campaign_kwordf']['inc']) && !empty($campaign['campaign_kwordf']['inc']) ) ? true : false;
			$campaign_kwordfexc=(isset($campaign['campaign_kwordf']['exc']) && !empty($campaign['campaign_kwordf']['exc']) ) ? true : false;
			if ($campaign_kwordfinc || $campaign_kwordfexc ) {
				trigger_error(sprintf(__('Processing Keyword filtering %1s','wpematico'),$item->get_title()),E_USER_NOTICE);
				if(! self::KeywordFilter($current_item, $campaign, $item )) {
					return -1;
				}
			}
		}
		return $current_item;
	}
	/**
	 * Keyword filtering
	 * @param type array $current_item
	 * @param type array $campaign
	 * @param type item Simplepie object $item
	 * @return boolean TRUE if is allowed, FALSE if must skip
	 */
	public static function KeywordFilter(&$current_item, &$campaign, &$item ) {
			if (!function_exists('wpempro_contains')) {
				require_once 'includes/functions.php';
			}
			// Item content  //Todavia no tengo los contenidos (chequea los del feed)
			$content = $current_item['content'];
			$title = $current_item['title'];
			$categories = "";
			if($campaign['campaign_kwordf']['inccat']) {
				if ($autocats = $item->get_categories()) {
					trigger_error(__('Checking KeyWords in Categories.', 'wpematico' ) ,E_USER_NOTICE);
					foreach($autocats as $id => $catego) {
						$categories .= ','.$catego->term;
					}
					$categories = substr($categories, 1);
				}
			}

			// ***** Must include if at least one checkbox are checked
			if($campaign['campaign_kwordf']['inctit'] || $campaign['campaign_kwordf']['inccon'] || $campaign['campaign_kwordf']['inccat'] ) {
				$campaign_kwordf=(isset($campaign['campaign_kwordf']['inc']) && !empty($campaign['campaign_kwordf']['inc']) ) ? $campaign['campaign_kwordf']['inc'] : "";
				$keyarr=explode("\n",$campaign_kwordf);	 
				foreach($keyarr  as  $key=>$value){
				   $value=trim($value);  //  check the value for  empty line 
				   if  (!empty($value))	   {
						$words['inc'][]= $value;
				   }
				}
				$foundit = false;
				if( isset($words) && !empty($words) ) {
					if($campaign['campaign_kwordf']['inc_anyall'] == 'anyword' ) {
						// Must contain any word in title, in content OR in source tag
						$foundtit = $foundcon = $foundcat = false;
						if($campaign['campaign_kwordf']['inctit']) { //title 
							$foundtit =  wpempro_contains($title, $words['inc'], true);
						}
						if($campaign['campaign_kwordf']['inccon']) { //content
							$foundcon =  wpempro_contains($content, $words['inc'], true);
						}
						if($campaign['campaign_kwordf']['inccat']) { //in categories
							$foundcat =  wpempro_contains($categories, $words['inc'], true);
						}

						$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
						if ($foundit !== false ) {  
							trigger_error( __('Must contain: Found a keyword. Continuing...','wpematico'), E_USER_NOTICE );
						}else{
							trigger_error( __('Skiping: Must contain: Do not found any Keyword.','wpematico'), E_USER_WARNING );
							return false;
						}

					}else{
						// All Words must be in one field ?
	/*					$foundtit = $foundcon = $foundcat = false;
						if($campaign['campaign_kwordf']['inctit']) { //title 
							$foundtit =  wpempro_contains($title, $words['inc']);
						}
						if($campaign['campaign_kwordf']['inccon']) { //content
							$foundcon =  wpempro_contains($content, $words['inc']);
						}
						if($campaign['campaign_kwordf']['inccat']) { //in categories
							$foundcat =  wpempro_contains($categories, $words['inc']);
						}
						$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
						if ($foundit !== false ) {  // a la primera que no encuentra ya se vuelve
							trigger_error(sprintf(__('Found all KeyWords!','wpematico')),E_USER_NOTICE);
						}else{
							trigger_error(sprintf(__('Skiping: Not found some Keyword','wpematico')),E_USER_WARNING);
							return false;
						}
	*/
						// All Words can be by summing the 3 fields or all words 1 by 1 ?
						$foundit = false;

						for ($i = 0; $i < count($words['inc']); $i++) {
							$word = $words['inc'][$i];
							$foundtit = $foundcon = $foundcat = false;
							if($campaign['campaign_kwordf']['inctit']) { //title 
								$foundtit =  stripos($title, $word);
								$foundtit = ($foundtit !== false) ? true : false;
							}
							if($campaign['campaign_kwordf']['inccon']) { //content
								$foundcon =  stripos($content, $word);
								$foundcon = ($foundcon !== false) ? true : false;
							}
							if($campaign['campaign_kwordf']['inccat']) { //categories
								$foundcat =  stripos($categories, $word);
								$foundcat = ($foundcat !== false) ? true : false;
							}

							$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
							if ($foundit !== false ) {
								trigger_error(sprintf(__('MC:Found!: word %1s','wpematico'),$word),E_USER_NOTICE);
							}else{
								trigger_error(sprintf(__('MC:Skiping: Not found word %1s in content or title %2s.','wpematico'),$word,$title),E_USER_WARNING);
								return false;
							}
						}  // for i
					}
				}
			
				$foundit = false;
				$incregex = stripslashes($campaign['campaign_kwordf']['incregex']);
				if(!empty($incregex)) {
					$foundtit = $foundcon = $foundcat = false;
					if($campaign['campaign_kwordf']['inctit'] ) { //title 
						$foundtit = (preg_match($incregex, $title)) ? true : false;
					}
					if($campaign['campaign_kwordf']['inccon']) { //content
						$foundcon = (preg_match($incregex, $content)) ? true : false;
					}
					if($campaign['campaign_kwordf']['inccat']) { //categories
						$foundcat = (preg_match($incregex, $categories)) ? true : false;
					}

					$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
					if ($foundit !== false ) {  
						trigger_error(sprintf(__('Must contain: Found regex %1s. Continuing...','wpematico'),$incregex),E_USER_NOTICE);
					}else{
						trigger_error(sprintf(__('Skiping: Must contain do not found regex %1s.','wpematico'),$incregex),E_USER_WARNING);
						return false;
					}
				}
			}
					
			// ************ Cannot contain "exclude" *************************************
			// ***** Must include if at least one checkbox are checked
		if($campaign['campaign_kwordf']['exctit'] || $campaign['campaign_kwordf']['exccon'] || $campaign['campaign_kwordf']['exccat']) {
			$campaign_kwordf=(isset($campaign['campaign_kwordf']['exc']) && !empty($campaign['campaign_kwordf']['exc']) ) ? $campaign['campaign_kwordf']['exc'] : "";
			$keyarr=explode("\n",$campaign_kwordf);	 
			foreach($keyarr  as  $key=>$value){
				$value=trim($value);  //  check the value for  empty line 
				if  (!empty($value)) {
					$words['exc'][]= $value;
			    }
			}
			$foundit = false;
			if( isset($words) && !empty($words) ){
				if($campaign['campaign_kwordf']['exc_anyall'] != 'anyword' ) {
					$foundtit = $foundcon = $foundcat = false;
						// NO Debe contener TODAS las palabras sino dev. false
					if($campaign['campaign_kwordf']['exctit']) { //title 
						$foundtit =  wpempro_contains($title, $words['exc']);
					}
					if($campaign['campaign_kwordf']['exccon']) { //content
						$foundcon =  wpempro_contains($content, $words['exc']);
					}
					if($campaign['campaign_kwordf']['exccat']) { //categories
						$foundcat =  wpempro_contains($categories, $words['exc']);
					}

					$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
					if ($foundit === false ) {  
						trigger_error( __('Cannot contain: Do not found all keywords. Continuing...','wpematico'), E_USER_NOTICE );
					}else{
						trigger_error( __('Skiping: Cannot contain: Found all Keywords.','wpematico'), E_USER_WARNING );
						return false;
					}

				}else{
					// NO Debe contener ALGUNA de las palabras sino dev. false
					$foundit = false;
					for ($i = 0; $i < count($words['exc']); $i++) {
						$word = $words['exc'][$i];
						$foundtit = $foundcon = $foundcat = false;
						if($campaign['campaign_kwordf']['exctit']) { //title 
							$foundtit =  stripos($title, $word);
							$foundtit = ($foundtit !== false) ? true : false;
						}
						if($campaign['campaign_kwordf']['exccon']) { //content
							$foundcon =  stripos($content, $word);
							$foundcon = ($foundcon !== false) ? true : false;
						}
						if($campaign['campaign_kwordf']['exccat']) { //categories
							$foundcat =  stripos($categories, $word);
							$foundcat = ($foundcat !== false) ? true : false;
						}

						$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
						if ($foundit === false ) { 
							trigger_error(sprintf(__('CC:Not Found!: word %1s','wpematico'),$word),E_USER_NOTICE);
						}else{
							trigger_error(sprintf(__('CC:Skiping: Found word %1s in content or title %2s.','wpematico'),$word,$title),E_USER_WARNING);
								return false;
						}
					}
				}
			}

			$foundit = false;
			$excregex = stripslashes($campaign['campaign_kwordf']['excregex']);
			if(!empty($excregex)) {
				$foundtit = $foundcon = $foundcat = false;
				if($campaign['campaign_kwordf']['exctit'] ) { //title 
					$foundtit = (preg_match($excregex, $title)) ? true : false;
				}
				if($campaign['campaign_kwordf']['exccon']) { //content
					$foundcon = (preg_match($excregex, $content)) ? true : false;
				}
				if($campaign['campaign_kwordf']['exccat']) { //categories
					$foundcat = (preg_match($excregex, $categories)) ? true : false;
				}

				$foundit = $foundtit ||	$foundcon || $foundcat;  // found A word in title, in content OR in source tag
				if ($foundit === false ) {  
					trigger_error(sprintf(__('Cannot contain: Not Found regex %1s. Continuing...','wpematico'),$excregex),E_USER_NOTICE);
				}else{
					trigger_error(sprintf(__('Skiping: Cannot contain: found regex %1s.','wpematico'),$excregex),E_USER_WARNING);
					return false;
				}
			}
		}

		return true;
	}

	public static function multifeed_urls($feed, $kf, $campaign) {
		$is_multipagefeed = (!isset($campaign['feed']['is_multipagefeed'][$kf])?false:$campaign['feed']['is_multipagefeed'][$kf]);
		$multifeed_maxpages = (!isset($campaign['feed']['multifeed_maxpages'][$kf])?1:$campaign['feed']['multifeed_maxpages'][$kf]);
		if ($is_multipagefeed && !is_array($feed)) {
			if ($multifeed_maxpages > 1) {
				$array_urls = array();
				$array_urls[] = $feed;
			
				for($p = 2; $p<=$multifeed_maxpages; $p++) {
					$array_urls[] = add_query_arg('paged', $p, $feed );
				}
				return $array_urls;
			}
		}
		return $feed;
	}
	public static function media_html_entities_url($src) {
		trigger_error(sprintf(__('Decoding HTML entities of %1s', 'wpematico' ), $src), E_USER_NOTICE);
		$src = html_entity_decode($src);
		return $src;
	}
	public static function media_follow_redirection_url($src) {
		trigger_error(sprintf(__('Follow redirection of %1s', 'wpematico' ), $src), E_USER_NOTICE);
		
		if (version_compare(phpversion(), '5.3.0', '>=')) { 
			stream_context_set_default(array(
			    'ssl' => array(
			        'verify_peer' => false,
			        'verify_peer_name' => false,
			    ),
			));
		}
		
		$headers = get_headers($src);
		foreach($headers as $header){
			$parts = explode(':', $header, 2);
			if(strtolower($parts[0]) == 'location') {
				$location = trim($parts[1]);
				$url_parts = parse_url($location);
				if (!isset($url_parts['host']) && !isset($url_parts['scheme'])) {
					$src_parts = parse_url($src);
					if (isset($src_parts['host']) && isset($src_parts['scheme'])) {
						$location = $src_parts['scheme'].'://'.$src_parts['host'].$location;
					}
				}
				trigger_error(sprintf(__('New SRC URL: %1s', 'wpematico' ), $location), E_USER_NOTICE);
				return $location;
			}
		}
		return $src;
	}

	public static function template_feed_sitename($vars, $current_item, $campaign, $feed, $item) {
		$current_feed_url = (!empty($feed->feed_url) ? $feed->feed_url : '');
		
		if (!empty($campaign['campaign_feeds'])) {
			
			$index_feed = array_search($current_feed_url, $campaign['campaign_feeds']);
			
			if ( ($index_feed === false || $index_feed === null) && self::$current_key_feed > -1 ) {
				$index_feed = self::$current_key_feed;
			}

			if ($index_feed !== false && $index_feed !== null) {
				if(empty($campaign['feed']['feed_name'][$index_feed])) {
					$feed_name = str_replace('www.', '', parse_url($current_feed_url, PHP_URL_HOST));
				}else{
					$feed_name = $campaign['feed']['feed_name'][$index_feed];
				}
				$vars['{feed_name}'] = $feed_name;
			}
		}
		return $vars;
	}

	public static function get_word2tax_options($campaign) {
		$options = array();
		$options['word'] 	= array();
		$options['title'] 	= array();
		$options['regex'] 	= array();
		$options['cases'] 	= array();
		$options['post']	= array();
		$options['term'] 	= array();

		if ( empty($campaign['campaign_no_setting_word2tax']) ) {
			$word_to_taxonomy_options = get_option('WPeMaticoPRO_word_to_taxonomy');
			if ( ! empty($word_to_taxonomy_options['word']) ) {
				foreach ($word_to_taxonomy_options['word'] as $key => $val) {

					if ( !empty($campaign['campaign_customposttype']) && $word_to_taxonomy_options['post'][$key] == $campaign['campaign_customposttype']) {
						
						$options['word'][]		= $word_to_taxonomy_options['word'][$key];
						$options['title'][] 	= $word_to_taxonomy_options['title'][$key];
						$options['regex'][] 	= $word_to_taxonomy_options['regex'][$key];
						$options['cases'][] 	= $word_to_taxonomy_options['cases'][$key];
						$options['tax'][] 		= $word_to_taxonomy_options['tax'][$key];
						$options['term'][] 		= $word_to_taxonomy_options['term'][$key];
					
					}

				}
			}
			
		}
		if( !empty($campaign['campaign_word2tax']['word']) ) {
			foreach ($campaign['campaign_word2tax']['word'] as $key => $val) {

				$options['word'][]		= $campaign['campaign_word2tax']['word'][$key];
				$options['title'][] 	= $campaign['campaign_word2tax']['title'][$key];
				$options['regex'][] 	= $campaign['campaign_word2tax']['regex'][$key];
				$options['cases'][] 	= $campaign['campaign_word2tax']['cases'][$key];
				$options['tax'][] 		= $campaign['campaign_word2tax']['tax'][$key];
				$options['term'][] 		= $campaign['campaign_word2tax']['term'][$key];

			}
		}

		return $options;
	}
	/**
	* Static function assign_taxonomies
	* @access public
	* @return void
	* @since 1.9.3
	*/
	public static function assign_taxonomies($args, $campaign) {
		$word2tax_options = self::get_word2tax_options($campaign);
		
		self::$assig_taxonomies = array();
		
		if( isset($word2tax_options['word']) 
				&& (!empty($word2tax_options['word'][0]) )
				&& (!empty($word2tax_options['tax'][0]) )
				&& (!empty($word2tax_options['term'][0]) )
			)
		{	
			trigger_error(sprintf(__('Processing Words to Taxonomies of %1s', 'wpematico' ), $args['post_title'] ),E_USER_NOTICE);

			foreach ($word2tax_options['word'] as $i => $val) {
				$foundit = false;
				$word = stripslashes(htmlspecialchars_decode(@$word2tax_options['word'][$i]));
				if (isset($word2tax_options['tax'][$i]) && isset($word2tax_options['term'][$i])) {
					$to_tax = $word2tax_options['tax'][$i];
					$to_term = $word2tax_options['term'][$i];
					
					if ($word2tax_options['regex'][$i]) {
						if ($word2tax_options['title'][$i]) {
							$foundit = (preg_match($word, $args['post_title'])) ? true : false; 
						} else {
							$foundit = (preg_match($word, $args['post_content'])) ? true : false; 
						}
					} else {
						if ($word2tax_options['cases'][$i]) {
							if($word2tax_options['title'][$i]) {
								$foundit = strpos($args['post_title'], $word);
							} else {
								$foundit = strpos($args['post_content'], $word);
							}
						} else {
							if ($word2tax_options['title'][$i]) {
								$foundit = stripos($args['post_title'], $word); //insensible a May/min
							} else {
								$foundit = stripos($args['post_content'], $word); //insensible a May/min
							}
						}
					}
					if ($foundit !== false ) {
						trigger_error(sprintf(__('Found!: word %1s to Term_id %2s', 'wpematico' ), $word, $to_term), E_USER_NOTICE);
						if (!isset(self::$assig_taxonomies[$to_tax])) {
							self::$assig_taxonomies[$to_tax] = array();
						}
						self::$assig_taxonomies[$to_tax][] = (int)$to_term;
						
					} else {
						trigger_error(sprintf(__('Not found word %1s', 'wpematico' ),$word),E_USER_NOTICE);
					}
				}
			}
				
			
		}
		return $args;
	}
	/**
	* Static function insert_word2taxonomies
	* @access public
	* @return void
	* @since 1.9.3
	*/
	public static function insert_word2taxonomies($post_id, $campaign, $item) {
		foreach (self::$assig_taxonomies as $taxonomy => $terms) {
			$previous_terms = wp_get_object_terms( $campaign['ID'], $taxonomy );
			$previous_terms_id = array();
		    foreach( $previous_terms AS $t ) {
		        $previous_terms_id[] = $t->term_id;
		    }
			$terms = array_merge($terms, $previous_terms_id);
			$inserted_terms = wp_set_post_terms( $post_id, $terms, $taxonomy);
			if(!empty($inserted_terms)) {
		
				trigger_error( sprintf(__("Added terms of %s taxonomy: ", 'wpematico' ), $taxonomy)  . implode(", ", $inserted_terms), E_USER_NOTICE);
			}
		}
	}
	/**
	* Static function template_custom_feed_tags
	* @access public
	* @return void
	* @since 1.7.4
	*/
	public static function template_custom_feed_tags($vars, $current_item, $campaign, $feed, $item) {
		if (empty($campaign['campaign_cfeed_tags'])) {
			$campaign['campaign_cfeed_tags'] = array('name' => array(''), 'value' => array(''));
		}
		foreach ($campaign['campaign_cfeed_tags']['name'] as $i => $value) {
			$name_tag = ((!empty($campaign['campaign_cfeed_tags']['name'][$i]))? $campaign['campaign_cfeed_tags']['name'][$i] : '');
			$template = ((!empty($campaign['campaign_cfeed_tags']['value'][$i]))? $campaign['campaign_cfeed_tags']['value'][$i] : '');
			if (!empty($template) && !isset($vars[$template]) && !empty($name_tag)) {
				
				$tags_item = wpempro_feed_tags_selector($name_tag, $item, $feed);
				$vars[$template] = $tags_item;
		
			}
		}
		return $vars;
	}

	/**
	* Static function add_simplepie_cookies
	* @access public
	* @param $simplepie SimplePie Object to be filtered.
	* @param $feed_url String contains the Feed URL its would be fetch.
	* @return $simplepie SimplePie Object
	* @since 1.9.0
	*/
	public static function add_simplepie_cookies($simplepie, $feed_url) {
		
		if (!empty(self::$fetching_campaign)) {
			if (!empty(self::$fetching_campaign['campaign_feeds'])) {
				$index_feed = array_search($feed_url, self::$fetching_campaign['campaign_feeds']);

				if ( ($index_feed === false || $index_feed === null) && self::$current_key_feed > -1 ) {
					$index_feed = self::$current_key_feed;
				}

				if ($index_feed !== false && $index_feed !== null) {
					if (!empty(self::$fetching_campaign['feed']['enable_cookies'][$index_feed])) {

						$parsed_url = parse_url($feed_url);
						$host = (isset($parsed_url['host'])? $parsed_url['host']: time());
						WPeMaticoPro_Cookies::$hosts[] = $host;
						$hash_host = md5($host);
						$cookie_file = WPeMaticoPro_Cookies::get_file_path($hash_host);
						$simplepie->curl_options[CURLOPT_COOKIESESSION] = true;
						$simplepie->curl_options[CURLOPT_COOKIEJAR] = $cookie_file;
    					$simplepie->curl_options[CURLOPT_COOKIEFILE] = $cookie_file;
						
					}
				}
			}
		}
		return $simplepie;
	}

	/**
	* Static function add_save_file_cookies
	* @access public
	* @param $ch cURL Handler to be filtered.
	* @param $url String contains the URL its would be fetch.
	* @return $ch cURL Handler
	* @since 1.9.0
	*/
	public static function add_save_file_cookies($ch, $url) {
		$parsed_url = parse_url($url);
		$host = WPeMaticoPro_Cookies::url_is_using_cookie($parsed_url);
		if (!empty($host)) {
			$hash_host = md5($host);
			$cookie_file = WPeMaticoPro_Cookies::get_file_path($hash_host);
			curl_setopt ($ch, CURLOPT_COOKIESESSION, true); 
			curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie_file); 
			curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie_file); 
		}
		return $ch;
	}

	/**
	* Static function add_get_contents_cookies
	* @access public
	* @param $options Array it contains the cURL options to be filtered.
	* @param $url String contains the URL its would be fetch.
	* @return $options Array
	* @since 1.9.0
	*/
	public static function add_get_contents_cookies($options, $url) {
		if (!empty($options['curl'])) {
			$parsed_url = parse_url($url);
			$host = WPeMaticoPro_Cookies::url_is_using_cookie($parsed_url);
			if (!empty($host)) {
				$hash_host = md5($host);
				$cookie_file = WPeMaticoPro_Cookies::get_file_path($hash_host);
				$options['curl_setopt']['CURLOPT_COOKIESESSION'] = true;
				$options['curl_setopt']['CURLOPT_COOKIEJAR'] = $cookie_file;
				$options['curl_setopt']['CURLOPT_COOKIEFILE'] = $cookie_file;
			}
		}
		
		return $options;
	}

	/**
	* Static function feed_param_cookies
	* @access public
	* @return void
	* @since 1.9.0
	*/
	public static function feed_param_cookies($fetch_feed_params, $index_feed, $campaign) {
		if (!empty($campaign['feed']['enable_cookies'][$index_feed])) {
			self::$fetching_campaign = array(
					'campaign_feeds' => array($fetch_feed_params['url']),
					'enable_cookies' => array(true),
				);
			
		}
		return $fetch_feed_params;
	}
	/**
	* Static function add_user_agent
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function add_user_agent($user_agent, $url) {
		if (!empty(self::$current_user_agent)) {
			$user_agent = self::$current_user_agent;
		}
		return $user_agent;
	}
	/**
	* Static function user_agent
	* This function is used to assign a user agent before start the feed fetch
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function user_agent($fetch_feed_params, $index_feed, $campaign) {
		self::$current_user_agent = null;
		if (!empty($campaign['feed']['user_agent'][$index_feed])) {
			$curr_index_agent = $campaign['feed']['user_agent'][$index_feed];
			$user_agents = WPeMaticoPro_Campaign_Edit::get_user_agents();
			if ($curr_index_agent != 'CoreUserAgent' && !empty($user_agents[$curr_index_agent])) {
				self::$current_user_agent = $user_agents[$curr_index_agent];
			}
		}
		return $fetch_feed_params;
	}

	/**
	* Static function input_encoding
	* This function is used to assign a input encoding before start the feed fetch
	* @access public
	* @return void
	* @since 1.9.1
	*/
	public static function input_encoding($fetch_feed_params, $index_feed, $campaign) {
		self::$current_input_encoding = null;
		if (!empty($campaign['feed']['campaign_input_encoding'][$index_feed])) {
			$curr_index_encoding = $campaign['feed']['campaign_input_encoding'][$index_feed];
			$input_encodings = WPeMaticoPro_Campaign_Edit::get_input_encodings();
			if ($curr_index_encoding != 'auto-detect' && !empty($input_encodings[$curr_index_encoding])) {
				self::$current_input_encoding = $input_encodings[$curr_index_encoding];
			}
		}
		return $fetch_feed_params;
	}

	/**
	* Static function set_input_encoding
	* This function is used to assign a input encoding to SimplePie
	* @access public
	* @return $feed SimplePie Object
	* @since 1.9.1
	*/
	public static function set_input_encoding($feed, $url) {
		if (!empty(self::$current_input_encoding)) {
			$feed->set_input_encoding(self::$current_input_encoding);
		}
		return $feed;
	}
	/**
	* Static function set_input_encoding_funct
	* This function filter the input encoding used in change_to_utf8
	* @access public
	* @param $from String with the input encoding 
	* @return $from String with the input encoding that maybe is from HTTP headers.
	* @since 1.9.1
	*/
	public static function set_input_encoding_funct($from) {
		if (!empty(self::$current_input_encoding)) {
			$from = self::$current_input_encoding;
		}
		return $from;
	}


	
	
	
	/**
	* Static function sanitize_google_feed_url
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function sanitize_google_feed_url($fetch_feed_params, $index_feed, $campaign) {
		if( (isset($campaign['fix_google_links']) && !empty($campaign['fix_google_links'])) ) {
			if (stripos($fetch_feed_params['url'], 'google') !== false) {
				$fetch_feed_params['url'] = str_replace('%20', '+', $fetch_feed_params['url']);
			}
		}
		return $fetch_feed_params;
	}
	/**
	* Static function force_feed
	* @access public
	* @return void
	* @since 1.7.5
	*/
	public static function force_feed($fetch_feed_params, $index_feed, $campaign) {
		$force_feed = false;
		if (!empty($campaign['feed']['force_feed'][$index_feed])) {
			$force_feed = true;
		}
		$fetch_feed_params['force_feed'] = $force_feed;
		return $fetch_feed_params;
	}

	/**
	* Static function filter_authors
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function filter_authors($skip, $current_item, $campaign, $item ) {
		$campaign_fauthor_inc_words = (isset($campaign['campaign_fauthor_inc_words']) && !empty($campaign['campaign_fauthor_inc_words']) ) ? true : false;
		$campaign_fauthor_inc_regex = (isset($campaign['campaign_fauthor_inc_regex']) && !empty($campaign['campaign_fauthor_inc_regex']) ) ? true : false;
		$campaign_fauthor_exc_words = (isset($campaign['campaign_fauthor_exc_words']) && !empty($campaign['campaign_fauthor_exc_words']) ) ? true : false;
		$campaign_fauthor_exc_regex = (isset($campaign['campaign_fauthor_exc_regex']) && !empty($campaign['campaign_fauthor_exc_regex']) ) ? true : false;
		$author = $item->get_author();
		if (!$author) {
			return false;
		}
		$author_name = $author->get_name();

		if (empty($author_name)) {
			trigger_error(sprintf(__('Processing Author Filtering: Cannot be executed because the author\'s name is empty.','wpematico'), $author_name), E_USER_NOTICE);
		}
		
		if (($campaign_fauthor_inc_words || $campaign_fauthor_inc_regex || $campaign_fauthor_exc_words || $campaign_fauthor_exc_regex) && !empty($author_name)) {
			trigger_error('<strong>'.sprintf(__('Processing Author Filtering: %1s','wpematico'), $author_name).'</strong>', E_USER_NOTICE);
			if(! self::author_filter_process($current_item, $campaign, $item )) {
				$skip = true;
			}
		}
		
		return $skip;
	}
	public static function author_filter_process(&$current_item, &$campaign, &$item ) {
		
		$author = $item->get_author();
		$author_name = $author->get_name();
		$campaign_fauthor_inc_words = (isset($campaign['campaign_fauthor_inc_words']) && !empty($campaign['campaign_fauthor_inc_words']) ) ? $campaign['campaign_fauthor_inc_words'] : '';
		$campaign_fauthor_inc_regex = (isset($campaign['campaign_fauthor_inc_regex']) && !empty($campaign['campaign_fauthor_inc_regex']) ) ? $campaign['campaign_fauthor_inc_regex'] : '';
		$campaign_fauthor_exc_words = (isset($campaign['campaign_fauthor_exc_words']) && !empty($campaign['campaign_fauthor_exc_words']) ) ? $campaign['campaign_fauthor_exc_words'] : '';
		$campaign_fauthor_exc_regex = (isset($campaign['campaign_fauthor_exc_regex']) && !empty($campaign['campaign_fauthor_exc_regex']) ) ? $campaign['campaign_fauthor_exc_regex'] : '';


		$words_include = array();
		if (!empty($campaign_fauthor_inc_words)) {
			$keyarr = explode("\n", $campaign_fauthor_inc_words);	 
			foreach($keyarr  as  $key=>$value){
			   $value = trim($value);  
			   if (!empty($value))	{ //  check the value for empty line 
					$words_include[] = $value;
			   }
			}
			
			$found_author_include = false;
			foreach ($words_include as $key => $word) {
				$found_author_include =  stripos($author_name, $word);
				$found_author_include = ($found_author_include !== false) ? true : false;
				if ($found_author_include) {
					trigger_error(sprintf(__('Author Must Contain:Found!: word %1s','wpematico'), $word), E_USER_NOTICE);
					return true;
				} else {
					trigger_error(sprintf(__('Author Must Contain:Not Found!: word %1s Continuing...','wpematico'), $word), E_USER_NOTICE);
				}
			}
			trigger_error(sprintf(__('Author Must Contain:Not Found Any Word, Skipping Post...','wpematico'), $word), E_USER_NOTICE);
			return false;
			
		}

		if (!empty($campaign_fauthor_inc_regex)) {
			$found_author_include = (preg_match($campaign_fauthor_inc_regex, $author_name)) ? true : false;
			if ($found_author_include) {
				trigger_error(sprintf(__('Author Must Contain: Found regex %1s.','wpematico'), $campaign_fauthor_inc_regex), E_USER_NOTICE);
				return true;
			}
		}


		$words_exclude = array();
		if (!empty($campaign_fauthor_exc_words)) {
			
			$keyarr = explode("\n", $campaign_fauthor_exc_words);	 
			foreach($keyarr  as  $key=>$value){
			   $value = trim($value);  
			   if (!empty($value))	{ //  check the value for empty line 
					$words_exclude[] = $value;
			   }
			}

			$found_author_include = false;
			foreach ($words_exclude as $key => $word) {
				$found_author_include =  stripos($author_name, $word);
				$found_author_include = ($found_author_include !== false) ? true : false;
				if ($found_author_include) {
					trigger_error(sprintf(__('Author Cannot Contain:Found!: word %1s, Skipping Post...','wpematico'), $word), E_USER_NOTICE);
					return false;
				} else {
					trigger_error(sprintf(__('Author Cannot Contain:Not Found!: word %1s Continuing...','wpematico'), $word), E_USER_NOTICE);
				}
			}

		}
		if (!empty($campaign_fauthor_exc_regex)) {
			$found_author_include = (preg_match($campaign_fauthor_exc_regex, $author_name)) ? true : false;
			if ($found_author_include) {
				trigger_error(sprintf(__('Author Cannot Contain: Found regex %1s, Skipping Post...','wpematico'), $campaign_fauthor_inc_regex), E_USER_NOTICE);
				return false;
			}
		}
		
		return true;
	}

	// Strip all in the content AFTER a word or phrase 
	public static function strip_lastphrasetoend( $current_item, $campaign, $feed, $item ) {
		if($current_item == -1) return -1;
		
		
		if( !empty($campaign['campaign_delfphrase']) ){

			$cfg = self::$current_options;
			$end_line = false;
			if( isset($cfg['end_of_the_line']) && $cfg['end_of_the_line'] && $campaign['campaign_delfphrase_end_line']) {
				if(!defined('WPEBETTEREXCERPTS_VERSION') && !empty($cfg['end_of_the_line_characters'])) {
					$end_line = true;
				}
				if(defined('WPEBETTEREXCERPTS_VERSION')) {
					$better_excerpt = get_option('wpematico_better_excerpts_options');	
					if (!empty($better_excerpt['end_sentence_chrs'])) {
						$end_line = true;
					}
				}
				
			}
			
			$keyarr = explode( "\n", $campaign['campaign_delfphrase'] );
			foreach($keyarr  as  $key=>$value){
				$phrase=trim($value);  //  check the value for  empty line 
				if(!empty($phrase)){
					$index_phrase = stripos($current_item['content'], $phrase);
					if($index_phrase !== FALSE ) { // the string exists
						if ($end_line) {
							$end_line_arr = explode(' ', $cfg['end_of_the_line_characters']);
							if (!empty($better_excerpt['end_sentence_chrs'])) {
								trigger_error('<strong>'.__('Obtaining the characters at the end of the sentence of Better Excerpt.','wpematico').'</strong>', E_USER_NOTICE);
								$end_line_arr = explode(' ', $better_excerpt['end_sentence_chrs']);
							}
						    $index_end_line = PHP_INT_MAX;

						    foreach ($end_line_arr as $kl => $elval) {
						        $index_curr_end_line = stripos($current_item['content'], $elval, $index_phrase);
						        if ($index_curr_end_line === false) {
						            $index_curr_end_line = PHP_INT_MAX;
						        } else {
						            if ($elval == '.') {
						                $check_formats = substr($current_item['content'], $index_curr_end_line, 7);
						                $index_formats = stripos($check_formats, '"');
						                if ($index_formats !== false) {
						                    $index_curr_end_line = stripos($current_item['content'], $elval, $index_curr_end_line+1);
						                    if ($index_curr_end_line === false) {
						                        $index_curr_end_line = PHP_INT_MAX;
						                    }
						                }
						            }
						        }
						        if ($index_curr_end_line < $index_end_line) {
						            $index_end_line = $index_curr_end_line;
						        }
						    }
						    if ($index_end_line <  PHP_INT_MAX) {
						        $content_before = substr($current_item['content'], 0, $index_phrase);
						        $add_content = '';
						        if (isset($campaign['campaign_delfphrase_keep']) && $campaign['campaign_delfphrase_keep']) {
						            $add_content .= $phrase; // don't uses $phrase to keep Case-sensitive
						        }
						        $content_before = $content_before.$add_content; 
						        $content_after = substr($current_item['content'], $index_end_line, -1);
						        $current_item['content'] = wpempro_closetags($content_before.$content_after);
						        trigger_error('<strong>'.sprintf(__('Deleting since phrase: %1s till end of the line.','wpematico'),$phrase).'</strong>', E_USER_NOTICE);
						    }
						} else {
							$add_content = '';
							if (isset($campaign['campaign_delfphrase_keep']) && $campaign['campaign_delfphrase_keep']) {
								$add_content .= substr($current_item['content'], $index_phrase, strlen($phrase)); // don't uses $phrase to keep Case-sensitive
							}
							$current_item['content'] = stristr($current_item['content'], $phrase, true); 
							$current_item['content'] .= $add_content;
							$current_item['content'] = wpempro_closetags($current_item['content']);
							trigger_error('<strong>'.sprintf(__('Deleting since phrase: %1s','wpematico'),$phrase).'</strong>', E_USER_NOTICE);
							break;
						}
					}
				}
			}
		}			
		return $current_item;
	}
	/**
	* Static function strip_audio_tags_content
	* @access public
	* @since 1.6.4
	*/
	public static function strip_audio_tags_content($current_item, $campaign) {
		trigger_error(__('Striped all &#x3C;audio&#x3E; tags from content. ', 'wpematico'), E_USER_NOTICE);
		$current_item['content'] = WPeMatico::strip_tags_content($current_item['content'], '<audio>', TRUE); 
		return $current_item;
	}
	/**
	* Static function strip_video_tags_content
	* @access public
	* @since 1.6.4
	*/
	public static function strip_video_tags_content($current_item, $campaign) {
		trigger_error(__('Striped all &#x3C;video&#x3E; tags from content. ', 'wpematico'), E_USER_NOTICE);
		$current_item['content'] = WPeMatico::strip_tags_content($current_item['content'], '<video>', TRUE); 
		return $current_item;
	}
	/**
	* Static function audio_src_cleaner
	* @access public
	* @param $audio_src_real String audio src to clean.
	* @return $new_audio_name String the new audio src cleaned.
	* @since 1.6.4
	*/
	public static function audio_src_cleaner($audio_src_real) {
		
		preg_match('/[^\/\?]+\.(mp3|m4a|ogg|wav)/i', $audio_src_real, $matches);
		$audio_name = sanitize_file_name(urldecode(basename($matches[0])));
	
		$parts = explode('.', $audio_name);
		$name = array_shift($parts);
		$extension = array_pop($parts);
	
		foreach((array) $parts as $part) {
			$name .= '.' . $part;
		}
		$name = (urldecode($name));
	
		$new_audio_name = dirname($audio_src_real) . '/' . $name . '.' . $extension;

		return $new_audio_name;
	}
	/**
	* Static function video_src_cleaner
	* @access public
	* @param $video_src_real String video src to clean.
	* @return $new_video_name String the new video src cleaned.
	* @since 1.6.4
	*/
	public static function video_src_cleaner($video_src_real) {
		
		preg_match('/[^\/\?]+\.(mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2)/i', $video_src_real, $matches);
		$video_name = sanitize_file_name(urldecode(basename($matches[0])));
	
		$parts = explode('.', $video_name);
		$name = array_shift($parts);
		$extension = array_pop($parts);
	
		foreach((array) $parts as $part) {
			$name .= '.' . $part;
		}
		$name = (urldecode($name));
	
		$new_video_name = dirname($video_src_real) . '/' . $name . '.' . $extension;

		return $new_video_name;
	}
	/**
	* Static function audio_rename
	* @access public
	* @since 1.6.4
	*/
	public static function audio_rename($new_audio_name = '', $current_item = null, $options_audios = array(), $item = null ) {

		if(empty($options_audios['audio_rename'])) {
			return $new_audio_name;
		}
			
		$new_audio_name = self::audio_src_cleaner($new_audio_name);
		preg_match('/[^\/\?]+\.(mp3|m4a|ogg|wav)/i', $new_audio_name, $matches);

		$audio_name = sanitize_file_name(urldecode(basename($matches[0])));
	
		$parts = explode('.', $audio_name);

		$extension = array_pop($parts);
		$extension = (empty($extension)) ? 'mp3' : $extension; // Allways JPG if extension is missing

		$vars = array(
			'{slug}',
			'{title}',
		);
		$replace = array(
			sanitize_file_name( $current_item['title']), //slug
			sanitize_title( $current_item['title']),  //remove tags
		);
		$name = ( ( $options_audios['audio_rename'] ) ? str_ireplace($vars, $replace, stripslashes( $options_audios['audio_rename'] ) ) : $audio_name) .".$extension";
		trigger_error(sprintf(__('Renamed audio %1s -> %2s', 'wpematico'), $audio_name, $name ), E_USER_NOTICE);

		$new_audio_name = $name;
		return $new_audio_name;
	}
	/**
	* Static function video_rename
	* @access public
	* @since 1.6.4
	*/
	public static function video_rename($new_video_name = '', $current_item = null, $options_videos = array(), $item = null ) {

		if(empty($options_videos['video_rename'])) {
			return $new_video_name;
		}
			
		$new_video_name = self::video_src_cleaner($new_video_name);
		preg_match('/[^\/\?]+\.(mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2)/i', $new_video_name, $matches);

		$video_name = sanitize_file_name(urldecode(basename($matches[0])));
	
		$parts = explode('.', $video_name);

		$extension = array_pop($parts);
		$extension = (empty($extension)) ? 'mp4' : $extension; // Allways JPG if extension is missing

		$vars = array(
			'{slug}',
			'{title}',
		);
		$replace = array(
			sanitize_file_name( $current_item['title']), //slug
			sanitize_title( $current_item['title']),  //remove tags
		);
		$name = ( ( $options_videos['video_rename'] ) ? str_ireplace($vars, $replace, stripslashes( $options_videos['video_rename'] ) ) : $video_name) .".$extension";
		trigger_error(sprintf(__('Renamed video %1s -> %2s', 'wpematico'), $video_name, $name ), E_USER_NOTICE);

		$new_video_name = $name;
		return $new_video_name;
	}
	/**
	* Static function image_rename
	* This function rename the images using some vars of template.
	* @access public
	* @return $newimgname String contains the new image renamed.
	* @since 1.9.1
	*/
	public static function image_rename($newimgname, $current_item = null, $campaign = null, $item = null ) {
		
		$newimgname = self::img_src_cleaner($newimgname);
		// Find only image filenames after the / and before the ? sign (? = 3F here)
		preg_match('/[^\/\?]+\.(?:jp[eg]+|png|bmp|giff?|tiff?)/i', $newimgname, $matches);
		// First step of urldecode and sanitize the filename
		$imgname = sanitize_file_name(urldecode(basename($matches[0])));
		// Split the name from the extension
		$parts = explode('.', $imgname);

		$extension = array_pop($parts);
	
		$vars = array(
			'{slug}',
			'{title}',
		);
		$replace = array(
			sanitize_file_name( $current_item['title']), //slug
			sanitize_title( $current_item['title']),  //remove tags
		);
		$name = ( ( $campaign['campaign_imgrename'] ) ? str_ireplace($vars, $replace, stripslashes( $campaign['campaign_imgrename'] ) ) : $imgname) .".$extension";
		trigger_error(sprintf(__('Renamed image %1s -> %2s', 'wpematico'),$newimgname, $name ), E_USER_NOTICE);

		$newimgname = $name;
		return $newimgname;
	}

	/**
	 * Clean parameters or query vars from image url
	 * @access public
	 * @param $imagen_src_real String contains the new image uncleaned.
	 * @return $newimgname String contains the new image cleaned.
	 * @since 1.9.1
	 */
	public static function img_src_cleaner($imagen_src_real) {
		// Find only image filenames after the / and before the ? sign
		preg_match('/[^\/\?]+\.(?:jp[eg]+|png|bmp|giff?|tiff?)/i', $imagen_src_real, $matches);
		// First step of urldecode and sanitize the filename
		$imgname = sanitize_file_name(urldecode(basename($matches[0])));
		// Split the name from the extension
		$parts = explode('.', $imgname);
		$name = array_shift($parts);
		$extension = array_pop($parts);
		// Join all names splitted by dots
		foreach((array) $parts as $part) {
			$name .= '.' . $part;
		}
		// Second step of urldecode and sanitize only the name of the file
		//$name = sanitize_title(urldecode($name));  // Pierde mayusculas
		$name = (urldecode($name));
		// Join the name with the extension
		$newimgname = dirname($imagen_src_real) . '/' . $name . '.' . $extension;

		return $newimgname;
	}	
}
endif;
WPeMaticoPro_Campaign_Fetch::before_fetching();
add_action('Wpematico_init_fetching', array('WPeMaticoPro_Campaign_Fetch', 'fetching'));
?>
