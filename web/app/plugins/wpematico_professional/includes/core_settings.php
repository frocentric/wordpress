<?php
// don't load directly 
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
* WPeMatico Pro Core Settings Class 
* This class is used to management the Settings of WPeMatico Core.
* @since 1.8.2
*/
if (!class_exists('WPeMaticoPro_Core_Settings')) :
class WPeMaticoPro_Core_Settings {
	public static $current_options = null;
	public static $current_options_core = null;
	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function hooks() {
		if (is_null(self::$current_options)) {
			self::$current_options = get_option(WPeMaticoPRO::OPTION_KEY);
		}
		if (is_null(self::$current_options_core)) {
			self::$current_options_core = get_option('WPeMatico_Options');
		}
		add_action('admin_print_scripts', array(__CLASS__, 'scripts') );
		add_action('wpematico_settings_audios', array(__CLASS__, 'audio_setting'), 10, 1);
		add_action('wpematico_settings_videos', array(__CLASS__, 'video_setting'), 10, 1);

		add_filter('wpematico_more_options', array(__CLASS__, 'save_extras_pro_options'), 10, 2);
		add_filter('wpematico_audios_options', array(__CLASS__, 'set_audios_options'), 10, 3);
		add_filter('wpematico_videos_options', array(__CLASS__, 'set_videos_options'), 10, 3);
	}
	/**
	* Static function scripts
	* @access public
	* @return void
	* @since 1.8.2
	*/
	public static function scripts() {
		$current_screen = get_current_screen();
		$on_core = empty($_REQUEST['tab']) ? true : $_REQUEST['tab'] == 'settings' ? true : false;
		if (isset($current_screen->id) && $current_screen->id == 'wpematico_page_wpematico_settings' && $on_core) {
			wp_enqueue_script( 'wpepro-core-settings', WPeMaticoPRO::$uri.'assets/js/core_settings.js', array( 'jquery' ), WPEMATICOPRO_VERSION, true );
		}
	}
	public static function video_setting($cfg) { 
		global $helptip;
		$overwrite_video = @$cfg['overwrite_video'];
		$clean_videos_urls = @$cfg['clean_videos_urls'];
		$rss_video = @$cfg['rss_video'];
		$rss_video_enclosure = @$cfg['rss_video_enclosure'];
		$rss_video_ifno = @$cfg['rss_video_ifno'];
		$strip_all_videos = @$cfg['strip_all_videos'];
		$enable_video_rename = @$cfg['enable_video_rename'];
		$video_rename = @$cfg['video_rename'];
		
		
		$upload_ranges = @$cfg['video_upload_ranges'];
		$upload_range_mb = @$cfg['video_upload_range_mb'];	
		?>

		<div id="video_upload_ranges_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px; <?php if (!$cfg['video_cache']) echo 'display:none;';?>">
			
			<input class="checkbox" type="checkbox"<?php checked($upload_ranges,true);?> name="video_upload_ranges" value="1" id="video_upload_ranges"/> 
			<label for="video_upload_ranges"><b><?php _e('Upload by ranges', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['upload_ranges']; ?>"></span>
			<div id="video_upload_range_mb_div" style="margin-left: 20px;<?php if (!$upload_ranges) echo 'display:none;';?>">
				<b><label for="video_rename"><?php _e('MBs per ranges', 'wpematico' ); ?>:</label></b>
				<input name="video_upload_range_mb" type="number" min="1" value="<?php echo $upload_range_mb;?>" id="video_upload_range_mb"/><br />
			</div>
			<br/>
		</div>

		<div id="contanier_video_ren" <?php echo ($cfg['customupload_videos']) ? '' : ' style="display:none;"'; ?>>
		
			<p><b><?php _e('Determine what happens with duplicated video names',  'wpematico' ); ?></b></p>
			<div id="what_video_ren" style="margin-left: 20px;">
				<label><input type="radio" name="overwrite_video" <?php echo checked('rename',$overwrite_video,false); ?> value="rename" /> <?php _e('Rename like Wordpress standards (name-1)'); ?></label><br />
				<label><input type="radio" name="overwrite_video" <?php echo checked('overwrite',$overwrite_video,false); ?> value="overwrite" /> <?php _e('Always Overwrite'); ?></label><br />
				<label><input type="radio" name="overwrite_video" <?php echo checked('keep',$overwrite_video,false); ?> value="keep" /> <?php _e('Always keep the first. Recommended.'); ?></label><br />
			</div>
			<p></p>
		</div>

		<input class="checkbox" type="checkbox"<?php checked($clean_videos_urls,true);?> name="clean_videos_urls" value="1" id="clean_videos_urls"/> <b><?php echo '<label for="clean_videos_urls">' . __('Strip the queries variables in videos URLs.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['clean_videos_urls']; ?>"></span><br/>

		<p></p>
		<div id="videorenamer"  class="inmetabox">
			<input class="checkbox" type="checkbox"<?php checked($enable_video_rename,true);?> name="enable_video_rename" value="1" id="enable_video_rename"/> 
			<label for="enable_video_rename"><b><?php _e('Enable Video Renamer', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['enable_video_rename']; ?>"></span>
			<div id="no_video_ren" style="margin-left: 20px;<?php if (!$enable_video_rename) echo 'display:none;';?>">
				<b><label for="video_rename"><?php _e('Rename the images to', 'wpematico' ); ?>:</label></b>
				<input name="video_rename" type="text" size="3" value="<?php echo $video_rename;?>" class="regular-text" id="video_rename"/><br />
				<p class="description"><?php _e("Don't complete the extension of the file. This field is used to change the name and remains the same extension.",  'wpematico' ); ?><br />
					<?php 
						printf( __("You can use %1s or %1s and will be replaced on uploading the video. Wordpress adds a number at the end if the video name already exists.",  'wpematico' ),
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
			<input class="checkbox" type="checkbox"<?php checked($rss_video_ifno,true);?> name="rss_video_ifno" value="1" id="rss_video_ifno"/> <b><label for="rss_video_ifno"> <?php _e('Only if no videos on content.', 'wpematico' ); ?></b></label>
			<br />
		</div>
		<p></p>

		<h3 class="subsection"><?php _e('From Content','wpematico'); ?></h3>
		<p><input class="checkbox" type="checkbox"<?php checked($strip_all_videos,true);?> name="strip_all_videos" value="1" id="strip_all_videos"/> <b><?php echo '<label for="strip_all_videos">' . __('Strip All Videos from Content.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['strip_all_videos']; ?>"></span>
		</p>
		

	<?php
	}
	public static function audio_setting($cfg) { 
		global $helptip;
		$overwrite_audio = @$cfg['overwrite_audio'];
		$clean_audios_urls = @$cfg['clean_audios_urls'];
		$rss_audio = @$cfg['rss_audio'];
		$rss_audio_enclosure = @$cfg['rss_audio_enclosure'];
		$rss_audio_ifno = @$cfg['rss_audio_ifno'];
		$strip_all_audios = @$cfg['strip_all_audios'];
		$enable_audio_rename = @$cfg['enable_audio_rename'];
		$audio_rename = @$cfg['audio_rename'];

		$upload_ranges = @$cfg['audio_upload_ranges'];
		$upload_range_mb = @$cfg['audio_upload_range_mb'];
			
		?>

		<div id="audio_upload_ranges_div"  class="inmetabox" style="margin-top:10px; margin-bottom:10px; <?php if (!$cfg['audio_cache']) echo 'display:none;';?>">
			
			<input class="checkbox" type="checkbox"<?php checked($upload_ranges,true);?> name="audio_upload_ranges" value="1" id="audio_upload_ranges"/> 
			<label for="audio_upload_ranges"><b><?php _e('Upload by ranges', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['upload_ranges']; ?>"></span>
			<div id="audio_upload_range_mb_div" style="margin-left: 20px;<?php if (!$upload_ranges) echo 'display:none;';?>">
				<b><label for="audio_rename"><?php _e('MBs per ranges', 'wpematico' ); ?>:</label></b>
				<input name="audio_upload_range_mb" type="number" min="1" value="<?php echo $upload_range_mb;?>" id="audio_upload_range_mb"/><br />
			</div>
			<br/>
		</div>

		<div id="contanier_audio_ren" <?php echo ($cfg['customupload_audios']) ? '' : ' style="display:none;"'; ?>>
		
			<p><b><?php _e('Determine what happens with duplicated audio names',  'wpematico' ); ?></b></p>
			<div id="what_audio_ren" style="margin-left: 20px;">
				<label><input type="radio" name="overwrite_audio" <?php echo checked('rename',$overwrite_audio,false); ?> value="rename" /> <?php _e('Rename like Wordpress standards (name-1)'); ?></label><br />
				<label><input type="radio" name="overwrite_audio" <?php echo checked('overwrite',$overwrite_audio,false); ?> value="overwrite" /> <?php _e('Always Overwrite'); ?></label><br />
				<label><input type="radio" name="overwrite_audio" <?php echo checked('keep',$overwrite_audio,false); ?> value="keep" /> <?php _e('Always keep the first. Recommended.'); ?></label><br />
			</div>
			<p></p>
		</div>

		<input class="checkbox" type="checkbox"<?php checked($clean_audios_urls,true);?> name="clean_audios_urls" value="1" id="clean_audios_urls"/> <b><?php echo '<label for="clean_audios_urls">' . __('Strip the queries variables in audios URLs.', 'wpematico' ) . '</label>'; ?></b>
		<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['clean_audios_urls']; ?>"></span><br/>

		<p></p>
		<div id="audiorenamer"  class="inmetabox">
			<input class="checkbox" type="checkbox"<?php checked($enable_audio_rename,true);?> name="enable_audio_rename" value="1" id="enable_audio_rename"/> 
			<label for="enable_audio_rename"><b><?php _e('Enable Audio Renamer', 'wpematico' ); ?></b></label>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['enable_audio_rename']; ?>"></span>
			<div id="no_audio_ren" style="margin-left: 20px;<?php if (!$enable_audio_rename) echo 'display:none;';?>">
				<b><label for="audio_rename"><?php _e('Rename the images to', 'wpematico' ); ?>:</label></b>
				<input name="audio_rename" type="text" size="3" value="<?php echo $audio_rename;?>" class="regular-text" id="audio_rename"/><br />
				<p class="description"><?php _e("Don't complete the extension of the file. This field is used to change the name and remains the same extension.",  'wpematico' ); ?><br />
					<?php 
						printf( __("You can use %1s or %1s and will be replaced on uploading the audio. Wordpress adds a number at the end if the audio name already exists.",  'wpematico' ),
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
			<input class="checkbox" type="checkbox"<?php checked($rss_audio_ifno,true);?> name="rss_audio_ifno" value="1" id="rss_audio_ifno"/> <b><label for="rss_audio_ifno"> <?php _e('Only if no audios on content.', 'wpematico' ); ?></b></label>
			<br />
		</div>
		<p></p>

		<h3 class="subsection"><?php _e('From Content','wpematico'); ?></h3>
		<p><input class="checkbox" type="checkbox"<?php checked($strip_all_audios,true);?> name="strip_all_audios" value="1" id="strip_all_audios"/> <b><?php echo '<label for="strip_all_audios">' . __('Strip All Audios from Content.', 'wpematico' ) . '</label>'; ?></b>
			<span class="dashicons dashicons-warning help_tip" title="<?php echo $helptip['strip_all_audios']; ?>"></span>
		</p>
		

	<?php
	}
	public static function set_audios_options($options, $settings, $campaign) {
		$options['overwrite_audio'] = $settings['overwrite_audio'];
		$options['clean_audios_urls'] = $settings['clean_audios_urls'];
		$options['rss_audio'] = $settings['rss_audio'];
		$options['rss_audio_enclosure'] = $settings['rss_audio_enclosure'];
		$options['rss_audio_ifno'] = $settings['rss_audio_ifno'];
		$options['strip_all_audios'] = $settings['strip_all_audios'];
		$options['enable_audio_rename'] = $settings['enable_audio_rename'];
		$options['audio_rename'] = $settings['audio_rename'];
		$options['upload_ranges'] = $settings['audio_upload_ranges'];
		$options['upload_range_mb'] = $settings['audio_upload_range_mb'];

		if(isset($campaign['campaign_no_setting_audio']) && $campaign['campaign_no_setting_audio']) {
			$options['overwrite_audio'] = $campaign['overwrite_audio'];
			$options['clean_audios_urls'] = $campaign['clean_audios_urls'];
			$options['rss_audio'] = $campaign['rss_audio'];
			$options['rss_audio_enclosure'] = $campaign['rss_audio_enclosure'];
			$options['rss_audio_ifno'] = $campaign['rss_audio_ifno'];
			$options['strip_all_audios'] = $campaign['strip_all_audios'];
			$options['enable_audio_rename'] = $campaign['enable_audio_rename'];
			$options['audio_rename'] = $campaign['audio_rename'];
			$options['upload_ranges'] = $campaign['audio_upload_ranges'];
			$options['upload_range_mb'] = $campaign['audio_upload_range_mb'];
		}
		return $options;
	}
	public static function set_videos_options($options, $settings, $campaign) {
		$options['overwrite_video'] = $settings['overwrite_video'];
		$options['clean_videos_urls'] = $settings['clean_videos_urls'];
		$options['rss_video'] = $settings['rss_video'];
		$options['rss_video_enclosure'] = $settings['rss_video_enclosure'];
		$options['rss_video_ifno'] = $settings['rss_video_ifno'];
		$options['strip_all_videos'] = $settings['strip_all_videos'];
		$options['enable_video_rename'] = $settings['enable_video_rename'];
		$options['video_rename'] = $settings['video_rename'];
		$options['upload_ranges'] = $settings['video_upload_ranges'];
		$options['upload_range_mb'] = $settings['video_upload_range_mb'];

		if(isset($campaign['campaign_no_setting_video']) && $campaign['campaign_no_setting_video']) {
			$options['overwrite_video'] = $campaign['overwrite_video'];
			$options['clean_videos_urls'] = $campaign['clean_videos_urls'];
			$options['rss_video'] = $campaign['rss_video'];
			$options['rss_video_enclosure'] = $campaign['rss_video_enclosure'];
			$options['rss_video_ifno'] = $campaign['rss_video_ifno'];
			$options['strip_all_videos'] = $campaign['strip_all_videos'];
			$options['enable_video_rename'] = $campaign['enable_video_rename'];
			$options['video_rename'] = $campaign['video_rename'];
			$options['upload_ranges'] = $campaign['video_upload_ranges'];
			$options['upload_range_mb'] = $campaign['video_upload_range_mb'];
		}

		return $options;
	}
	/**
	* Save extras professionals options on core settings
	* @access public
	* @return $cfg Array of all options that will be saved.
	* @since 1.8.2
	*/
	public static function save_extras_pro_options($cfg, $options) {
	
		$cfg['overwrite_audio']	= (isset($options['overwrite_audio']) && !empty($options['overwrite_audio']) ) ? $options['overwrite_audio'] : 'rename' ;
		$cfg['clean_audios_urls']		= (!isset($options['clean_audios_urls']) || empty($options['clean_audios_urls'])) ? false: ($options['clean_audios_urls']==1) ? true : false;
		$cfg['rss_audio']	= (!isset($options['rss_audio']) || empty($options['rss_audio'])) ? false: ($options['rss_audio']==1) ? true : false;
		$cfg['rss_audio_enclosure']	= (!isset($options['rss_audio_enclosure']) || empty($options['rss_audio_enclosure'])) ? false: ($options['rss_audio_enclosure']==1) ? true : false;
		$cfg['rss_audio_ifno']	= (!isset($options['rss_audio_ifno']) || empty($options['rss_audio_ifno'])) ? false: ($options['rss_audio_ifno']==1) ? true : false;
		$cfg['strip_all_audios']	= (!isset($options['strip_all_audios']) || empty($options['strip_all_audios'])) ? false: ($options['strip_all_audios']==1) ? true : false;
		$cfg['enable_audio_rename']	= (!isset($options['enable_audio_rename']) || empty($options['enable_audio_rename'])) ? false: ($options['enable_audio_rename']==1) ? true : false;
		$cfg['audio_rename'] 	= (isset($options['audio_rename']) && !empty($options['audio_rename']) ) ? $options['audio_rename'] : '{slug}';	
		$cfg['audio_upload_ranges']	= (!isset($options['audio_upload_ranges']) || empty($options['audio_upload_ranges'])) ? false: ($options['audio_upload_ranges']==1) ? true : false;
		$cfg['audio_upload_range_mb'] 	= (isset($options['audio_upload_range_mb']) && !empty($options['audio_upload_range_mb']) ) ? $options['audio_upload_range_mb'] : '5';	

		$cfg['overwrite_video']	= (isset($options['overwrite_video']) && !empty($options['overwrite_video']) ) ? $options['overwrite_video'] : 'rename' ;
		$cfg['clean_videos_urls']		= (!isset($options['clean_videos_urls']) || empty($options['clean_videos_urls'])) ? false: ($options['clean_videos_urls']==1) ? true : false;
		$cfg['rss_video']	= (!isset($options['rss_video']) || empty($options['rss_video'])) ? false: ($options['rss_video']==1) ? true : false;
		$cfg['rss_video_enclosure']	= (!isset($options['rss_video_enclosure']) || empty($options['rss_video_enclosure'])) ? false: ($options['rss_video_enclosure']==1) ? true : false;
		$cfg['rss_video_ifno']	= (!isset($options['rss_video_ifno']) || empty($options['rss_video_ifno'])) ? false: ($options['rss_video_ifno']==1) ? true : false;
		$cfg['strip_all_videos']	= (!isset($options['strip_all_videos']) || empty($options['strip_all_videos'])) ? false: ($options['strip_all_videos']==1) ? true : false;
		$cfg['enable_video_rename']	= (!isset($options['enable_video_rename']) || empty($options['enable_video_rename'])) ? false: ($options['enable_video_rename']==1) ? true : false;
		$cfg['video_rename'] 	= (isset($options['video_rename']) && !empty($options['video_rename']) ) ? $options['video_rename'] : '{slug}';	
		$cfg['video_upload_ranges']	= (!isset($options['video_upload_ranges']) || empty($options['video_upload_ranges'])) ? false: ($options['video_upload_ranges']==1) ? true : false;
		$cfg['video_upload_range_mb'] 	= (isset($options['video_upload_range_mb']) && !empty($options['video_upload_range_mb']) ) ? $options['video_upload_range_mb'] : '5';

		return $cfg;
	}
	
	
}
endif;
WPeMaticoPro_Core_Settings::hooks();
?>