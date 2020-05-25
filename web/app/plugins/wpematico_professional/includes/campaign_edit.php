<?php
/**
* It will be used to manage all feature on the campaign editing.
* @package     WPeMatico Professional
* @subpackage  Campaign edit.
* @since       1.7.5
*/
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
* Campaign Edit Class 
* @since 1.7.5
*/
if (!class_exists('WPeMaticoPro_Campaign_Edit')) :
class WPeMaticoPro_Campaign_Edit {
	public static $current_options = null;
	public static $current_options_core = null;
	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.7.5
	*/
	public static function hooks() {
		if (is_null(self::$current_options)) {
			self::$current_options = get_option(WPeMaticoPRO::OPTION_KEY);
		}
		if (is_null(self::$current_options_core)) {
			self::$current_options_core = get_option('WPeMatico_Options');
		}
		//add_action('wpematico_campaign_feed_body_column', array(__CLASS__, 'advanced_feed_icon'),99,3 );
		add_action('wpematico_campaign_feed_actions_1', array(__CLASS__, 'advanced_feed_icon'),99,3 );
		if (isset(self::$current_options['enablemultifeed']) && self::$current_options['enablemultifeed']) {
			add_action('wpematico_campaign_feed_body_column', array(__CLASS__, 'is_multipage_icon'),97,3 );
			add_action('wpematico_campaign_feed_advanced_options', array(__CLASS__, 'multifeedfields'), 14, 4 );
		}
		add_action('wpematico_campaign_feed_advanced_options', array(__CLASS__, 'avanced_force_feed'), 14, 4 );
		add_action('wpematico_campaign_feed_advanced_options', array(__CLASS__, 'avanced_user_agent'), 25, 4 );
		add_action('wpematico_campaign_feed_advanced_options', array(__CLASS__, 'avanced_input_encodings'), 25, 4 );
		add_action('wpematico_campaign_feed_advanced_options', array(__CLASS__, 'avanced_enable_cookie'), 25, 4 );

		add_action('wpematico_campaign_feed_advanced_options', array(__CLASS__, 'avanced_site_name'), 11, 4 );
		
		if (self::$current_options['enableauthorxfeed']) {
			add_action('wpematico_campaign_feed_header_column', array(__CLASS__, 'headerfeedat'),15 );
			add_action('wpematico_campaign_feed_body_column', array(__CLASS__, 'feedat'),15,3 );
		}

		add_action('wpematico_campaign_feed_header_column', array(__CLASS__, 'headerfeedname'),5 );
		add_action('wpematico_campaign_feed_body_column', array(__CLASS__, 'bodyfeedname'),5,3 );

		
		add_action( 'admin_print_scripts-post-new.php', array(__CLASS__,'scripts'), 11 );
		add_action( 'admin_print_scripts-post.php', array(__CLASS__,'scripts'), 11 );
		
		add_action('admin_print_styles-post-new.php', array(__CLASS__,'styles'));
		add_action('admin_print_styles-post.php', array(__CLASS__,'styles'));


		add_action('wpematico_image_box_out_setting', array(__CLASS__ ,'pro_images_box' ), 10);
		add_action('wpematico_audio_box_setting_after', array(__CLASS__ ,'pro_audios_box' ), 10);
		add_action('wpematico_video_box_setting_after', array(__CLASS__ ,'pro_videos_box' ), 10);

		add_action('wpematico_feeddate_tools', array(__CLASS__ ,'pro_date_from_tag' ), 10, 2);

		add_action('wp_ajax_wpepro_upload_default_image', array(__CLASS__, 'upload_default_image'));
		add_action('wp_ajax_wpepro_statuses', array( __CLASS__, 'AllStatuses'));

		add_action('wpematico_campaign_statuses', array( __CLASS__, 'getAllStatuses'));

		add_filter('wpematico_template_tags_campaign_edit', array(__CLASS__, 'extra_template_tags'), 15, 1);
		
	}
	public static function extra_template_tags($tags_array) {
		$tags_array[] = '{feed_name}';
		return $tags_array;
	}
	
	/**
	* static functions headerfeedname and bodyfeedname
	* Title and column of each feed URL 
	* @access public
	* @return void
	* @since 2.0
	 */
	static function headerfeedname( ) { 
		echo '<div style="display: inline-block;" class="name_column">' . __('Name', 'wpematico' ) . '</div>';  
	}

	static function bodyfeedname( $feed, $cfgbasic, $i ) { // part of basic feed every line metabox
		global $post, $campaign_data;

		@$feed_name = $campaign_data['feed']['feed_name'][$i];
		if($feed_name=="") {  // if no name, takes and shows the domain of the feed url
			$feed_name = str_replace('www.', '', parse_url($feed, PHP_URL_HOST));
			//$feed_name = parse_url($feed, PHP_URL_HOST);
		}
		echo '<input type="text" readonly class="feed_name_read_only" value="' . $feed_name . '">';
	}

	static function pro_date_from_tag( $campaign_data, $cfg ) { 
		global $helptip;
		$campaign_date_tag = $campaign_data['campaign_date_tag'];
		$campaign_date_tag_name = $campaign_data['campaign_date_tag_name'];
		?>
		<input class="checkbox" type="checkbox"<?php checked($campaign_date_tag, true); ?> name="campaign_date_tag" value="1" id="campaign_date_tag"/> 
		<label for="campaign_date_tag"><?php _e('Get Date from xml tag.', 'wpematico'); ?></label> <span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_date_tag']; ?>"></span>
		<input type="text" id="campaign_date_tag_name" name="campaign_date_tag_name" value="<?php echo $campaign_date_tag_name; ?>">
		<?php
	}

	
	/**
	* static functions headerfeedat and feedat
	* Title and column of custom author per feed
	* @access public
	* @return void
	 */
	static function headerfeedat( ) { 
		echo '<div style="display: inline-block;" class="author_column">' . __('Author', 'wpematico' ) . '</div>';  
	}

	static function feedat( $feed, $cfgbasic ) { // part of basic feed every line metabox
		global $post, $campaign_data;
		@$feed_author = ($feed=="") ? "-1" : $campaign_data[$feed]['feed_author'];

		$feedauthorargs = array(
						'show_option_none' => __('Use campaign Author', 'wpematico' ),
						'show_option_all' => __('Use feed Author', 'wpematico' ),
						'name' => 'feed_author[]',
						'selected' => $feed_author 
					 );
		wp_dropdown_users($feedauthorargs); 
	}

	/**
	 * getAllStatuses() 
	 * @global type $wp_post_statuses
	 * @param type $statuses
	 * @return type array of All statuses ordered by domain with subtitles
	 */
	static function getAllStatuses($statuses = array()){
		global $wp_post_statuses;
		if ( !(defined('DOING_AJAX') && DOING_AJAX) && !( isset($_GET['action']) && $_GET['action']=='edit' ) ) {
			return $statuses;
		}
		
		$statuses = $wp_post_statuses;
		array_walk($statuses, function(&$object, $key) {
			//echo "$key. ".print_r($object,1)."<br />\n";
			$object->domain = $object->label_count['domain'];
		});
		//WPeMatico_functions::array_sort($statuses, 'domain');
		usort($statuses, function ($a, $b) { return strcmp($a->domain, $b->domain); });
		//print_r($statuses);

//		$StatusForPosts = ($campaign_customposttype=='post' or $campaign_customposttype=='page')?1:0;
		$args = apply_filters('wpematico_statuses_args', array(
//					'label'                     => false,
//					'label_count'               => false,
//					'exclude_from_search'       => null,
//					'_builtin'                  => $StatusForPosts,
//					'public'                    => null,
//					'internal'                  => null,
//					'protected'                 => null,
//					'private'                   => null,
//					'publicly_queryable'        => null,
			'show_in_admin_status_list' => 1,
			'show_in_admin_all_list'    => 1,
		));
		
		return wp_filter_object_list( $statuses, $args );
		
	}
	
	/**
	* Static function AllStatuses
	* @access public
	* @return void
	* @since 1.8.3
	*/
	public static function AllStatuses() {
		$nonce = '';
		if (isset($_POST['nonce'])) {
			$nonce = sanitize_text_field($_POST['nonce']);
		}
		//just one once is enough. Using 
		if (!wp_verify_nonce( $nonce, 'pro_campaign_edit_nonce' ) ) {
		    wp_send_json_error();
		}

		$statuses = self::getAllStatuses();
		
		$status_domain ='';
		$echo=""; //<select name='campaign_posttype' style='width: 100%;'>";
		$options = array();
		foreach ($statuses  as $key=>$status ) {
			if($status_domain != $status->label_count['domain']){
				$status_domain = $status->label_count['domain'];
				$echo.="<option disabled='disabled' value='' /> $status_domain</option>";
				$options[]=array('id' => 'dis',
								 'name' => $status_domain
					);
			}
			$status_name = $status->name;
			$status_label = $status->label;
			if (in_array($status_name, array('future','')) ) continue;
			$echo.="<option ".selected($status_name, $campaign_posttype, false)." value='$status_name' /> $status_label</option>";
			//$options[$status_name] = $status_label;
			$options[]=array('id' => $status_name,
							 'name' => $status_label
				);

		}
	//	$echo.= '</select>';
		
		
		if (!empty($_POST['posttype'])) {
			$response = array(
				'stati_options' => $echo,
			);			
			die(json_encode($options) );
			
			wp_send_json_success($response);
		}
	}

	/**
	* Static function upload_default_image
	* @access public
	* @return void
	* @since 1.8.3
	*/
	public static function upload_default_image() {
		$nonce = '';
		if (isset($_POST['nonce'])) {
			$nonce = sanitize_text_field($_POST['nonce']);
		}
		if (!wp_verify_nonce( $nonce, 'pro_campaign_edit_nonce' ) ) {
		    wp_send_json_error();
		}

		if (!empty($_POST['img_url'])) {
			$newimgname = sanitize_file_name(urlencode(basename($_POST['img_url'])));  // new name here
			// Primero intento con mi funcion mas rapida
			$upload_dir = wp_upload_dir();
			$imagen_dst = trailingslashit($upload_dir['path']). $newimgname; 
			$imagen_dst_url = trailingslashit($upload_dir['url']). $newimgname;
			$new_image = WPeMatico::save_file_from_url($_POST['img_url'], $imagen_dst);
			$new_image_url = trailingslashit($upload_dir['url']).basename($new_image);
			
			$response = array(
				'old_img' => $_POST['img_url'], 
				'new_image' => $new_image_url,
				'new_id' => wpepro_insert_file_asattach($new_image, 0),
			);
			wp_send_json_success($response);
		}
	}
	
	/**
	* Static function styles
	* @access public
	* @return void
	* @since 1.7.5
	*/
	public static function styles() {
		global $post_type;
		if($post_type == 'wpematico') {
			wp_enqueue_style('wpepro-campaigns-edit-css', WPeMaticoPRO::$uri .'assets/css/campaign_edit.css', array(), WPEMATICOPRO_VERSION);
		}
	}
	/**
	* Static function scripts
	* @access public
	* @return void
	* @since 1.7.5
	*/
	public static function scripts() {
		global $post_type;
		if($post_type == 'wpematico') {
			wp_enqueue_script( 'wpepro-campaign-edit', WPeMaticoPRO::$uri.'assets/js/campaign_edit.js', array( 'jquery', 'wp-util' ), WPEMATICOPRO_VERSION, true );
			$args = array();
			$wpepro_post_type = get_post_types($args);
			$taxonomy_post_type = array();
			foreach ($wpepro_post_type as $postType) {
				$taxonomy_post_type[$postType] = get_object_taxonomies($postType);
			}

			wp_localize_script('wpepro-campaign-edit', 'wpepro_object', 
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'pro_campaign_edit_nonce' => wp_create_nonce('pro_campaign_edit_nonce'),
					'settings' => self::$current_options,
					'taxonomy_post_type' => $taxonomy_post_type,
					'text_select_term'	=> __('Select a term', 'wpematico'),
					'text_change_image'	=> __('Change Image', 'wpematico'),
					'post_types_terms'	=>  array(),
					'get_terms_nonce'	=> wp_create_nonce('wpepro-word-tax-terms-nonce')

				));
		}
	}
	/**
	* Print the advanced feed icon and his popup HTML
	* @access public
	* @param  string   $feed			Main feed URL 
	* @param  array    $cfgbasic		Main core configuration options
	* @param  int	   $key				Feed key order in campaign 
	* @return void
	* @since 1.7.5
	*/
	public static function advanced_feed_icon($feed, $cfgbasic, $key) { // part of basic feed every line metabox
		global $post, $campaign_data;
			?>
			<button type="button" title="<?php _e('Open Feed advanced Options', 'wpematico' ); ?>" id="feedoptions_<?php echo $key; ?>" class="feedoptionsicon dashicons dashicons-admin-settings"></button>
			<div id="modalopt_<?php echo $key; ?>" class="modal">
				<!-- Modal content -->
				<div class="modal-content">
				  <div class="modal-header">
					<span class="modal-close">&times;</span>
					<h3 style="background-color: transparent;"><?php _e('Feed Advanced Options', 'wpematico' ); ?>: <code><?php echo $feed; ?></code></h3>
				  </div>
				  <div class="modal-body">
					  <?php 
					  /**
					   * @param string $feed			Main feed URL 
					   * @param array  $campaign_data	All the campaign data
					   * @param array  $cfgbasic		Main core configuration options
					   * @param int	   $key				Feed key order in campaign 
					   */
						do_action('wpematico_campaign_feed_advanced_options', $feed, $campaign_data, $cfgbasic, $key); 
					  ?>
					<p></p>
				  </div>
				  <div class="modal-footer">
					  <span><b><?php _e('Close popup and save campaign to save the changes.', 'wpematico' ); ?></b></span>
				  </div>
				</div>
		  </div>
		<?php
	}
	/**
	* If it's activated the multi page on a feed this will be print a icon.
	* @access public
	* @param  string   $feed			Main feed URL 
	* @param  array    $cfgbasic		Main core configuration options
	* @param  int	   $key				Feed key order in campaign 
	* @return void
	* @since 1.7.5
	*/
	public static function is_multipage_icon( $feed, $cfgbasic, $key ) { // part of basic feed every line metabox
		global $post, $campaign_data;

		if( isset($campaign_data['feed']['is_multipagefeed'][$key]) && $campaign_data['feed']['is_multipagefeed'][$key] ) :
		?>
		<div style="display: inline-block;" class="multifeed_column">
			<span title="<?php _e('Is Multipage', 'wpematico' ); ?>" id="is_multifeed<?php echo $key; ?>" class="is_multifeedicon bicon two_thrid"></span>
		</div>
		<?php
		endif;
	}
	public static function multifeedfields( $feed, $campaign_data, $cfgbasic, $key ) { 
		global $post;
		$is_multipagefeed = (!isset($campaign_data['feed']['is_multipagefeed'][$key])?false:$campaign_data['feed']['is_multipagefeed'][$key]);
		$multifeed_maxpages = (!isset($campaign_data['feed']['multifeed_maxpages'][$key])?1:$campaign_data['feed']['multifeed_maxpages'][$key]);
		?>
			<div id="ismultifeed" class="">
			<p>
				<label>

					<input type="hidden" class="is_multipagefeed" name="feed[is_multipagefeed][]" id="is_multipagefeed_<?php echo $key; ?>" value="<?php echo ($is_multipagefeed ? '1' : ''); ?>" />
					<input data-unchecked-forced="is_multipagefeed_<?php echo $key; ?>" class="is_multipagefeed_checkbox checkbox" type="checkbox"<?php checked($is_multipagefeed ,true);?> name="is_multipagefeed_checkbox[]" value="1" id="is_multipagefeed_checkbox_<?php echo $key; ?>"/>
					


					<strong><?php _e('Check to use as a multipage feed.', 'wpematico' ); ?></strong></label><br/>
				<span class="description"><?php _e('This option allow to check multiple pages for feeds like https://etruel.com/feed/?paged=2.', 'wpematico' ); ?></span>
			</p>				
			<p id="ismfmaxp" style="margin-left:20px;display:block<?php //echo ($is_multipagefeed)?"block":"none"; // TODO: Add js ?>;">
				<label><input name="feed[multifeed_maxpages][]" type="number" min="0" size="3" value="<?php echo $multifeed_maxpages;?>" class="multifeed_maxpages small-text" id="multifeed_maxpages_<?php echo $key; ?>"/> 
				<?php echo __('Max pages to fetch.', 'wpematico' ); ?></label> <br/>
				<span class="description"><?php _e('You should change the field "Max items to create on each fetch" to a value = Max Pages * 10.', 'wpematico' ); ?></span><br/>
				<label class="description" onclick="jQuery('#campaign_max').val(jQuery('#multifeed_maxpages_<?php echo $key; ?>').val()*10);"><?php _e('Click here to fix it automatically.', 'wpematico' ); ?></label>
			</p>
		</div>				
		<?php
	}

	public static function avanced_force_feed( $feed, $campaign_data, $cfgbasic, $key ) { 
		global $post;
		$force_feed = (!isset($campaign_data['feed']['force_feed'][$key])? false : $campaign_data['feed']['force_feed'][$key]);
		?>
		<div id="forcefeed" class="">
			<p>
				<label>
					
					<input type="hidden" class="force_feed" name="feed[force_feed][]" id="force_feed_<?php echo $key; ?>" value="<?php echo ($force_feed ? '1' : ''); ?>" />
					<input data-unchecked-forced="force_feed_<?php echo $key; ?>" class="force_feed_checkbox checkbox" type="checkbox"<?php checked($force_feed ,true);?> name="force_feed_checkbox[]" value="1" id="force_feed_checkbox_<?php echo $key; ?>"/>
				

				<strong><?php _e('Check to force feed.', 'wpematico' ); ?></strong></label><br/>
				<span class="description"><?php _e('This option allow force to use a feed with invalid mime or content.', 'wpematico' ); ?></span>
			</p>				
			
		</div>				
		<?php
	}

	public static function avanced_site_name( $feed, $campaign_data, $cfgbasic, $key ) { 
		global $post;
		$feed_name = (empty($campaign_data['feed']['feed_name'][$key])) ? '' : $campaign_data['feed']['feed_name'][$key];
		?>
		<div id="site_name_div" class="">
			<p>
				<label><strong><?php _e('Feed Name:', 'wpematico' ); ?></strong><input class="feed_name" type="text" name="feed[feed_name][]" value="<?php echo $feed_name; ?>" id="feed_name_<?php echo $key; ?>"/>
				</label><br/>
				<span class="description"><?php _e('You can assign a name for this feed, also can be used later in the Post template as {feed_name}.', 'wpematico' ); ?></span>
			</p>				
			
		</div>				
		<?php
	}

	public static function avanced_enable_cookie( $feed, $campaign_data, $cfgbasic, $key ) { 
		global $post;
		$enable_cookies = (!isset($campaign_data['feed']['enable_cookies'][$key])? false : $campaign_data['feed']['enable_cookies'][$key]);
		?>
		<div id="enablecookies" class="">
			<p>
				<label>


					<input type="hidden" class="enable_cookies" name="feed[enable_cookies][]" id="enable_cookies_<?php echo $key; ?>" value="<?php echo ($enable_cookies ? '1' : ''); ?>" />
					<input data-unchecked-forced="enable_cookies_<?php echo $key; ?>" class="enable_cookies_checkbox checkbox" type="checkbox"<?php checked($enable_cookies ,true);?> name="enable_cookies_checkbox[]" value="1" id="enable_cookies_checkbox_<?php echo $key; ?>"/>
				

				<strong><?php _e('Check to use HTTP Cookies.', 'wpematico' ); ?></strong></label><br/>
				<span class="description"><?php _e('This option enables the use of HTTP Cookies', 'wpematico' ); ?></span>
			</p>				
			
		</div>				
		<?php
	}
	/**
	* Static function get_user_agents
	* @access public
	* @return $user_agents Array of user agents
	* @since 1.8.2
	*/
	public static function get_user_agents() {
		$user_agents = array();
		$user_agents['CoreUserAgent'] = 'Core User Agent';
		$user_agents['Mozilla/5.0-Window'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';
		$user_agents['Mozilla/5.0-Linux'] = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:56.0) Gecko/20100101 Firefox/56.0';
		$user_agents = apply_filters('wpematico_pro_user_agents_array', $user_agents);
		return $user_agents;
	}
	/**
	* Static function avanced_user_agent
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function avanced_user_agent( $feed, $campaign_data, $cfgbasic, $key ) { 
		global $post;
		$user_agent = (!isset($campaign_data['feed']['user_agent'][$key])? 'CoreUserAgent' : $campaign_data['feed']['user_agent'][$key]);
		$user_agents_array = self::get_user_agents();
		?>
		<div id="user_agent_div" class="">
			<p>
				<label><strong><?php _e('Select User Agent:', 'wpematico' ); ?></strong> <select class="user_agent" name="feed[user_agent][]" id="user_agent_<?php echo $key; ?>">
					<?php
					foreach ($user_agents_array as $key => $value) : ?>
						<option value="<?php echo $key; ?>" <?php selected($user_agent, $key, true); ?>><?php echo $key; ?></option>
					<?php
					endforeach;
					?>
					</select>
				</label><br/>
				<span class="description"><?php _e('This option allow select a different User Agent when get remote websites.', 'wpematico' ); ?></span>
			</p>				
			
		</div>				
		<?php
	}

	/**
	* Static function get_input_encodings
	* @access public
	* @return $input_encodings Array of input encodings
	* @since 1.9.1
	*/
	public static function get_input_encodings() {
		$input_encodings =  array( 
            'auto-detect' 	=> 'Auto Detect', 
            'UTF-8'			=> 'UTF-8', 
            'ASCII'			=> 'ASCII', 
            'ISO-8859-1'	=> 'ISO-8859-1',
            'ISO-8859-2'	=> 'ISO-8859-2', 
            'ISO-8859-3'	=> 'ISO-8859-3', 
            'ISO-8859-4'	=> 'ISO-8859-4',
            'ISO-8859-5'	=> 'ISO-8859-5', 
            'ISO-8859-6'	=> 'ISO-8859-6',
            'ISO-8859-7'	=> 'ISO-8859-7',
            'ISO-8859-8'	=> 'ISO-8859-8',
            'ISO-8859-9'	=> 'ISO-8859-9',
            'ISO-8859-10'	=> 'ISO-8859-10', 
            'ISO-8859-13'	=> 'ISO-8859-13',
            'ISO-8859-14'	=> 'ISO-8859-14',
            'ISO-8859-15'	=> 'ISO-8859-15',
            'ISO-8859-16'	=> 'ISO-8859-16', 
            'Windows-1251'	=> 'Windows-1251',
            'Windows-1252'	=> 'Windows-1252',
            'Windows-1254'	=> 'Windows-1254', 
        );
		return $input_encodings;
	}
	/**
	* Static function avanced_user_agent
	* @access public
	* @return void
	* @since 1.9.1
	*/
	public static function avanced_input_encodings( $feed, $campaign_data, $cfgbasic, $key ) { 
		global $post;
		$input_encoding = (!isset($campaign_data['feed']['campaign_input_encoding'][$key])? 'CoreUserAgent' : $campaign_data['feed']['campaign_input_encoding'][$key]);
		$input_encodings_array = self::get_input_encodings();
		?>
		<div id="campaign_input_encoding_div" class="">
			<p>
				<label>
					<strong><?php _e('Feed Chrset.', 'wpematico' ); ?></strong> <select class="campaign_input_encoding" name="feed[campaign_input_encoding][]" id="campaign_input_encoding_<?php echo $key; ?>">
					<?php
					foreach ($input_encodings_array as $key => $value) : ?>
						<option value="<?php echo $key; ?>" <?php selected($input_encoding, $key, true); ?>><?php echo $value; ?></option>
					<?php
					endforeach;
					?>
					</select>
				</label><br/>
				<span class="description"><?php _e('This option allows select and force an Input Chrset Encoding to set for this feed.', 'wpematico' ); ?></span>
			</p>				
			
		</div>				
		<?php
	}
	/**
	* Static function word_to_taxonomy_box
	* @access public
	* @return void
	* @since 1.9.3
	*/
	public static function word_to_taxonomy_box() { 
		global $post, $campaign_data, $helptip;
		$campaign_word2tax 				= (empty($campaign_data['campaign_word2tax']) ? array() : $campaign_data['campaign_word2tax']);
		$campaign_no_setting_word2tax 	= (empty($campaign_data['campaign_no_setting_word2tax']) ? false : $campaign_data['campaign_no_setting_word2tax']);
		if (empty($campaign_word2tax)) {
			$campaign_word2tax = array('word' => array(''), 'title' => array(false), 'regex' => array(false), 'cases' => array(false), 'tax' => array('-1'), 'term' => array('-1') );
		}
		
		?>
		<input name="campaign_no_setting_word2tax" id="campaign_no_setting_word2tax" class="checkbox" value="1" type="checkbox" <?php checked($campaign_no_setting_word2tax, true); ?> />
		<label for="campaign_no_setting_word2tax"><?php echo __(' Ignore Words to Tax in Settings', WPeMaticoPRO::TEXTDOMAIN ); ?></label> <span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['audio_options']; ?>"></span>
		
		
		<div id="container_word_to_taxonomy">
		
		</div>
		<div id="paging-box-word2tax">
			<a href="#" class="button-primary add" id="addmorerew-word2tax"><?php _e('Add more.', WPeMaticoPRO::TEXTDOMAIN ); ?></a>
		</div>

		<script type="text/html" id="tmpl-word-to-taxonomy-entity">

		   <div id="w2t_ID_{{data.ID}}" class="row_word_to_tax">
				<div class="pDiv jobtype-select p7">
					<div id="w1">
						<label><?php _e('Word:', 'wpematico'); ?> <input type="text" size="25" class="regular-text" id="campaign_word2tax_word_{{data.ID}}" name="campaign_word2tax[word][{{data.ID}}]" value="{{data.word_value}}"></label><br>
						<label><input name="campaign_word2tax[title][{{data.ID}}]" id="campaign_word2tax_title_{{data.ID}}" class="checkbox w2ctitle" value="1" type="checkbox" <# if ( data.ontitle ) { #> checked="checked" <# } #> ><?php _e('on Title', 'wpematico'); ?>&nbsp;&nbsp;</label>
						<label><input name="campaign_word2tax[regex][{{data.ID}}]" id="campaign_word2tax_regex_{{data.ID}}" class="checkbox w2cregex" value="1" type="checkbox" <# if ( data.onregex ) { #> checked="checked" <# } #> ><?php _e('RegEx', 'wpematico'); ?>&nbsp;&nbsp;</label>
						<label><input name="campaign_word2tax[cases][{{data.ID}}]" id="campaign_word2tax_cases_{{data.ID}}" class="checkbox w2ccases" value="1" type="checkbox" <# if ( data.oncases ) { #> checked="checked" <# } #> ><?php _e('Case sensitive', 'wpematico'); ?>&nbsp;&nbsp;</label>
					</div>
					<div id="c1">
						<select name="campaign_word2tax[tax][{{data.ID}}]" id="campaign_word2tax_tax_{{data.ID}}" class="form-no-clear word2tax_tax">
							<option value="-1"><?php _e('Select a taxonomy', 'wpematico'); ?></option>
							{{{data.options_select_tax}}}
						</select>
						<select style="width: 150px;" name="campaign_word2tax[term][{{data.ID}}]" id="campaign_word2tax_term_{{data.ID}}" class="form-no-clear">
							<option value="-1"><?php _e('Select a term', 'wpematico'); ?></option>
							{{{data.options_select_term}}}
						</select>
					</div>
					<span class="wi10" id="w2cactions">
						<label title="Delete this item" class="bicon delete left btn_delete_w2t"></label>
					</span>
				</div>
			</div>

		</script>
		
		<script type="text/javascript">
			
			jQuery(document).ready(function($) {
				
				wpepro_update_taxonomy_id(jQuery);

				<?php foreach ($campaign_word2tax['word'] as $key => $val) : ?>
					add_new_input_group(<?php var_export($campaign_word2tax['word'][$key]); ?>, <?php var_export($campaign_word2tax['title'][$key]); ?>, <?php var_export($campaign_word2tax['regex'][$key]) ?>, <?php var_export($campaign_word2tax['cases'][$key]) ?>, <?php var_export( $campaign_word2tax['tax'][$key] ); ?>, <?php echo $campaign_word2tax['term'][$key]; ?>);

				<?php endforeach; ?>


				jQuery('#addmorerew-word2tax').click(function(e) {
					add_new_input_group();
					word2tax_events_rows();
					e.preventDefault();
				});
				jQuery('input[name="campaign_customposttype"]').change(function(e) {
					jQuery('#container_word_to_taxonomy').html('');
					add_new_input_group();
					word2tax_events_rows();
					e.preventDefault();
				});
				word2tax_events_rows();


			});

		</script>
	<?php
	}
	/**
	* Static function filter_per_author_box
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function filter_per_author_box() {
		global $post, $campaign_data, $helptip;
		
		$campaign_fauthor_inc_words = @$campaign_data['campaign_fauthor_inc_words'];
		$campaign_fauthor_inc_regex = @$campaign_data['campaign_fauthor_inc_regex'];
		$campaign_fauthor_exc_words = @$campaign_data['campaign_fauthor_exc_words'];
		$campaign_fauthor_exc_regex = @$campaign_data['campaign_fauthor_exc_regex'];
		$cfg = self::$current_options;
		?>
		
		<span class="left"><?php _e('Skip posts with words in author\'s name.',  'wpematico' ); ?></span><span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['skip_posts_with_words_author']; ?>"></span>
			
			
		<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
			<b><?php _e('Must contain', 'wpematico' ); ?>:</b><br />
			
			<label for="campaign_fauthor_inc_words"><?php _e('Words:', 'wpematico' ); ?></label><br />
			<textarea style="width: 50%; height: 70px;" class="regular-text" id="campaign_fauthor_inc_words" name="campaign_fauthor_inc_words"><?php echo stripslashes($campaign_fauthor_inc_words); ?></textarea><br />
			<label for="campaign_fauthor_inc_regex"><?php _e('RegEx:', 'wpematico' ); ?></label>		
			<input class="regular-text" type="text" id="campaign_fauthor_inc_regex" name="campaign_fauthor_inc_regex" value="<?php echo stripslashes($campaign_fauthor_inc_regex); ?>" />
		</div>
		<div class="" style="background: #eef1ff none repeat scroll 0% 0%;border: 2px solid #cee1ef;padding: 0.5em;">
			<b><?php _e('Cannot contain:', 'wpematico' ); ?></b><br />
			
			<label for="campaign_fauthor_exc_words"><?php _e('Words:', 'wpematico' ); ?></label><br />
			<textarea style="width: 50%; height: 70px;" class="regular-text" id="campaign_fauthor_exc_words" name="campaign_fauthor_exc_words"><?php echo stripslashes($campaign_fauthor_exc_words); ?></textarea><br />
			<label for="campaign_fauthor_exc_regex"><?php _e('RegEx:', 'wpematico' ); ?></label>		
			<input type="text" class="regular-text" id="campaign_fauthor_exc_regex" name="campaign_fauthor_exc_regex" value="<?php echo stripslashes($campaign_fauthor_exc_regex); ?>" />				    
		</div>
			
		<div class="clear"></div>
		<?php
	}
	/**
	* Static function pro_images_box
	* Print the images box on campaign.
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function pro_images_box() {
		global $post, $campaign_data, $helptip;
		$default_img = @$campaign_data['default_img'];
		$default_img_url = @$campaign_data['default_img_url'];
		$default_img_link = @$campaign_data['default_img_link'];
		$default_img_title = @$campaign_data['default_img_title'];
		$default_img_id = @$campaign_data['default_img_id'];
		
		$campaign_rssimg = @$campaign_data['campaign_rssimg'];
		$strip_all_images = @$campaign_data['strip_all_images'];
		$overwrite_image = @$campaign_data['overwrite_image'];
		$clean_images_urls = @$campaign_data['clean_images_urls'];
		$image_src_gettype = @$campaign_data['image_src_gettype'];

		$check_image_content = @$campaign_data['check_image_content'];
		$strip_image_without_content = @$campaign_data['strip_image_without_content'];
		
		$discardifnoimage = @$campaign_data['discardifnoimage'];
		$rssimg_enclosure = @$campaign_data['rssimg_enclosure'];
		$rssimg_ifno = @$campaign_data['rssimg_ifno'];
		$rssimg_add2img = @$campaign_data['rssimg_add2img'];
		$add1stimg = @$campaign_data['add1stimg'];
		$rssimg_featured = @$campaign_data['rssimg_featured'];
		$which_featured = (!isset($campaign_data['which_featured'])) ? 'content1' : $campaign_data['which_featured'];
				
		$campaign_enableimgrename = @$campaign_data['campaign_enableimgrename'];
		$campaign_imgrename = @$campaign_data['campaign_imgrename'];

		$cfg = self::$current_options;
		$cfgbasic = self::$current_options_core;
		$cfgbasic['customupload'] = (!isset($cfgbasic['customupload'])? false : $cfgbasic['customupload'] == true ? true : false );
		?>
		<div id="contanier_image_ren" <?php echo ($campaign_data['campaign_customupload']) ? '' : ' style="display:none;"'; ?>>
			<p><b><?php _e('Determine what happens with duplicated image names',  'wpematico' ); ?></b></p>
				<div id="whatimgren" style="margin-left: 20px;">
				<label><input type="radio" name="overwrite_image" <?php echo checked('rename',$overwrite_image,false); ?> value="rename" /> <?php _e('Rename like Wordpress standards (name-1)'); ?></label><br />
				<label><input type="radio" name="overwrite_image" <?php echo checked('overwrite',$overwrite_image,false); ?> value="overwrite" /> <?php _e('Always Overwrite'); ?></label><br />
				<label><input type="radio" name="overwrite_image" <?php echo checked('keep',$overwrite_image,false); ?> value="keep" /> <?php _e('Always keep the first. Recommended.'); ?></label><br />
				</div>
			<p></p>
		</div>
		<br/>
		<input class="checkbox" type="checkbox"<?php checked($clean_images_urls,true);?> name="clean_images_urls" value="1" id="clean_images_urls"/> <b><?php echo '<label for="clean_images_urls">' . __('Strip the queries variables in images URls.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['clean_images_urls']; ?>"></span><br/>
		<input class="checkbox" type="checkbox"<?php checked($image_src_gettype,true);?> name="image_src_gettype" value="1" id="image_src_gettype"/> <b><?php echo '<label for="image_src_gettype">' . __('Check the source image to determine the extension.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['image_src_gettype']; ?>"></span><br/>
		
		<input class="checkbox" type="checkbox"<?php checked($check_image_content,true);?> name="check_image_content" value="1" id="check_image_content"/> <b><?php echo '<label for="check_image_content">' . __('Check if image has correct content.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['check_image_content']; ?>"></span>
		<div id="check_image_content_div" style="margin-left: 20px;<?php if (!$check_image_content) echo 'display:none;';?>">
			<input class="checkbox" type="checkbox"<?php checked($strip_image_without_content,true);?> name="strip_image_without_content" value="1" id="strip_image_without_content"/> <b><?php echo '<label for="strip_image_without_content">' . __('Strip from content if it&#x27;s not an image.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['strip_image_without_content']; ?>"></span>
		</div>
		
		<p></p>
		<div id="imagerenamer"  class="inmetabox">
			<input class="checkbox" type="checkbox"<?php checked($campaign_enableimgrename,true);?> name="campaign_enableimgrename" value="1" id="campaign_enableimgrename"/> 
			<label for="campaign_enableimgrename"><b><?php _e('Enable Image Renamer', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_enableimgrename']; ?>"></span>
			<div id="noimgren" style="margin-left: 20px;<?php if (!$campaign_enableimgrename) echo 'display:none;';?>">
				<b><label for="campaign_imgrename"><?php _e('Rename the images to', 'wpematico' ); ?>:</label></b>
				<input name="campaign_imgrename" type="text" size="3" value="<?php echo $campaign_imgrename;?>" class="regular-text" id="campaign_imgrename"/><br />
				<p class="description"><?php _e("Don't complete the extension of the file. This field is used to change the name and remains the same extension.",  'wpematico' ); ?><br />
				<?php printf( __("You can use %1s or %1s and will be replaced on uploading the image. Wordpress adds a number at the end if the image name already exists.",  'wpematico' ),
							'<a href="JavaScript:void(0);" onclick="jQuery(\'#campaign_imgrename\').val( jQuery(\'#campaign_imgrename\').val()+jQuery(this).text() );">{title}</a>',
							'<a href="JavaScript:void(0);" onclick="jQuery(\'#campaign_imgrename\').val( jQuery(\'#campaign_imgrename\').val()+jQuery(this).text() );">{slug}</a>'
					  ); 
				?>
				</p>
			</div>
		</div>			
		<h3 class="subsection"><?php _e('From feed items','wpematico'); ?></h3>
		
		<p><input class="checkbox" type="checkbox"<?php checked($campaign_rssimg,true);?> name="campaign_rssimg" value="1" id="campaign_rssimg"/> <b><?php echo '<label for="campaign_rssimg">' . __('Get also Images from RSS', 'wpematico' ) . '</label>'; ?></b></p>
		<div class="rssimg_opt" style="padding-left:20px; <?php if (!$campaign_rssimg) echo 'display:none;';?>">
			<input class="checkbox" type="checkbox"<?php checked($rssimg_enclosure,true);?> name="rssimg_enclosure" value="1" id="rssimg_enclosure"/> <b><label for="rssimg_enclosure"> <?php _e('Also enclosure and media RSS tags.', 'wpematico' ); ?></b></label>
			<br />
			<input class="checkbox" type="checkbox"<?php checked($rssimg_ifno,true);?> name="rssimg_ifno" value="1" id="rssimg_ifno"/> <b><label for="rssimg_ifno"> <?php _e('Only if no images on content.', 'wpematico' ); ?></b></label>
			<br />
			<input class="checkbox" type="checkbox"<?php checked($rssimg_add2img,true);?> name="rssimg_add2img" value="1" id="rssimg_add2img"/> <label for="rssimg_add2img"><b> <?php _e('Make featured RSS image.', 'wpematico' ); ?></b></label>
		</div>
		<p></p>

		<h3 class="subsection"><?php _e('From Content','wpematico'); ?></h3>
		<p><input class="checkbox" type="checkbox"<?php checked($strip_all_images,true);?> name="strip_all_images" value="1" id="strip_all_images"/> <b><?php echo '<label for="strip_all_images">' . __('Strip All Images from Content.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['strip_all_images']; ?>"></span>
		</p>
		
		<div id="noimages" <?php if ($strip_all_images) echo 'style="display:none;"';?>>
			<input class="checkbox" type="checkbox"<?php checked($discardifnoimage,true);?> name="discardifnoimage" value="1" id="discardifnoimage"/> <b><?php echo '<label for="discardifnoimage">' . __('Discard the Post if NO Images in Content.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['discardifnoimage']; ?>"></span>	<br/>	
			<input name="add1stimg" id="add1stimg" class="checkbox" value="1" type="checkbox" <?php checked($add1stimg,true); ?> /> <b><?php echo '<label for="add1stimg">' . __('Add featured image at the beginning of the post content.', 'wpematico' ) . '</label>'; ?></b>	
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['add1stimg']; ?>"></span>
		<?php	
		// **************************** Images filters by dimensions *******************************
		if ($cfg['enableimgfilter']) :  // Si está habilitado en settings, lo muestra 
			@$imagefilters = $campaign_data['imagefilters'];
			if(!($imagefilters)) $imagefilters = array('allow'=>array(''),'woh'=>array(''),'mol'=>array(''),'value'=>array(''));
			?><p></p>
			<div class="lavender inmetabox">
				<p class="he20">
				<b><span class="left"><?php _e('Add filters by dimensions of images.', 'wpematico' ) ?></span></b>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['filters_by_dimensions_of_images']; ?>"></span>
				<div id="imgfilt_edit" class="inlinetext">		
					<?php for ($i = 0; $i <= count(@$imagefilters['value']); $i++) : ?>
					<div class="<?php if(($i % 2) == 0) echo 'bw'; else echo 'lightblue'; ?> <?php if($i==count($imagefilters['value'])) echo 'hide'; ?>">
						<div class="pDiv jobtype-select p7" id="nuevoimgfilt">
							<div class="left p4">
								<b><?php echo '<label for="campaign_if_allow_'. $i .'">' . __('Filter:', 'wpematico' ) . '</label>&nbsp;&nbsp;&nbsp;&nbsp; '; ?></b>
								<select id="campaign_if_allow_<?php echo $i; ?>" name="campaign_if_allow[<?php echo $i; ?>]">
									<option value="Allow" <?php echo ($imagefilters['allow'][$i]=="Allow" || $imagefilters['allow'][$i]=="") ? 'SELECTED' : ''; ?> > Allow</option>
									<option value="Skip" <?php echo ($imagefilters['allow'][$i]=="Skip") ? 'SELECTED' : ''; ?> > Skip</option>
								</select>						
							</div>
							<div class="left p4">
								<select id="campaign_if_woh_<?php echo $i; ?>" name="campaign_if_woh[<?php echo $i; ?>]">
									<option value="width" <?php echo ($imagefilters['woh'][$i]=="width" || $imagefilters['woh'][$i]=="") ? 'SELECTED' : ''; ?> > width</option>
									<option value="height" <?php echo ($imagefilters['woh'][$i]=="height") ? 'SELECTED' : ''; ?> > height</option>
								</select>						
							</div>
							<div class="left p4">
								<select id="campaign_if_mol_<?php echo $i; ?>" name="campaign_if_mol[<?php echo $i; ?>]">
									<option value="more" <?php echo ($imagefilters['mol'][$i]=="more" || $imagefilters['mol'][$i]=="") ? 'SELECTED' : ''; ?> > more</option>
									<option value="less" <?php echo ($imagefilters['mol'][$i]=="less") ? 'SELECTED' : ''; ?> > less</option>
								</select>						
							</div>
							<div id="cf1" class="left p4">
								 <?php _e('size:','wpematico') ?><input name="campaign_if_value[<?php echo $i; ?>]" type="text" value="<?php echo stripslashes(@$imagefilters['value'][$i]) ?>" class="normal-text" id="campaign_if_value" /> pixels
							</div>
							<div class="m7">
								<span class="" id="w2cactions">
									<label title="<?php _e('Delete this item', 'wpematico' ); ?>" onclick=" jQuery(this).parent().parent().parent().children('#cf1').children('#campaign_if_value').val(''); jQuery(this).parent().parent().parent().fadeOut();" class="right ui-icon redx_circle"></label>
								</span>
							</div>
						</div>
					</div>
					<?php $a=$i;endfor ?>
					<input id="imgfilt_max" value="<?php echo $a; ?>" type="hidden" name="imgfilt_max">

				</div>
				<div class="clear"></div>
				<div id="paging-box">		  
					<a href="JavaScript:void(0);" class="button-primary left m4" id="addmoreimgf" style="font-weight: bold; text-decoration: none;"><?php _e('Add more', 'wpematico' ); ?>.</a>
				</div>
			</div>
			<?php
			endif; //enableimgfilter
			?><br />
		</div>
	<?php
	// **************************** FEATURED Images Parsers  *******************************
		?><p></p>
		<h3 class="subsection leftText"><?php _e('Parsers for Featured image', 'wpematico' ); ?></h3>
		<p></p>
		<div class="inmetabox" style="background-color: #F9F2B5;">
			<p><input name="default_img" id="default_img" class="checkbox" value="1" type="checkbox" <?php checked($default_img,true); ?> /> <b><?php echo '<label for="default_img">' . __('Default Featured image if not found image on content.', 'wpematico' ) . '</label>'; ?></b></p>
			<table class="form-table-tf" id="tblupload" style="padding-left:20px; <?php if (!$default_img) echo 'display:none;';?>">
			<?php //for($id = 1; $id <= 1 ; $id++) :  ?>
			  <tr>
				<th scope="row" style="line-height: 26px;"> </th>
				<td><label for="default_img_url">
					<div id="default_img_url_div" <?php echo (!empty($default_img_url) ? '' : ' style="display:none;"');  ?>>
						<?php  _e('Image URL:', 'wpematico' ); ?>
						<input type="text" class="regular-text" readonly="true" name="default_img_url" id="default_img_url" value="<?php echo $default_img_url; ?>" />
						<a href="<?php echo $default_img_url; ?>" title="<?php  _e('Open Image URL in a new browser tab', 'wpematico' ); ?>" target="_Blank" class="default_img_url_openlink"><span class="dashicons dashicons-external"></span></a>
						<br>
					</div>
				  
				  <input id="upload_image_button" class="et_upload_button button" type="button" value="<?php (!empty($default_img_url) ? _e('Change Image', 'wpematico' ) : _e('Add Image', 'wpematico' ) );  ?>" />
				  <input type="hidden" class="regular-text" name="default_img_link" id="default_img_link" value="<?php echo $default_img_link; ?>" /> 
				  <input type="hidden" class="regular-text" name="default_img_title" id="default_img_title" value="<?php echo $default_img_title; ?>" />
				  <input type="hidden" class="regular-text" name="default_img_id" id="default_img_id" value="<?php echo $default_img_id; ?>" />
				  
				  </label>
				</td>
			  </tr>
			<?php //endfor; ?>
			</table>
			<br />
			<b><label><?php echo __('Only allow first Featured image that meets the following filters.', 'wpematico' ); ?></label></b>
		<?php
			@$featimgfilters = $campaign_data['featimgfilters'];
			if(!($featimgfilters)) $featimgfilters = array('allow'=>array(''),'woh'=>array(''),'mol'=>array(''),'value'=>array('')); 
		?>
			<div id="featimgfilt_edit" class="inlinetext">		
				<?php 
					for ($i = 0; $i <= count(@$featimgfilters['value']); $i++) : 
						if (empty($featimgfilters['allow'][$i])) {
							$featimgfilters['allow'][$i] = 'Allow';
						}
						if (empty($featimgfilters['woh'][$i])) {
							$featimgfilters['woh'][$i] = 'width';
						}
						if (empty($featimgfilters['mol'][$i])) {
							$featimgfilters['mol'][$i] = 'more';
						}

				?>
				<div class="<?php if(($i % 2) == 0) echo 'bw'; else echo 'lightblue'; ?> <?php if($i==count($featimgfilters['value'])) echo 'hide'; ?>">
					<div class="pDiv jobtype-select p7" id="nuevofeatimgfilt">
						<div class="left p4">
							<b><?php echo '<label for="campaign_feat_allow_'. $i .'">' . __('Filter:', 'wpematico' ) . '</label>&nbsp;&nbsp;&nbsp;&nbsp; '; ?></b>
							<select id="campaign_feat_allow_<?php echo $i; ?>" name="campaign_feat_allow[<?php echo $i; ?>]">
								<option value="Allow" <?php echo ($featimgfilters['allow'][$i]=="Allow" || $featimgfilters['allow'][$i]=="") ? 'SELECTED' : ''; ?> > Allow</option>
								<option value="Skip" <?php echo ($featimgfilters['allow'][$i]=="Skip") ? 'SELECTED' : ''; ?> > Skip</option>
							</select>						
						</div>
						<div class="left p4">
							<select id="campaign_feat_woh_<?php echo $i; ?>" name="campaign_feat_woh[<?php echo $i; ?>]">
								<option value="width" <?php echo ($featimgfilters['woh'][$i]=="width" || $featimgfilters['woh'][$i]=="") ? 'SELECTED' : ''; ?> > width</option>
								<option value="height" <?php echo ($featimgfilters['woh'][$i]=="height") ? 'SELECTED' : ''; ?> > height</option>
							</select>						
						</div>
						<div class="left p4">
							<select id="campaign_feat_mol_<?php echo $i; ?>" name="campaign_feat_mol[<?php echo $i; ?>]">
								<option value="more" <?php echo ($featimgfilters['mol'][$i]=="more" || $featimgfilters['mol'][$i]=="") ? 'SELECTED' : ''; ?> > more</option>
								<option value="less" <?php echo ($featimgfilters['mol'][$i]=="less") ? 'SELECTED' : ''; ?> > less</option>
							</select>						
						</div>
						<div id="cf1" class="left p4">
							 <?php _e('size:','wpematico') ?><input name="campaign_feat_value[<?php echo $i; ?>]" type="text" value="<?php echo stripslashes(@$featimgfilters['value'][$i]) ?>" class="normal-text" id="campaign_feat_value" /> pixels
						</div>
						<div class="m7">
							<span class="" id="w2cactions">
								<label title="<?php _e('Delete this item', 'wpematico' ); ?>" onclick=" jQuery(this).parent().parent().parent().children('#cf1').children('#campaign_feat_value').val(''); jQuery(this).parent().parent().parent().fadeOut();" class="right ui-icon redx_circle"></label>
							</span>
						</div>
					</div>
				</div>
				<?php $a=$i;endfor ?>
				<input id="featimgfilt_max" value="<?php echo $a; ?>" type="hidden" name="featimgfilt_max">
				
			</div>
			<div class="clear"></div>
			<div id="paging-box">		  
				<a href="JavaScript:void(0);" class="button-primary left m4" id="addmorefeatimgf" style="font-weight: bold; text-decoration: none;"><?php _e('Add more', 'wpematico' ); ?>.</a>
			 </div>
		</div>
		<?php
	}
	/**
	* Static function pro_audios_box
	* Print the audio box on campaign.
	* @access public
	* @return void
	* @since 1.6.4
	*/
	public static function pro_audios_box() {
		global $post, $campaign_data, $helptip;
		$campaign_audio_cache = $campaign_data['campaign_audio_cache'];
		$overwrite_audio = @$campaign_data['overwrite_audio'];
		$clean_audios_urls = @$campaign_data['clean_audios_urls'];
		$rss_audio = @$campaign_data['rss_audio'];
		$rss_audio_enclosure = @$campaign_data['rss_audio_enclosure'];
		$rss_audio_ifno = @$campaign_data['rss_audio_ifno'];
		
		$strip_all_audios = @$campaign_data['strip_all_audios'];

		$upload_ranges = @$campaign_data['audio_upload_ranges'];
		$upload_range_mb = @$campaign_data['audio_upload_range_mb'];
		
		$enable_audio_rename = @$campaign_data['enable_audio_rename'];
		$audio_rename = @$campaign_data['audio_rename'];

		$audio_decode_html_ent_url = @$campaign_data['audio_decode_html_ent_url'];
		$audio_follow_redirection = @$campaign_data['audio_follow_redirection'];

		$cfg = self::$current_options;
		$cfgbasic = self::$current_options_core;

		?>
		<div id="audio_upload_ranges_div"  style="margin-top:10px; margin-bottom:10px; <?php if (!$campaign_audio_cache) echo 'display:none;';?>">
		
			<input class="checkbox" type="checkbox"<?php checked($upload_ranges,true);?> name="audio_upload_ranges" value="1" id="audio_upload_ranges"/> 
			<label for="audio_upload_ranges"><b><?php _e('Upload by ranges', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['audio_upload_ranges']; ?>"></span>
			<div id="audio_upload_range_mb_div" style="margin-left: 20px;<?php if (!$upload_ranges) echo 'display:none;';?>">
				<b><label for="audio_rename"><?php _e('MBs per ranges', 'wpematico' ); ?>:</label></b>
				<input name="audio_upload_range_mb" type="number" min="1" value="<?php echo $upload_range_mb;?>" id="audio_upload_range_mb"/><br />
			</div>
			<br/>
		</div>

		
		
		<div id="contanier_audio_ren" <?php echo ($campaign_data['campaign_customupload_audio']) ? '' : ' style="display:none;"'; ?>>
			<p><b><?php _e('Determine what happens with duplicated audio names',  'wpematico' ); ?></b></p>
			<div id="what_audio_ren" style="margin-left: 20px;">
				<label><input type="radio" name="overwrite_audio" <?php echo checked('rename',$overwrite_audio,false); ?> value="rename" /> <?php _e('Rename like Wordpress standards (name-1)'); ?></label><br />
				<label><input type="radio" name="overwrite_audio" <?php echo checked('overwrite',$overwrite_audio,false); ?> value="overwrite" /> <?php _e('Always Overwrite'); ?></label><br />
				<label><input type="radio" name="overwrite_audio" <?php echo checked('keep',$overwrite_audio,false); ?> value="keep" /> <?php _e('Always keep the first. Recommended.'); ?></label><br />
			</div>
		</div>
		<p></p>


		<input class="checkbox" type="checkbox"<?php checked($clean_audios_urls,true);?> name="clean_audios_urls" value="1" id="clean_audios_urls"/> <b><?php echo '<label for="clean_audios_urls">' . __('Strip the queries variables in audios URLs.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['clean_audios_urls']; ?>"></span><br/>
		
		<div id="audio_decode_html_ent_url_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px;">
		
			<input class="checkbox" type="checkbox"<?php checked($audio_decode_html_ent_url,true);?> name="audio_decode_html_ent_url" value="1" id="audio_decode_html_ent_url"/> 
			<label for="audio_decode_html_ent_url"><b><?php _e('Decode html entities in URLs.', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['audio_decode_html_ent_url']; ?>"></span>

		</div>

		<div id="audio_follow_redirection_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px;">
		
			<input class="checkbox" type="checkbox"<?php checked($audio_follow_redirection,true);?> name="audio_follow_redirection" value="1" id="audio_follow_redirection"/> 
			<label for="audio_follow_redirection"><b><?php _e('Follow redirections to find the audio file.', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['audio_follow_redirection']; ?>"></span>

		</div>
			
		
			<p></p>
			<div id="audiorenamer"  class="inmetabox">
				<input class="checkbox" type="checkbox"<?php checked($enable_audio_rename,true);?> name="enable_audio_rename" value="1" id="enable_audio_rename"/> 
				<label for="enable_audio_rename"><b><?php _e('Enable Audio Renamer', 'wpematico' ); ?></b></label>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['enable_audio_rename']; ?>"></span>
				<div id="no_audio_ren" style="margin-left: 20px;<?php if (!$enable_audio_rename) echo 'display:none;';?>">
					<b><label for="audio_rename"><?php _e('Rename the images to', 'wpematico' ); ?>:</label></b>
					<input name="audio_rename" type="text" size="3" value="<?php echo $audio_rename;?>" class="regular-text" id="audio_rename"/><br />
					<p class="description"><?php _e("Don't complete the extension of the file. This field is used to change the name and remains the same extension.",  'wpematico' ); ?><br />
						<?php printf( __("You can use %1s or %1s and will be replaced on uploading the audio. Wordpress adds a number at the end if the audio name already exists.",  'wpematico' ),
									'<a href="JavaScript:void(0);" onclick="jQuery(\'#audio_rename\').val( jQuery(\'#audio_rename\').val()+jQuery(this).text() );">{title}</a>',
									'<a href="JavaScript:void(0);" onclick="jQuery(\'#audio_rename\').val( jQuery(\'#audio_rename\').val()+jQuery(this).text() );">{slug}</a>'
							  ); 
						?>
					</p>
				</div>
			</div>
		
				
		<h3 class="subsection"><?php _e('From feed items','wpematico'); ?></h3>
			
		<p><input class="checkbox" type="checkbox"<?php checked($rss_audio,true);?> name="rss_audio" value="1" id="rss_audio"/> <b><?php echo '<label for="rss_audio">' . __('Get also Audios from RSS', 'wpematico' ) . '</label>'; ?></b></p>
		<div class="rss_audio_opt" style="padding-left:20px; <?php if (!$rss_audio) echo 'display:none;';?>">
			<input class="checkbox" type="checkbox"<?php checked($rss_audio_enclosure,true);?> name="rss_audio_enclosure" value="1" id="rss_audio_enclosure"/> <b><label for="rss_audio_enclosure"> <?php _e('Also enclosure and media RSS tags.', 'wpematico' ); ?></b></label>
			<br />
			<input class="checkbox" type="checkbox"<?php checked($rss_audio_ifno,true);?> name="rss_audio_ifno" value="1" id="rss_audio_ifno"/> <b><label for="rssimg_ifno"> <?php _e('Only if no audios on content.', 'wpematico' ); ?></b></label>
			<br />
		</div>
		<p></p>

		<h3 class="subsection"><?php _e('From Content','wpematico'); ?></h3>
		<p><input class="checkbox" type="checkbox"<?php checked($strip_all_audios,true);?> name="strip_all_audios" value="1" id="strip_all_audios"/> <b><?php echo '<label for="strip_all_audios">' . __('Strip All Audios from Content.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['strip_all_audios']; ?>"></span>
		</p>
			
		<?php
	}


	/**
	* Static function pro_videos_box
	* Print the video box on campaign.
	* @access public
	* @return void
	* @since 1.6.4
	*/
	public static function pro_videos_box() {
		global $post, $campaign_data, $helptip;
		$campaign_video_cache = $campaign_data['campaign_video_cache'];
		$overwrite_video = @$campaign_data['overwrite_video'];
		$clean_videos_urls = @$campaign_data['clean_videos_urls'];
		$rss_video = @$campaign_data['rss_video'];
		$rss_video_enclosure = @$campaign_data['rss_video_enclosure'];
		$rss_video_ifno = @$campaign_data['rss_video_ifno'];
		
		$strip_all_videos = @$campaign_data['strip_all_videos'];

		$upload_ranges = @$campaign_data['video_upload_ranges'];
		$upload_range_mb = @$campaign_data['video_upload_range_mb'];	
		

		$enable_video_rename = @$campaign_data['enable_video_rename'];
		$video_rename = @$campaign_data['video_rename'];

		$video_decode_html_ent_url = @$campaign_data['video_decode_html_ent_url'];
		$video_follow_redirection = @$campaign_data['video_follow_redirection'];

		

		$cfg = self::$current_options;
		$cfgbasic = self::$current_options_core;

		?>
		<div id="video_upload_ranges_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px; <?php if (!$campaign_video_cache) echo 'display:none;';?>">
		
			<input class="checkbox" type="checkbox"<?php checked($upload_ranges,true);?> name="video_upload_ranges" value="1" id="video_upload_ranges"/> 
			<label for="video_upload_ranges"><b><?php _e('Upload by ranges', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['video_upload_ranges']; ?>"></span>
			<div id="video_upload_range_mb_div" style="margin-left: 20px;<?php if (!$upload_ranges) echo 'display:none;';?>">
				<b><label for="video_rename"><?php _e('MBs per ranges', 'wpematico' ); ?>:</label></b>
				<input name="video_upload_range_mb" type="number" min="1" value="<?php echo $upload_range_mb;?>" id="video_upload_range_mb"/><br />
			</div>
			<br/>
		</div>

		
		
		<div id="contanier_video_ren" <?php echo ($campaign_data['campaign_customupload_video']) ? '' : ' style="display:none;"'; ?>>
			<p><b><?php _e('Determine what happens with duplicated video names',  'wpematico' ); ?></b></p>
			<div id="what_video_ren" style="margin-left: 20px;">
				<label><input type="radio" name="overwrite_video" <?php echo checked('rename',$overwrite_video,false); ?> value="rename" /> <?php _e('Rename like Wordpress standards (name-1)'); ?></label><br />
				<label><input type="radio" name="overwrite_video" <?php echo checked('overwrite',$overwrite_video,false); ?> value="overwrite" /> <?php _e('Always Overwrite'); ?></label><br />
				<label><input type="radio" name="overwrite_video" <?php echo checked('keep',$overwrite_video,false); ?> value="keep" /> <?php _e('Always keep the first. Recommended.'); ?></label><br />
			</div>
		</div>
		<p></p>


		<input class="checkbox" type="checkbox"<?php checked($clean_videos_urls,true);?> name="clean_videos_urls" value="1" id="clean_videos_urls"/> <b><?php echo '<label for="clean_videos_urls">' . __('Strip the queries variables in videos URLs.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['clean_videos_urls']; ?>"></span><br/>
		
		<div id="video_decode_html_ent_url_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px;">
		
			<input class="checkbox" type="checkbox"<?php checked($video_decode_html_ent_url,true);?> name="video_decode_html_ent_url" value="1" id="video_decode_html_ent_url"/> 
			<label for="video_decode_html_ent_url"><b><?php _e('Decode html entities in URLs.', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['video_decode_html_ent_url']; ?>"></span>

		</div>

		<div id="video_follow_redirection_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px;">
		
			<input class="checkbox" type="checkbox"<?php checked($video_follow_redirection,true);?> name="video_follow_redirection" value="1" id="video_follow_redirection"/> 
			<label for="video_follow_redirection"><b><?php _e('Follow redirections to find the video file.', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['video_follow_redirection']; ?>"></span>

		</div>
			<p></p>
			<div id="videorenamer"  class="inmetabox">
				<input class="checkbox" type="checkbox"<?php checked($enable_video_rename,true);?> name="enable_video_rename" value="1" id="enable_video_rename"/> 
				<label for="enable_video_rename"><b><?php _e('Enable Video Renamer', 'wpematico' ); ?></b></label>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['enable_video_rename']; ?>"></span>
				<div id="no_video_ren" style="margin-left: 20px;<?php if (!$enable_video_rename) echo 'display:none;';?>">
					<b><label for="video_rename"><?php _e('Rename the images to', 'wpematico' ); ?>:</label></b>
					<input name="video_rename" type="text" size="3" value="<?php echo $video_rename;?>" class="regular-text" id="video_rename"/><br />
					<p class="description"><?php _e("Don't complete the extension of the file. This field is used to change the name and remains the same extension.",  'wpematico' ); ?><br />
						<?php printf( __("You can use %1s or %1s and will be replaced on uploading the video. Wordpress adds a number at the end if the video name already exists.",  'wpematico' ),
									'<a href="JavaScript:void(0);" onclick="jQuery(\'#video_rename\').val( jQuery(\'#video_rename\').val()+jQuery(this).text() );">{title}</a>',
									'<a href="JavaScript:void(0);" onclick="jQuery(\'#video_rename\').val( jQuery(\'#video_rename\').val()+jQuery(this).text() );">{slug}</a>'
							  ); 
						?>
					</p>
				</div>
			</div>
		
				
		<h3 class="subsection"><?php _e('From feed items','wpematico'); ?></h3>
			
		<p><input class="checkbox" type="checkbox"<?php checked($rss_video,true);?> name="rss_video" value="1" id="rss_video"/> <b><?php echo '<label for="rss_video">' . __('Get also Videos from RSS', 'wpematico' ) . '</label>'; ?></b></p>
		<div class="rss_video_opt" style="padding-left:20px; <?php if (!$rss_video) echo 'display:none;';?>">
			<input class="checkbox" type="checkbox"<?php checked($rss_video_enclosure,true);?> name="rss_video_enclosure" value="1" id="rss_video_enclosure"/> <b><label for="rss_video_enclosure"> <?php _e('Also enclosure and media RSS tags.', 'wpematico' ); ?></b></label>
			<br />
			<input class="checkbox" type="checkbox"<?php checked($rss_video_ifno,true);?> name="rss_video_ifno" value="1" id="rss_video_ifno"/> <b><label for="rssimg_ifno"> <?php _e('Only if no videos on content.', 'wpematico' ); ?></b></label>
			<br />
		</div>
		<p></p>

		<h3 class="subsection"><?php _e('From Content','wpematico'); ?></h3>
		<p><input class="checkbox" type="checkbox"<?php checked($strip_all_videos,true);?> name="strip_all_videos" value="1" id="strip_all_videos"/> <b><?php echo '<label for="strip_all_videos">' . __('Strip All Videos from Content.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['strip_all_videos']; ?>"></span>
		</p>
			
		<?php
	}
	public static function custitle_box( $post ) {
			global $post, $campaign_data, $helptip;
			$campaign_striptagstitle = @$campaign_data['campaign_striptagstitle'];
			$campaign_enablecustomtitle = @$campaign_data['campaign_enablecustomtitle'];
			$campaign_customtitle = @$campaign_data['campaign_customtitle'];
			$campaign_ctitlecont = @$campaign_data['campaign_ctitlecont'];
			$campaign_custitdup = @$campaign_data['campaign_custitdup'];
			$campaign_ctdigits = @$campaign_data['campaign_ctdigits'];
			$campaign_ctnextnumber = @$campaign_data['campaign_ctnextnumber'];
			$campaign_delete_till_ontitle = @$campaign_data['campaign_delete_till_ontitle'];
			$campaign_delete_till_ontitle_characters = @$campaign_data['campaign_delete_till_ontitle_characters'];
			$campaign_delete_till_ontitle_keep = @$campaign_data['campaign_delete_till_ontitle_keep'];
			$campaign_ontitle_cut_at = @$campaign_data['campaign_ontitle_cut_at'];
			$campaign_ontitle_cut_at_words = @$campaign_data['campaign_ontitle_cut_at_words'];

			?><p><b>
			<?php echo '<label for="campaign_striptagstitle">' . __('Strip HTML Tags From Title', 'wpematico' ) . '</label>'; ?></b> <input class="checkbox" type="checkbox"<?php checked($campaign_striptagstitle,true);?> name="campaign_striptagstitle" value="1" id="campaign_striptagstitle"/>
				<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_striptagstitle']; ?>"></span>
			</p>
			<p><b>
			<?php echo '<label for="campaign_enablecustomtitle">' . __('Enable Custom Post title', 'wpematico' ) . '</label>'; ?></b> <input class="checkbox" type="checkbox"<?php checked($campaign_enablecustomtitle,true);?> name="campaign_enablecustomtitle" value="1" id="campaign_enablecustomtitle"/> <span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_enablecustomtitle']; ?>"></span>
			</p>
		<div id="nocustitle" <?php if (!$campaign_enablecustomtitle) echo 'style="display:none;"';?>>					
			<p><b><?php echo '<label for="campaign_customtitle">' . __('Custom Title for every post:', 'wpematico' ) . '</label>'; ?></b>
			<input name="campaign_customtitle" type="text" size="3" value="<?php echo $campaign_customtitle;?>" class="regular-text" id="campaign_customtitle"/><br />
			<?php _e("You can write here the title for every post. All posts will be named with this field.",  'wpematico' ); ?><br />
			<?php _e("Now you can use {title} and {counter} and will be replaced on title.",  'wpematico' ); ?><br />
			<small><?php _e("If you don't use {counter} and checked the box below, by default the counter is added to end of title.",  'wpematico' ); ?></small>
			<br />
			<?php _e("Ex: 'New Post: {title}'",  'wpematico' ); ?>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked($campaign_custitdup, true);?> name="campaign_custitdup" value="1" id="campaign_custitdup"/>
				<b><?php echo '<label for="campaign_custitdup">' . __('Add an extra filter to check duplicates by Custom Post title', 'wpematico' ) . '</label>'; ?></b>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked($campaign_ctitlecont,true);?> name="campaign_ctitlecont" value="1" id="campaign_ctitlecont"/>
				<b><?php echo '<label for="campaign_ctitlecont">' . __('Add counter to Post title', 'wpematico' ) . '</label>'; ?></b>
			<div id="ctnocont" <?php if (!$campaign_ctitlecont) echo 'style="display:none;"';?>>	
				<b><?php echo '<label for="campaign_ctdigits">' . __('Min. Counter Digits:', 'wpematico' ) . '</label>'; ?></b>
				<select id="campaign_ctdigits" name="campaign_ctdigits" onchange="LCeros(getElementById('campaign_ctnextnumber'), this.value);" >
					<option value="1" <?php echo ($campaign_ctdigits=="1") ? 'SELECTED' : ''; ?> > 1</option>
					<option value="2" <?php echo ($campaign_ctdigits=="2" || $campaign_ctdigits=="") ? 'SELECTED' : ''; ?> > 2</option>
					<option value="3" <?php echo ($campaign_ctdigits=="3") ? 'SELECTED' : ''; ?> > 3</option>
					<option value="4" <?php echo ($campaign_ctdigits=="4") ? 'SELECTED' : ''; ?> > 4</option>
					<option value="5" <?php echo ($campaign_ctdigits=="5") ? 'SELECTED' : ''; ?> > 5</option>
					<option value="6" <?php echo ($campaign_ctdigits=="6") ? 'SELECTED' : ''; ?> > 6</option>
					<option value="7" <?php echo ($campaign_ctdigits=="7") ? 'SELECTED' : ''; ?> > 7</option>
					<option value="8" <?php echo ($campaign_ctdigits=="8") ? 'SELECTED' : ''; ?> > 8</option>
					<option value="9" <?php echo ($campaign_ctdigits=="9") ? 'SELECTED' : ''; ?> > 9</option>
				</select>
				<b><?php echo '<label for="campaign_ctnextnumber">' . __('Next Number:', 'wpematico' ) . '</label>'; ?></b>
				<input name="campaign_ctnextnumber" type="text" size="3" value="<?php echo sprintf("%0".$campaign_ctdigits."d",$campaign_ctnextnumber);?>" class="small-text" id="campaign_ctnextnumber" onblur="LCeros(this,getElementById('campaign_ctdigits').value);" style="width: 80px;"/></p>
			</div>
		</div>
		<div id="div_delete_till_title_feature">
			<b><label for="campaign_delete_till_ontitle"><?php _e('Enable delete till the end starting from some characters', 'wpematico' ); ?> </label></b> 
			<input class="checkbox" type="checkbox"<?php checked($campaign_delete_till_ontitle,true);?> name="campaign_delete_till_ontitle" value="1" id="campaign_delete_till_ontitle"/>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['campaign_delete_till_ontitle']; ?>"></span>
			<div id="div_delete_till_ontitle" <?php if (!$campaign_delete_till_ontitle) echo 'style="display:none;"';?>>	
				
				<b><label for="campaign_delete_till_ontitle_characters"><?php _e('Characters of end of sentence:', 'wpematico' ); ?></label></b>
				<input name="campaign_delete_till_ontitle_characters" type="text" value="<?php echo $campaign_delete_till_ontitle_characters; ?>" class="large-text" id="campaign_delete_till_ontitle_characters"/></p>
				<div>
					<b><label for="campaign_delete_till_ontitle_keep"><?php _e('Keep characters (Do not delete their)', 'wpematico' ); ?> </label></b> 
					<input class="checkbox" type="checkbox"<?php checked($campaign_delete_till_ontitle_keep,true);?> name="campaign_delete_till_ontitle_keep" value="1" id="campaign_delete_till_ontitle_keep"/>
				</div>
			</div>
		</div>

		<div id="div_cut_greater_title">
			<b><label for="campaign_ontitle_cut_at"><?php _e('Cut at:', 'wpematico' ); ?></label></b>
			<input name="campaign_ontitle_cut_at" type="number" min="0" value="<?php echo $campaign_ontitle_cut_at; ?>" class="small-text" id="campaign_ontitle_cut_at"/> <?php _e('Letter. If greater', 'wpematico'); ?></p>
			<div>
				<b><label for="campaign_ontitle_cut_at_words"><?php _e('Words', 'wpematico' ); ?> </label></b> 
				<input class="checkbox" type="checkbox"<?php checked($campaign_ontitle_cut_at_words,true);?> name="campaign_ontitle_cut_at_words" value="1" id="campaign_ontitle_cut_at_words"/>
			</div>
		</div>
			
		
		<?php
	}	
}
endif;
WPeMaticoPro_Campaign_Edit::hooks();

?>
