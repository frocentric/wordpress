<?php
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


add_filter('wpematico_help_settings_before', 'wpematico_pro_help_setting', 10, 1);
function wpematico_pro_help_setting($helpsettings) {
	$helpsettings['Global Settings']['clean_audios_urls'] = array( 
						'title' => __('Strip the queries variables in audios URLs', 'wpematico' ),
						'tip' => __('Cleans the queries variables in the same url of the audio before download it. Not recommended unless you have problems to get the files.', 'wpematico' ),
					);
	$helpsettings['Global Settings']['enable_audio_rename'] = array( 
						'title' => __('Enable Audio Renamer', 'wpematico' ),
						'tip' => __('You can write here the new name for every audio uploaded to the posts. All audios will be renamed with this field.', 'wpematico' ),
					);
	$helpsettings['Global Settings']['strip_all_audios'] = array( 
						'title' => __('Strip All Audios from Content', 'wpematico' ),
						'tip' => __('Activate this will deactivate all following options for audios.', 'wpematico' ),
					);

	$helpsettings['Global Settings']['clean_videos_urls'] = array( 
						'title' => __('Strip the queries variables in videos URLs', 'wpematico' ),
						'tip' => __('Cleans the queries variables in the same url of the video before download it. Not recommended unless you have problems to get the files.', 'wpematico' ),
					);
	$helpsettings['Global Settings']['enable_video_rename'] = array( 
						'title' => __('Enable Video Renamer', 'wpematico' ),
						'tip' => __('You can write here the new name for every video uploaded to the posts. All videos will be renamed with this field.', 'wpematico' ),
					);
	$helpsettings['Global Settings']['strip_all_videos'] = array( 
						'title' => __('Strip All Videos from Content', 'wpematico' ),
						'tip' => __('Activate this will deactivate all following options for videos.', 'wpematico' ),
					);

	$helpsettings['Global Settings']['upload_ranges'] = array( 
						'title' => __('Upload by ranges', 'wpematico' ),
						'tip' => __('Use this instead of Wordpress functions to improve performance. This function uploads the file by ranges "as is" from the original to use it inside the post.', 'wpematico' ),
					);
	return $helpsettings;
}

function wpematico_pro_helptips($helptip) {
	return $helptip;
}
function wpematico_pro_help_settings($helpsettings) {
	return $helpsettings;
}

function wpematico_pro_help_settings_rrewrites($helpsettings) {
	
	$helpsettings = '<br>'.__('Complete the Ramdom Rewrites form by adding all comma-separated words, line by line.', 'wpematico' ).'<br>'.
		__('Insert in the form a comma-separated list of words to be used to replace.<br>
			This list will be added to the each campaign Ramdon Rewrite list that has activated the feature.<br>
			The words will be searched line by line and, if one word is found (no matter the order in the line),<br>
			will be replaced for one of the others of the same line.<br>
			', 'wpematico' ).'<br>'.
			__('<b>Example:</b><br>
				<b>lady, woman, female, girl</b><br>
				If the text contains the word "woman" will be replaced by "lady", "female" or "girl" ramdomly.', 'wpematico' );
			
	return $helpsettings;
}

function wpematico_pro_help_campaign($helpsettings) {

			if (empty($helpsettings['XML Campaign Type']['xml_categories_separated_commas'])) {
				$helpsettings['XML Campaign Type']['xml_categories_separated_commas'] = array( 
						'title' => __('Separated by commas', 'wpematico' ),
						'tip' => __('Separated by commas', 'wpematico' ),
					);
			}

			if (empty($helpsettings['Campaign Options']['add_no_follow'])) {
				$helpsettings['Campaign Options']['add_no_follow'] = array( 
						'title' => __('Add rel="nofollow" to links.', 'wpematico' ),
						'tip' => __('This option adds the attribute rel="nofollow" to all the links obtained in the post.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Campaign Options']['fix_google_links'])) {
				$helpsettings['Campaign Options']['fix_google_links'] = array( 
						'title' => __('Sanitize Googlo News permalink.', 'wpematico' ),
						'tip' => __('Sanitize Googlo News permalink.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Tags generation'])) {
				$helpsettings['Tags generation'] = array();
			}
			if (empty($helpsettings['Tags generation']['campaign_autotags'])) {
				$helpsettings['Tags generation']['campaign_autotags'] = array( 
						'title' => __('Auto generate tags', 'wpematico' ),
						'tip' => __('This feature try to get tags automatically from post content.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Tags generation']['campaign_tags_feeds'])) {
				$helpsettings['Tags generation']['campaign_tags_feeds'] = array( 
						'title' => __('Use &lt;tag&gt; tags from feed if exist.', 'wpematico' ),
						'tip' => __('This feature try to get tags automatically from the feed item.', 'wpematico' ),
						'plustip' => __('This feature runs before that parse the content to get the tags from the text.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Tags generation']['campaign_nrotags'])) {
				$helpsettings['Tags generation']['campaign_nrotags'] = array( 
						'title' => __('Limit tags quantity.', 'wpematico' ),
						'tip' => __('Tags with 3 characters or less are ignored.', 'wpematico' ),
					);
			}
			
			if (empty($helpsettings['Tags generation']['campaign_badtags'])) {
				$helpsettings['Tags generation']['campaign_badtags'] = array( 
						'title' => __('Bad Tags.', 'wpematico' ),
						'tip' => __('Enter comma separated list of excluded Tags.', 'wpematico' ),
					);
			}

			if (empty($helpsettings['Custom Parsers']['campaign_delfphrase'])) {
				$helpsettings['Custom Parsers']['campaign_delfphrase'] = array( 
						'title' => __('Delete all in the content AFTER a word or phrase till the end.', 'wpematico' ),
						'tip' => __('<span class="srchbdr0 hide" id="hlpphra" style="display: inline;">
				<b>Basics:</b>	 
				Delete from phrase allows you to strip a a portion of the content from a phrase to the end of the content.<br>
				A phrase or a word by line: The first phrase found in content will be used to strip the rest of the content including the phrase.<br>
				
				<b>Example:</b> If the post content contain the phrase "This post was originally published on xxxxxx..." and then you want to strip this because you don&apos;t want it in your posts, you should use "This post was originally published".				<br>
				<br>
			</span>', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Parsers']['campaign_delfphrase_keep'])) {
				$helpsettings['Custom Parsers']['campaign_delfphrase_keep'] = array( 
						'title' => __('Keep phrase', 'wpematico' ),
						'tip' => __('This option allows you to preserve the phrase found in the post content.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Parsers']['campaign_delfphrase_end_line'])) {
				$helpsettings['Custom Parsers']['campaign_delfphrase_end_line'] = array( 
						'title' => __('Till the end of the line', 'wpematico' ),
						'tip' => __('Delete the phrase till the end of the line instead the end of the content.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Parsers']['flip_paragraphs_title'])) {
				$helpsettings['Custom Parsers']['flip_paragraphs_title'] = array( 
						'title' => __('Flip Paragraphs', 'wpematico' ),
						'tip' => __('Allow flip all paragraphs of the content (&lt;p&gt; tags), each one is replaced by the following and so on until the end of the content.', 'wpematico' ) ."<br />"
						. __('It runs after the above strips, so first deletes what doesn\'t suit and then inverts the paragraphs.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Parsers']['campaign_flip_paragraphs'])) {
				$helpsettings['Custom Parsers']['campaign_flip_paragraphs'] = array( 
						'title' => __('Activate Flip Paragraphs', 'wpematico' ),
						'tip' => __('All new posts fetched by the campaign will flip the first and second paragraph of the content (&lt;p&gt; tags), each one is replaced by the other. And if the third one with the fourth one is presented as well, and so on until the end of the content.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Parsers']['campaign_lastag_tag'])) {
				$helpsettings['Custom Parsers']['campaign_lastag_tag'] = array( 
						'title' => __('Last HTML tag to remove', 'wpematico' ),
						'tip' => __('Finds the tag from right to end of the content and strip it.  Works after Phrase feature above. Keep empty to ignore.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Title Options'])) {
				$helpsettings['Custom Title Options'] = array();
			}
			if (empty($helpsettings['Custom Title Options']['campaign_striptagstitle'])) {
				$helpsettings['Custom Title Options']['campaign_striptagstitle'] = array( 
						'title' => __('Strip HTML Tags From Title', 'wpematico' ),
						'tip' => __('Strip HTML Tags From Title.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Title Options']['campaign_enablecustomtitle'])) {
				$helpsettings['Custom Title Options']['campaign_enablecustomtitle'] = array( 
						'title' => __('Enable Custom Post title', 'wpematico' ),
						'tip' => __('Enable Custom Post title.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Title Options']['campaign_delete_till_ontitle'])) {
				$helpsettings['Custom Title Options']['campaign_delete_till_ontitle'] = array( 
						'title' => __('Enable delete till the end starting from some characters', 'wpematico' ),
						'tip' => __('Remove all characters after some characters or words. Separate all words with space.', 'wpematico' ),
					);
			}

			
			if (empty($helpsettings['Keywords Filters'])) {
				$helpsettings['Keywords Filters'] = array();
			}
			if (empty($helpsettings['Keywords Filters']['skip_posts_with_words'])) {
				$helpsettings['Keywords Filters']['skip_posts_with_words'] = array( 
						'title' => __('Skip posts with words in content or words not in content.', 'wpematico' ),
						'tip' => __('<b>Basics:</b>	 
				The keyword filters allows you to skip a fetched post if it has a word in its content, title or in its source categories. Leaves empty all fields to ignore.<br>
				Must Contain (Any Word): The three fields are checked and a word must be, at least, in one of them.<br>
				Must Contain (All Words): The three fields are checked at once and all the words must be in them.<br>
				
				<b>Example:</b> If the post content contain the word "motor" and then you want skip because you don&apos;t want posts about motors, simply type "motor" in the "Words" field (1 per line).<br>
				<b>Regular Expressions</b><br>
				For advanced users, regular expressions are supported. Using this will allow you to make more powerful filters. Take multiple word filtering for example. Instead of using many rows of words to assign motor and car to Engines, you can use the | operator: (motor|car). If you want Case insensitive on RegEx, add "/i" at the end of RegEx.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Keywords Filters']['skip_posts_with_words_author'])) {
				$helpsettings['Keywords Filters']['skip_posts_with_words_author'] = array( 
						'title' => __('Skip posts with words in author\'s name.', 'wpematico' ),
						'tip' => __('<b>Basics:</b>	 
				This allows you to skip a fetched post if it has a word in its author\'s name. Leaves empty all fields to ignore.<br>
				Must Contain: The author\'s name must have at least one of these words<br>
				
				<b>Example:</b> If the post author\'s name contain the word "jose" and then you want skip because you don&apos;t want jose\'s posts, simply type "jose" in the "Words" field (1 per line).<br>
				<b>Regular Expressions</b><br>
				For advanced users, regular expressions are supported. Using this will allow you to make more powerful filters. Take multiple word filtering for example. Instead of using many rows of words to assign jose and maria, you can use the | operator: (jose|maria). If you want Case insensitive on RegEx, add "/i" at the end of RegEx.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images'])) {
				$helpsettings['PRO Options for Images'] = array();
			}
			if (empty($helpsettings['PRO Options for Images']['clean_images_urls'])) {
				$helpsettings['PRO Options for Images']['clean_images_urls'] = array( 
						'title' => __('Strip the queries variables in images URls', 'wpematico' ),
						'tip' => __('Cleans the queries variables in the same url of the image before download it. Not recommended unless you have problems to get the files.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['image_src_gettype'])) {
				$helpsettings['PRO Options for Images']['image_src_gettype'] = array( 
						'title' => __('Check the source image to determine the extension', 'wpematico' ),
						'tip' => __('Cleans and puts the correct extensions to the images that are delivered through a script, avoiding the WordPress upload error: Extension not allowed. Not Recommended.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['check_image_content'])) {
				$helpsettings['PRO Options for Images']['check_image_content'] = array( 
						'title' => __('Check if image has correct content.', 'wpematico' ),
						'tip' => __('Strip all images that not has a correct content, avoiding upload images that cannot be viewed.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['strip_image_without_content'])) {
				$helpsettings['PRO Options for Images']['strip_image_without_content'] = array( 
						'title' => __('Check if image has correct content.', 'wpematico' ),
						'tip' => __('This option allows you to strip the &#x3C;img&#x3E; tag from the content of the post if it is not an image or a CDN returns HTML code instead of the image. If this option is not checked, the image will not be uploaded but the src will be left to the original site.', 'wpematico' ),
					);
			}

			
			if (empty($helpsettings['PRO Options for Images']['campaign_enableimgrename'])) {
				$helpsettings['PRO Options for Images']['campaign_enableimgrename'] = array( 
						'title' => __('Enable Image Renamer', 'wpematico' ),
						'tip' => __('You can write here the new name for every image uploaded to the posts. All images will be renamed with this field.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['strip_all_images'])) {
				$helpsettings['PRO Options for Images']['strip_all_images'] = array( 
						'title' => __('Strip All Images from Content', 'wpematico' ),
						'tip' => __('Activate this will deactivate all following options for images.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['discardifnoimage'])) {
				$helpsettings['PRO Options for Images']['discardifnoimage'] = array( 
						'title' => __('Discard the Post if NO Images in Content.', 'wpematico' ),
						'tip' => __('The posts without images in content will not be added.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['add1stimg'])) {
				$helpsettings['PRO Options for Images']['add1stimg'] = array( 
						'title' => __('Add featured image at the beginning of the post content.', 'wpematico' ),
						'tip' => __('This feature overwrites the option "Remove featured image from Content" of the General Settings.
', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['filters_by_dimensions_of_images'])) {
				$helpsettings['PRO Options for Images']['filters_by_dimensions_of_images'] = array( 
						'title' => __('Add filters by dimensions of images.', 'wpematico' ),
						'tip' => __('<b>Basics:</b> You can allow or skip each image in every post depends on image dimensions.<br>
						You must select type of filter and fill width or height size in pixels for allow or skip every image.<br>
						Any image that does not comply with a filter will be removed from post content.  (Also its &lt;a href&gt; link, if it has)<br>
						Be careful that the filters are not clogged with each other and miss all images.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Images']['filters_by_dimensions_of_images'])) {
				$helpsettings['PRO Options for Images']['filters_by_dimensions_of_images'] = array( 
						'title' => __('Add filters by dimensions of images.', 'wpematico' ),
						'tip' => __('<b>Basics:</b> You can allow or skip each image in every post depends on image dimensions.<br>
						You must select type of filter and fill width or height size in pixels for allow or skip every image.<br>
						Any image that does not comply with a filter will be removed from post content.  (Also its &lt;a href&gt; link, if it has)<br>
						Be careful that the filters are not clogged with each other and miss all images.', 'wpematico' ),
					);
			}


			/* Audio helps */

			if (empty($helpsettings['PRO Options for Audios'])) {
				$helpsettings['PRO Options for Audios'] = array();
			}
			if (empty($helpsettings['PRO Options for Audios']['clean_audios_urls'])) {
				$helpsettings['PRO Options for Audios']['clean_audios_urls'] = array( 
						'title' => __('Strip the queries variables in audios URLs', 'wpematico' ),
						'tip' => __('Cleans the queries variables in the same url of the audio before download it. Not recommended unless you have problems to get the files.', 'wpematico' ),
					);
			}
			
			if (empty($helpsettings['PRO Options for Audios']['enable_audio_rename'])) {
				$helpsettings['PRO Options for Audios']['enable_audio_rename'] = array( 
						'title' => __('Enable Audio Renamer', 'wpematico' ),
						'tip' => __('You can write here the new name for every audio uploaded to the posts. All audios will be renamed with this field.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Audios']['strip_all_audios'])) {
				$helpsettings['PRO Options for Audios']['strip_all_audios'] = array( 
						'title' => __('Strip All Audios from Content', 'wpematico' ),
						'tip' => __('Activate this will deactivate all following options for audios.', 'wpematico' ),
					);
			}

			if (empty($helpsettings['PRO Options for Audios']['audio_upload_ranges'])) {
				$helpsettings['PRO Options for Audios']['audio_upload_ranges'] = array( 
						'title' => __('Upload by ranges', 'wpematico' ),
						'tip' => __('Use this instead of Wordpress functions to improve performance. This function uploads the audios by ranges "as is" from the original to use it inside the post.', 'wpematico' ),
					);
			}

			if (empty($helpsettings['PRO Options for Audios']['audio_decode_html_ent_url'])) {
				$helpsettings['PRO Options for Audios']['audio_decode_html_ent_url'] = array( 
						'title' => __('Decode html entities in URLs.', 'wpematico' ),
						'tip' => __('Use this option to convert HTML entities to their corresponding characters in the URLs of the audios files.', 'wpematico' ),
					);
			}

			if (empty($helpsettings['PRO Options for Audios']['audio_follow_redirection'])) {
				$helpsettings['PRO Options for Audios']['audio_follow_redirection'] = array( 
						'title' => __('Follow redirections to find the audio file.', 'wpematico' ),
						'tip' => __('Use this option to follow redirections to find the audio file.', 'wpematico' ),
					);
			}



			/* Video helps */

			if (empty($helpsettings['PRO Options for Videos'])) {
				$helpsettings['PRO Options for Videos'] = array();
			}
			if (empty($helpsettings['PRO Options for Videos']['clean_videos_urls'])) {
				$helpsettings['PRO Options for Videos']['clean_videos_urls'] = array( 
						'title' => __('Strip the queries variables in videos URLs', 'wpematico' ),
						'tip' => __('Cleans the queries variables in the same url of the image before download it. Not recommended unless you have problems to get the files.', 'wpematico' ),
					);
			}
			
			if (empty($helpsettings['PRO Options for Videos']['enable_video_rename'])) {
				$helpsettings['PRO Options for Videos']['enable_video_rename'] = array( 
						'title' => __('Enable Video Renamer', 'wpematico' ),
						'tip' => __('You can write here the new name for every video uploaded to the posts. All videos will be renamed with this field.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['PRO Options for Videos']['strip_all_videos'])) {
				$helpsettings['PRO Options for Videos']['strip_all_videos'] = array( 
						'title' => __('Strip All Videos from Content', 'wpematico' ),
						'tip' => __('Activate this will deactivate all following options for videos.', 'wpematico' ),
					);
			}
			
			if (empty($helpsettings['PRO Options for Videos']['video_upload_ranges'])) {
				$helpsettings['PRO Options for Videos']['video_upload_ranges'] = array( 
						'title' => __('Upload by ranges', 'wpematico' ),
						'tip' => __('Use this instead of Wordpress functions to improve performance. This function uploads the video by ranges "as is" from the original to use it inside the post.', 'wpematico' ),
					);
			}

			if (empty($helpsettings['PRO Options for Videos']['video_decode_html_ent_url'])) {
				$helpsettings['PRO Options for Videos']['video_decode_html_ent_url'] = array( 
						'title' => __('Decode html entities in URLs.', 'wpematico' ),
						'tip' => __('Use this option to convert HTML entities to their corresponding characters in the URLs of the videos files.', 'wpematico' ),
					);
			}

			if (empty($helpsettings['PRO Options for Videos']['video_follow_redirection'])) {
				$helpsettings['PRO Options for Videos']['video_follow_redirection'] = array( 
						'title' => __('Follow redirections to find the video file.', 'wpematico' ),
						'tip' => __('Use this option to follow redirections to find the video file.', 'wpematico' ),
					);
			}


			if (empty($helpsettings['Word Count Filters'])) {
				$helpsettings['Word Count Filters'] = array();
			}
			if (empty($helpsettings['Word Count Filters']['Word_Count_Filters'])) {
				$helpsettings['Word Count Filters']['Word_Count_Filters'] = array( 
						'title' => __('This allow you to ignore a post if below X words or letters in content. Also allow assign a category to the post if greater than X words.', 'wpematico' ),
						'tip' => __('<b>Greater than:</b> You must check "Words" field if you want to count words, but the letters in the content are counted; then complete the amount to check to assign a special category.<br>
					<b>Cut at:</b> If the content is bigger than X Words, then the post is converted to text (strip all HTML tags) and cutted at X Words.					 If letters are selected just cut the content at X letters without strip HTML, if is in a middle of html tag, will run a function to close the tags.					First image on content will remain as featured image.<br>
					<b>Less than:</b> If the content is less than X then the post is skipped.<br>
					Leave empty or 0 to ignore.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Custom Fields'])) {
				$helpsettings['Custom Fields'] = array();
			}
			if (empty($helpsettings['Custom Fields']['Custom_Fields'])) {
				$tags_array = array();
				$tags_array[] = '{title}';
				$tags_array[] = '{content}';
				$tags_array[] = '{itemcontent}';
				$tags_array[] = '{image}';
				$tags_array[] = '{author}';
				$tags_array[] = '{authorlink}';
				$tags_array[] = '{permalink}';
				$tags_array[] = '{feedurl}';
				$tags_array[] = '{feedtitle}';
				$tags_array[] = '{feeddescription}';
				$tags_array[] = '{feedlogo}';
				$tags_array[] = '{campaigntitle}';
				$tags_array[] = '{campaignid}';
				$tags_array[] = '{item_date}';
				$tags_array[] = '{item_time}';
				$tags_on_campaign_edit = apply_filters('wpematico_pro_template_tags_cf_help', $tags_array);
				$template_tags_html = '';
				foreach ($tags_on_campaign_edit as $tag) {
					$template_tags_html .= '<span class="tagcf">'.$tag.'</span>';
					$array_sliced = (array_slice($tags_on_campaign_edit, -1));
					$lastEl = array_pop($array_sliced);
					if ($tag != $lastEl) {
						$template_tags_html .= ', ';
					} 
				}
				$helpsettings['Custom Fields']['Custom_Fields'] = array( 
						'title' => __('Add custom fields with values as templates.', 'wpematico' ),
						'tip' => sprintf(__('<b>Basics:</b> You must put the custom field name and its value in every field.<br>
				<b>Value:</b>
				You can use the same template fields like in box Post template.<br>
				<b>Valid value tags:</b>%s.', 'wpematico' ),$template_tags_html), 
					);
			}

			if (empty($helpsettings['Custom Feed Tags'])) {
				$helpsettings['Custom Feed Tags'] = array();
			}
			if (empty($helpsettings['Custom Feed Tags']['Custom_Feed_Tags'])) {
				
				$helpsettings['Custom Feed Tags']['Custom_Feed_Tags'] = array( 
						'title' => __('Add custom feed tags to use on template or custom fields.', 'wpematico' ),
						'tip' => sprintf(__('<b>Basics:</b> You must put the custom feed tag and its template variable in every field.<br>
				<b>Feed Tag:</b> If exist a tag &#x3C;yourtag&#x3E;value&#x3C;/yourtag&#x3E; you should put <strong>yourtag</strong><br>
				<b>Template:</b> <strong>{yourtag}</strong>.', 'wpematico' ),$template_tags_html), 
					);
			}
			if (empty($helpsettings['Feeds for this Campaign'])) {
				$helpsettings['Feeds for this Campaign'] = array();
			}
			if (empty($helpsettings['Feeds for this Campaign']['import_feed_list'])) {
				$helpsettings['Feeds for this Campaign']['import_feed_list'] = array( 
						'title' => __('Import feed list.', 'wpematico' ),
						'tip' => __('The list must be one feed URL per line.  You can add an author username if the feature "Author per Feed" is enabled on PRO Settings. If not exist, the users will be added to wordpress users. Otherwise take the campaign author. 
							<br />www.yourfeed.com/feed, admin<br />www.otherfeed.com/rss, visituser<br />www.thirthfeed.com/atom, wpuser<br />
							', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Ramdom Rewrites'])) {
				$helpsettings['Ramdom Rewrites'] = array();
			}
			if (empty($helpsettings['Ramdom Rewrites']['activate_ramdom_rewrite'])) {
				$helpsettings['Ramdom Rewrites']['activate_ramdom_rewrite'] = array( 
						'title' => __('Activate Ramdom Rewrites.', 'wpematico' ),
						'tip' => __('Activate Ramdom Rewrites. Will show the form to allow add all the lines with comma-separated words to be used.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Ramdom Rewrites']['ramdom_rewrite_count'])) {
				$helpsettings['Ramdom Rewrites']['ramdom_rewrite_count'] = array( 
						'title' => __('Number of maximum words to replace.', 'wpematico' ),
						'tip' => __('Maximum number of words to be replaced in the content of each post.', 'wpematico' ),
					);
			}
			if (empty($helpsettings['Ramdom Rewrites']['words_to_rewrites'])) {
				$helpsettings['Ramdom Rewrites']['words_to_rewrites'] = array( 
						'title' => __('Words to Rewrite.', 'wpematico' ),
						'tip' => __('Insert in the form a comma-separated list of words to be used to replace.<br>
							This list will be added to the general Ramdon Rewrite list from the Settings screen.<br>
							The words will be searched line by line and, if one word is found (no matter the order in the line),<br>
							will be replaced for one of the others of the same line.<br>
							', 'wpematico' ),
						'plustip' => __('
							<b>Example:</b><br>
							<b>lady, woman, female, girl</b><br>
							If the text contains the word "woman" will be replaced by "lady", "female" or "girl" ramdomly.', 'wpematico' ),
					);
			}

			return $helpsettings;
		}
		