<?php
// don't load directly 
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

function wpematico_get_prosettings_sections() {
	$sections				 = array();
	$sections['settings']	 = __('Settings', 'wpematico');
	$sections				 = apply_filters('wpematico_get_prosettings_sections', $sections);

	return $sections;
}

/**
 * WPeMatico Pro Settings Class 
 * This class is used to management the Professional Settings 
 * @since 1.8.2
 */
if(!class_exists('WPeMaticoPro_Settings')) :

	class WPeMaticoPro_Settings {

		public static $current_options		 = null;
		public static $current_options_core	 = null;

		/**
		 * Static function hooks
		 * @access public
		 * @return void
		 * @since 1.8.2
		 */
		public static function hooks() {
			if(is_null(self::$current_options)) {
				self::$current_options = get_option(WPeMaticoPRO::OPTION_KEY);
			}
			if(is_null(self::$current_options_core)) {
				self::$current_options_core = get_option('WPeMatico_Options');
			}

			add_filter('wpematico_settings_tabs', array(__CLASS__, 'prosettings_tabs'));
			add_action('wpematico_settings_section_settings', array(__CLASS__, 'pro_settings_page'), 0, 5);
			add_action('wpematico_save_prosettings', array(__CLASS__, 'prosettings_save'));
		}

		/**
		 * Retrieve tools tabs
		 * @since       1.2.4
		 * @return      array
		 */
		public static function prosettings_tabs($tabs) {
			$protab	 = array('prosettings' => '<div style=""><span class="dashicons dashicons-awards"></span>' . __('Professional', 'wpematico') . '&nbsp;</div>');
			$tabs	 = array_slice($tabs, 0, 1, true) + $protab + array_slice($tabs, 1, count($tabs) - 1, true);
			return $tabs;
		}

		public static function pro_settings_page() {
			global $cfg, $current_screen;
			if(!isset($current_screen))	wp_die("Cheatin' uh?", "Closed today.");
			$cfg = get_option(WPeMaticoPRO::OPTION_KEY);
			?><form method="post" action="<?php admin_url('edit.php?post_type=wpematico&page=pro_settings'); ?>">
				<?php wp_nonce_field('wpematicopro-settings'); ?>
				<div class="wrap">
					<div id="poststuff" class="metabox-holder has-right-sidebar">
						<div id="side-info-column" class="inner-sidebar">
							<div id="side-sortables" class="meta-box-sortables ui-sortable">
								<div class="postbox">
									<h3 class="handle"><?php _e('About', 'wpematico'); ?></h3>
									<div class="inside">
										<p id="left1" style="text-align:center;">
											<a href="http://etruel.com/downloads/wpematico-professional/" target="_Blank" title="Go to new etruel WebSite">
												<img style="width: 100%; background: transparent;border-radius: 15px;" src="https://etruel.com/wp-content/uploads/2018/08/wpematico-professional-520x260.png" title="">
											</a><br />
											<b>WPeMatico PRO <?php echo WPEMATICOPRO_VERSION; ?></b>
										</p>
										<p><?php _e('Thanks for use and enjoy this plugin.', 'wpematico'); ?></p>
										<p><?php _e('If you like it and want to thank, you can write a 5 star review on Wordpress.', 'wpematico'); ?></p>
										<style type="text/css">#linkrate:before { content: "\2605\2605\2605\2605\2605";font-size: 18px;}
											#linkrate { font-size: 18px;}</style>
										<p style="text-align: center;">
											<a href="https://wordpress.org/support/view/plugin-reviews/wpematico?filter=5&rate=5#postform" id="linkrate" class="button" target="_Blank" title="Click here to rate plugin on Wordpress">  Rate</a>
										</p>
										<p></p>
									</div>
								</div>

								<div class="postbox"><div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br></div>
									<h3 class="handle"><?php _e('Advanced Features', 'wpematico'); ?></h3>
									<div class="inside">
										<p></p>
											<label><input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enable_word_to_taxonomy'], true); ?> name="enable_word_to_taxonomy" id="enable_word_to_taxonomy" /> <?php _e('Enable <b><i>Word 2 Taxonomy</i></b> feature', 'wpematico'); ?>
												<span class="dashicons dashicons-warning help_tip" title="<?php echo esc_html__('This is for assigning taxonomies based on content words.', 'wpematico'); ?>"></span></label>
										<p></p>
											<label><input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enable_ramdom_words_rewrites'], true); ?> name="enable_ramdom_words_rewrites" id="enable_ramdom_words_rewrites" /> <?php _e('Enable <b><i>Ramdom Rewrites</i></b> feature', 'wpematico'); ?>
												<span class="dashicons dashicons-warning help_tip" title="<?php echo esc_html__('Rewrite custom words randomly as synonyms.  You must complete the words separated by comma and per line in the textarea.', 'wpematico'); ?>"></span></label>
											<?php echo (($cfg['enable_ramdom_words_rewrites']) ? sprintf(__('Complete it in the <a href="%s">textarea</a>.', 'wpematico'), admin_url('edit.php?post_type=wpematico&page=wpematico_settings&tab=prosettings&section=ramdom_rewrites')) : ''); ?>
										<p></p>
										<label><input class="checkbox" value="1" type="checkbox" <?php @checked($cfg['enableeximport'], true); ?> name="enableeximport" id="enableeximport" /> <?php _e('Enable <b><i>Export/Import</i></b> single Campaign', 'wpematico'); ?></label>
										<p></p>
										<label><input class="checkbox" value="1" type="checkbox" <?php @checked($cfg['enablepromenu'], true); ?> name="enablepromenu" id="enablepromenu" /> <?php _e('Enable <b><i>PRO Settings</i></b> menu item', 'wpematico'); ?></label>
										<p></p>
									</div>
								</div>
								<div class="postbox">
									<div class="inside">
										<p>
											<input type="hidden" name="wpematico-action" value="save_prosettings" />
											<?php submit_button(__('Save settings', 'wpematico'), 'primary', 'wpematico-save-prosettings', false); ?>
										</p>
									</div>
								</div>

								<?php do_action('wpematico_wp_ratings'); ?>
							</div>
						</div>
						<div id="post-body">
							<div id="post-body-content">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<div class="postbox inside">
										<h3><?php _e('PRO options', 'wpematico'); ?></h3>
										<div class="inside">
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enablekwordf'], true); ?> name="enablekwordf" id="enablekwordf" /> <?php _e('Enable <b><i>Keyword Filtering</i></b> feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need this feature in every campaign, you can activate here.  Not recommended if you will not use this.', 'wpematico'); ?><br /> 
												<?php _e('This is for exclude or include posts according to the keywords <b>found</b> at content or title.', 'wpematico'); ?>
											</div><br /> 
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enablewcf'], true); ?> name="enablewcf" id="enablewcf" /> <?php _e('Enable <b><i>Word count Filters</i></b> feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need this feature in every campaign, you can activate here.  Not recommended if you will not use this.', 'wpematico'); ?><br /> 
												<?php _e('This is for cut, exclude or include posts according to the letters o words <b>counted</b> at content.', 'wpematico'); ?>
											</div><br /> 
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enablecustomtitle'], true); ?> name="enablecustomtitle" id="enablecustomtitle" /> <?php _e('Enable <b><i>Custom Title</i></b> feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you want a custom title for posts of a campaign, you can activate here.  Not recommended if you will not use this.', 'wpematico'); ?><br />
											</div><br /> 
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enableauthorxfeed'], true); ?> name="enableauthorxfeed" id="enableauthorxfeed" /> <?php _e('Enable <b><i>Author per feed</i></b> feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('This option allow you assign an author per feed when editing campaign.  If no choice any author, the campaign author will be taken.', 'wpematico'); ?><br />
											</div><br />
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enableimportfeed'], true); ?> name="enableimportfeed" id="enableimportfeed" /> <?php _e('Enable <b><i>Import feed list</i></b> feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('On campaign edit you can import, copy & paste in a textarea field, a list of feed addresses with/out author names.', 'wpematico'); ?><br />
											</div><br />
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enablemultifeed'], true); ?> name="enablemultifeed" id="enablemultifeed" /> <?php _e('Enable <b><i>Multipaged</i></b> feeds feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('On campaign edit you can set the fetching process to multipaged RSS feeds.  Like https://etruel.com/feed/?paged=1', 'wpematico'); ?><br />
											</div><br />
											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enabletags'], true); ?> name="enabletags" id="enabletags" onclick="if (true == jQuery(this).is(':checked'))
																	jQuery('#badtags').fadeIn();
																else
																	jQuery('#badtags').fadeOut();" /> <?php _e('Enable <b><i>Auto Tags</i></b> feature.', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('This feature generate tags automatically on every post fetched, on campaign edit you can disable auto feature and manually enter a list of tags or leave empty.', 'wpematico'); ?><br />
											</div>
											<div id="badtags" style="margin-left:20px;<?php if(!$cfg['enabletags']) echo 'display:none;'; ?>">
												<b><?php echo '<label for="all_badtags">' . __('Bad Tags that will be not used on all posts:', 'wpematico') . '</label>'; ?></b><br />
												<textarea style="width:600px;" id="all_badtags" name="all_badtags"><?php echo stripslashes(@$cfg['all_badtags']); ?></textarea><br />
												<?php echo __('Enter comma separated list of excluded Tags in all campaigns.', 'wpematico'); ?>
											</div><br />

											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enablecfields'], true); ?> name="enablecfields" id="enablecfields" /> <?php _e('Enable <b><i>Custom Fields</i></b> feature.', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('Add custom fields with values as templates on every post.', 'wpematico'); ?><br />
											</div><br />

											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enable_custom_feed_tags'], true); ?> name="enable_custom_feed_tags" id="enable_custom_feed_tags" /> <?php _e('Enable <b><i>Custom Feed Tags</i></b> feature.', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('Add custom feed tags as template tags or custom field values on every post.', 'wpematico'); ?><br />
											</div><br />

											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enableimgfilter'], true); ?> name="enableimgfilter" id="enableimgfilter" /> <?php _e('Enable <b><i>Image Filters</i></b> feature.', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('You can allow or skip each image in every post depends on image dimensions.', 'wpematico'); ?><br />
											</div><br />

											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['end_of_the_line'], true); ?> name="end_of_the_line" id="end_of_the_line" onclick="if (true == jQuery(this).is(':checked'))
																	jQuery('#end_of_the_line_div').fadeIn();
																else
																	jQuery('#end_of_the_line_div').fadeOut();" /> <?php _e('Enable <b><i>Deletes till the end of the line</i></b> feature.', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('This feature allows to delete from a word or phrase until the end of the line of a sentence.', 'wpematico'); ?><br />
											</div>
											<div id="end_of_the_line_div" style="margin-left:20px;<?php if(!$cfg['end_of_the_line']) echo 'display:none;'; ?>">
												<?php if(!defined('WPEBETTEREXCERPTS_VERSION')) :
													?>
													<b><?php echo '<label for="end_of_the_line_characters">' . __('Characters of end of sentence:', 'wpematico') . '</label>'; ?></b><br />
													<input style="width:600px;" id="end_of_the_line_characters" name="end_of_the_line_characters" value="<?php echo stripslashes(@$cfg['end_of_the_line_characters']); ?>"><br />
													<?php echo __('Enter space separated list of characters of end of the line.', 'wpematico'); ?>
													<?php
												else:
													?>
													<b><?php _e('Characters of end of sentence in the', 'wpematico'); ?> <a href="<?php echo admin_url('edit.php?post_type=wpematico&page=wpematico_settings&tab=better_excerpts'); ?>"><?php _e('Better Excerpt Options', 'wpematico'); ?></a></b><br />
												<?php
												endif;
												?>
											</div><br />

											<input class="checkbox" value="1" type="checkbox" <?php checked($cfg['enable_filter_per_author'], true); ?> name="enable_filter_per_author" id="enable_filter_per_author" /> <?php _e('Enable <b><i>Author Filtering</i></b> feature', 'wpematico'); ?><br />
											<div id="hlpw2c" style="padding-left:20px;"><?php _e('If you need this feature in every campaign, you can activate here.  Not recommended if you will not use this.', 'wpematico'); ?><br /> 
												<?php _e('This is for exclude or include posts according to the authors <b>found</b> at posts.', 'wpematico'); ?>
											</div><br /> 

											<p>
												<input type="hidden" name="wpematico-action" value="save_prosettings" />
												<?php submit_button(__('Save settings', 'wpematico'), 'primary', 'wpematico-save-prosettings', false); ?>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<?php
		}

		public static function prosettings_save() {
			if('POST' === $_SERVER['REQUEST_METHOD']) {
				if(get_magic_quotes_gpc()) {
					$_POST = array_map('stripslashes_deep', $_POST);
				}

				# evaluation goes here
				check_admin_referer('wpematicopro-settings');
				$cfg = array();

				$errlev = error_reporting();
				error_reporting(E_ALL & ~E_NOTICE);  // desactivo los notice que aparecen con los _POST

				$cfg['enablekwordf']		 = $_POST['enablekwordf'] == 1 ? true : false;
				$cfg['enablewcf']			 = $_POST['enablewcf'] == 1 ? true : false;
				$cfg['enablecustomtitle']	 = $_POST['enablecustomtitle'] == 1 ? true : false;
				$cfg['enablefullcontent']	 = $_POST['enablefullcontent'] == 1 ? true : false;
				$cfg['enableauthorxfeed']	 = $_POST['enableauthorxfeed'] == 1 ? true : false;
				$cfg['enableimportfeed']	 = $_POST['enableimportfeed'] == 1 ? true : false;
				$cfg['enablemultifeed']		 = $_POST['enablemultifeed'] == 1 ? true : false;
				$cfg['enabletags']			 = $_POST['enabletags'] == 1 ? true : false;
				$cfg['all_badtags']			 = sanitize_text_field($_POST['all_badtags']);
				$cfg['enablecfields']		 = $_POST['enablecfields'] == 1 ? true : false;
				$cfg['enableimgfilter']		 = $_POST['enableimgfilter'] == 1 ? true : false;

				$cfg['enableeximport']				 = $_POST['enableeximport'] == 1 ? true : false;
				$cfg['enablepromenu']				 = $_POST['enablepromenu'] == 1 ? true : false;
				$cfg['enable_ramdom_words_rewrites'] = $_POST['enable_ramdom_words_rewrites'] == 1 ? true : false;

				$cfg['end_of_the_line']				 = $_POST['end_of_the_line'] == 1 ? true : false;
				$cfg['end_of_the_line_characters']	 = sanitize_text_field($_POST['end_of_the_line_characters']);

				$cfg['enable_custom_feed_tags']	 = $_POST['enable_custom_feed_tags'] == 1 ? true : false;
				$cfg['enable_filter_per_author'] = $_POST['enable_filter_per_author'] == 1 ? true : false;
				$cfg['enable_word_to_taxonomy']	 = $_POST['enable_word_to_taxonomy'] == 1 ? true : false;

				if(update_option(WPeMaticoPRO::OPTION_KEY, $cfg)) {
					WPeMatico::add_wp_notice(array('text' => __('Settings saved.', 'wpematico'), 'below-h2' => false));
				}
				error_reporting($errlev);
				wp_redirect(admin_url('edit.php?post_type=wpematico&page=wpematico_settings&tab=prosettings'));
			}
		}

	}

	endif;
WPeMaticoPro_Settings::hooks();

