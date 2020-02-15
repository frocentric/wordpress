<?php
/**
 *  @package WPeMatico Full Content
 *  @subpackage Campaign Edit 
 * */
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}


if(!class_exists('WPeMatico_FullContent_Campaign_Edit')) :

	class WPeMatico_FullContent_Campaign_Edit {

		public static function hooks() {
			add_action('admin_print_styles-post.php', array(__CLASS__, 'styles'));
			add_action('admin_print_styles-post-new.php', array(__CLASS__, 'styles'));
			add_action('admin_print_scripts-post.php', array(__CLASS__, 'scripts'));
			add_action('admin_print_scripts-post-new.php', array(__CLASS__, 'scripts'));


			// Metabox campaigns 
			add_action('add_meta_boxes', array(__CLASS__, 'metaboxes'), 15, 0);

			add_filter('pro_check_campaigndata', array(__CLASS__, 'check_campaigndata'), 15, 2);
		}

		public static function styles() {
			global $post;
			if($post->post_type != 'wpematico')
				return $post->ID;
		}

		public static function scripts() {
			global $post;
			if($post->post_type != 'wpematico')
				return $post->ID;

			wp_enqueue_script('wpematico_fullcontent_campaign_edit_js', WPEFULLCONTENT_URL . 'assets/js/campaign_edit.js');
			wp_localize_script('wpematico_fullcontent_campaign_edit_js', 'fullcontent_object', array(
				'fullcontent_message' => __('You cannot use this features with "Youtube Fetcher" campaign type.', 'WPeMatico_fullcontent')
			));
		}

		public static function metaboxes() {
			global $pagenow, $post;
			if(!(($pagenow == 'post.php' || $pagenow == 'post-new.php') && $post->post_type == 'wpematico' ))
				return false;

			add_meta_box('fullcontent-box', __('Full Content Options', 'wpematico'), array(__CLASS__, 'box'), 'wpematico', 'normal', 'default');
		}

		public static function box($post) {
			global $post, $campaign_data;
			//$campaign_fullcontent = $campaign_data['campaign_fullcontent'];
			//$campaign_usecurl = $campaign_data['campaign_usecurl'];
			?>
			<p><label><input class="checkbox" type="checkbox" <?php checked($campaign_data['campaign_fullcontent'], true); ?> name="campaign_fullcontent" value="1" id="campaign_fullcontent"/>
					<b><?php _e('Get complete item content from the original item link', 'wpematico'); ?></b></label><br />
				<?php _e('This feature tries to get the original full article through the item link.', 'wpematico'); ?> <?php _e('To get this it parses the entire content of the source webpage and strips the useless html tags.', 'wpematico'); ?>
			</p>
			<p><label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_usecurl'], true); ?> name="campaign_usecurl" value="1" id="campaign_usecurl"/>
					<b><?php _e('Use cURL', 'wpematico'); ?></b></label><br />
				<?php _e('Forces to use standard PHP cURL to scratches the source webpage, before trying with file_get_contents and Wordpress functions. RECOMMENDED.', 'wpematico'); ?>
			</p>
			<p><label><input class="checkbox" onclick="t = jQuery(this);
								if (t.is(':checked'))
									t.parent().parent().next().fadeIn();
								else
									t.parent().parent().next().fadeOut();" type="checkbox"<?php checked($campaign_data['campaign_ogimage'], true); ?> name="campaign_ogimage" value="1" id="campaign_ogimage"/>
					<b><?php _e('Use Open Graph for featured image', 'wpematico'); ?></b></label><br />
				<?php _e('Search for meta tag on source head, og:image (like facebook) to get the url of the featured image. If it is not found then search for twitter:image.', 'wpematico'); ?>
			</p>	
			<div id="div_ogimage"  style="margin-left: 20px; display: <?php echo ($campaign_data['campaign_ogimage']) ? 'block' : 'none'; ?>;">
				<p>
					<label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_ogimage_above_content'], true); ?> name="campaign_ogimage_above_content" value="1" id="campaign_ogimage_above_content"/>
						<b><?php _e('Add it above the content.', 'wpematico'); ?></b></label><br />
					<?php _e('This allows adding the Open Graph image to the beginning of the content.', 'wpematico'); ?>
				</p>
				<p>
					<label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_ogimage_if_not_in_content'], true); ?> name="campaign_ogimage_if_not_in_content" value="1" id="campaign_ogimage_if_not_in_content"/>
						<b><?php _e('Only if no image in content.', 'wpematico'); ?></b></label><br />
					<?php _e('Do not add the og:image as featured and at the beginning of the post, <strong>if there are images in the content</strong>.', 'wpematico'); ?>
				</p>

			</div>

			<p><label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_fullmultipage'], true); ?> name="campaign_fullmultipage" value="1" id="campaign_fullmultipage"/>
					<b><?php _e('Searches for multi-page articles', 'wpematico'); ?></b></label><br />
				<?php _e('Searches and makes an unique content for articles divided into multiples pages. The xPath for the \'next\' link must be set in config file.', 'wpematico'); ?>
			</p>
			<p><label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_fulltitle'], true); ?> name="campaign_fulltitle" value="1" id="campaign_fulltitle"/>
					<b><?php _e('Get the Title from the source webpage', 'wpematico'); ?></b></label><br />
				<?php _e('Forces to use the title obtained from the full content if not blank, instead of the feed item title. This will overwrite the custom title Professional feature.', 'wpematico'); ?>
			</p>
			<p><label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_fulldate'], true); ?> name="campaign_fulldate" value="1" id="campaign_fulldate"/>
					<b><?php _e('Get the Date for the post from the source webpage', 'wpematico'); ?></b></label><br />
				<?php _e('Forces to use the date obtained from the full content if not blank, instead of the current date. This will overwrite the feed item date feature.', 'wpematico'); ?>
			</p>
			<p><label><input onclick="t = jQuery(this);
								if (t.is(':checked'))
									t.parent().parent().next().fadeIn();
								else
									t.parent().parent().next().fadeOut();" class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_fullauthor'], true); ?> name="campaign_fullauthor" value="1" id="campaign_fullauthor"/>
					<b><?php _e('Get the Author from the source webpage', 'wpematico'); ?></b></label><br />
				<?php echo _e('Forces to use the author obtained from the full content. Will checks if the username exist. This will overwrite the author fields in campaign.', 'wpematico'); ?>
			</p>
			<p style="margin-left: 20px; display: <?php echo ($campaign_data['campaign_fullauthor']) ? 'block' : 'none'; ?>;">
				<label><input class="checkbox" type="checkbox"<?php checked($campaign_data['campaign_fccreateauthor'], true); ?> name="campaign_fccreateauthor" value="1" id="campaign_fccreateauthor"/>
					<b><?php _e('Create the Author if not exist.', 'wpematico'); ?></b></label><br />
				<?php echo _e('Creates the user if the username do not exist as Wordpress user. Will be created with the email [username]@[thisdomain].  If not checked follows the campaign rules.', 'wpematico'); ?>
			</p>
			<?php
		}

		/**
		 *  Save campaign data
		 */
		public static function check_campaigndata($campaign_data = array(), $post_data) {
			$campaign_data['campaign_fullcontent']	 = (!isset($post_data['campaign_fullcontent']) || empty($post_data['campaign_fullcontent'])) ? false : ($post_data['campaign_fullcontent'] == 1) ? true : false;
			$campaign_data['campaign_usecurl']		 = (!isset($post_data['campaign_usecurl']) || empty($post_data['campaign_usecurl'])) ? false : ($post_data['campaign_usecurl'] == 1) ? true : false;
			$campaign_data['campaign_ogimage']		 = (!isset($post_data['campaign_ogimage']) || empty($post_data['campaign_ogimage'])) ? false : ($post_data['campaign_ogimage'] == 1) ? true : false;

			$campaign_data['campaign_ogimage_above_content']	 = (!isset($post_data['campaign_ogimage_above_content']) || empty($post_data['campaign_ogimage_above_content'])) ? false : ($post_data['campaign_ogimage_above_content'] == 1) ? true : false;
			$campaign_data['campaign_ogimage_if_not_in_content'] = (!isset($post_data['campaign_ogimage_if_not_in_content']) || empty($post_data['campaign_ogimage_if_not_in_content'])) ? false : ($post_data['campaign_ogimage_if_not_in_content'] == 1) ? true : false;


			$campaign_data['campaign_fullmultipage']	 = (!isset($post_data['campaign_fullmultipage']) || empty($post_data['campaign_fullmultipage'])) ? false : ($post_data['campaign_fullmultipage'] == 1) ? true : false;
			$campaign_data['campaign_fulltitle']		 = (!isset($post_data['campaign_fulltitle']) || empty($post_data['campaign_fulltitle'])) ? false : ($post_data['campaign_fulltitle'] == 1) ? true : false;
			$campaign_data['campaign_fulldate']			 = (!isset($post_data['campaign_fulldate']) || empty($post_data['campaign_fulldate'])) ? false : ($post_data['campaign_fulldate'] == 1) ? true : false;
			$campaign_data['campaign_fullauthor']		 = (!isset($post_data['campaign_fullauthor']) || empty($post_data['campaign_fullauthor'])) ? false : ($post_data['campaign_fullauthor'] == 1) ? true : false;
			$campaign_data['campaign_fccreateauthor']	 = (!isset($post_data['campaign_fccreateauthor']) || empty($post_data['campaign_fccreateauthor'])) ? false : ($post_data['campaign_fccreateauthor'] == 1) ? true : false;
			// **** Return campaign_data
			return $campaign_data;
		}

	}

	endif;
WPeMatico_FullContent_Campaign_Edit::hooks();




