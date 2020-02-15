<?php
// don't load directly 
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/**
 * WPeMatico Pro Extra Settings Class 
 * This class is used to add the Professional Extra Settings 
 * @since 2.2
 */
if(!class_exists('WPeMaticoPro_ExtraSettings')) :

	class WPeMaticoPro_ExtraSettings {

		public static $current_options = null;

		const RAMDOM_REWRITES_OPTION	 = 'WPeMaticoPRO_ramdom_rewrites';
		const WORDS_TO_TAX_OPTION		 = 'WPeMaticoPRO_word_to_taxonomy';

		public static function hooks() {
			if(is_null(self::$current_options)) {
				self::$current_options = get_option(WPeMaticoPRO::OPTION_KEY);
			}

			if(!empty(self::$current_options['enable_ramdom_words_rewrites'])) {
				add_filter('wpematico_get_prosettings_sections', array(__CLASS__, 'ramdom_rewrites'));
				add_action('wpematico_settings_section_ramdom_rewrites', array(__CLASS__, 'ramdom_rewrites_form'));
				add_action('admin_post_save_wpe_pro_ramdom_rewrites', array(__CLASS__, 'save_ramdom_rewrites_form'));
				add_action('admin_init', array(__CLASS__, 'help_ramdom_rewrites_form'));
			}

			if(!empty(self::$current_options['enable_word_to_taxonomy'])) {
				add_filter('wpematico_get_prosettings_sections', array(__CLASS__, 'word_to_taxonomy_section'));
				add_action('wpematico_settings_section_word_to_taxonomy', array(__CLASS__, 'word_to_taxonomy_form'));
				add_action('admin_post_save_wpe_pro_word_to_taxonomy', array(__CLASS__, 'save_word_to_taxonomy_form'));
				add_action('admin_init', array(__CLASS__, 'help_word_to_taxonomy_form'));
				add_action('wp_ajax_wpepro_word2tax_terms', array(__CLASS__, 'word_to_taxonomy_ajax_tax'));
			}
			add_filter('wpematico_settings_tabs', array('WPeMaticoPro_Settings', 'prosettings_tabs'));
			add_action('wpematico_settings_section_settings', array('WPeMaticoPro_Settings', 'pro_settings_page'), 0, 5);
			add_action('wpematico_save_prosettings', array('WPeMaticoPro_Settings', 'prosettings_save'));

			add_action('admin_print_scripts', array(__CLASS__, 'scripts'));
			add_action('admin_print_styles', array(__CLASS__, 'styles'));
		}

		public static function scripts() {

			if(isset($_GET['tab']) && $_GET['tab'] == 'prosettings') { // Only print the JS file on settings page.
				if(isset($_GET['section']) && $_GET['section'] == 'word_to_taxonomy') { // Only print the JS file on section page.
					wp_enqueue_script('wp-util');
					wp_enqueue_script('wpepro-word-tax-page', WPeMaticoPRO::$uri . 'assets/js/section_word2tax.js', array('jquery'), WPEMATICOPRO_VERSION, true);
					wp_enqueue_style('wpepro-word-tax-css', WPeMaticoPRO::$uri . 'assets/css/wordtotax.css', array(), WPEMATICOPRO_VERSION);

					$args			 = array(
						'public' => true
					);
					$output			 = 'names'; // names or objects, note names is the default
					$output			 = 'objects'; // names or objects, note names is the default
					$operator		 = 'and'; // 'and' or 'or'
//					$postTypes	 = get_post_types($args, $output, $operator);
					
					$postTypesObj	 = get_post_types($args, $output, $operator);


					$word2tax_obj						 = array();
					$word2tax_obj['text_select_tax']	 = __('Select a taxonmy', 'wpematico');
					$word2tax_obj['text_select_term']	 = __('Select a term', 'wpematico');
					$word2tax_obj['post_types_tax']		 = array();
					$word2tax_obj['post_types_terms']	 = array();
					$word2tax_obj['get_terms_nonce']	 = wp_create_nonce('wpepro-word-tax-terms-nonce');

					foreach($postTypesObj as $postType=>$ptobj) {
						$word2tax_obj['post_types_tax'][$postType] = get_object_taxonomies($postType);
						$postTypesArr[$postType]= $ptobj->labels->singular_name;
					}
					$word2tax_obj['post_types']			 = $postTypesArr;

					wp_localize_script('wpepro-word-tax-page', 'wpepro_word2tax_obj', $word2tax_obj);
				}
			}
		}

		/**
		 * Static function styles
		 * @access public
		 * @return void
		 * @since 1.8.0
		 */
		public static function styles() {
			if(isset($_GET['tab']) && $_GET['tab'] == 'debug_info') { // Only print the CSS file on settings page.
				if(isset($_GET['section']) && $_GET['section'] == 'word_to_taxonomy') { // Only print the CSS file on section page.
				}
			}
		}

		public static function ramdom_rewrites($sections) {
			$sections['ramdom_rewrites'] = __('Ramdom Rewrites', 'wpematico');
			return $sections;
		}

		public static function default_ramdom_rewrites_options($never_set = FALSE) {
			$default_options = array(
				'words_to_rewrites' => '',
			);

			return $default_options;
		}

		public static function help_ramdom_rewrites_form() {
			if(( isset($_GET['page']) && $_GET['page'] == 'wpematico_settings' ) &&
				( isset($_GET['post_type']) && $_GET['post_type'] == 'wpematico' ) &&
				( isset($_GET['tab']) && $_GET['tab'] == 'prosettings' ) &&
				( isset($_GET['section']) && $_GET['section'] == 'ramdom_rewrites' )) {

				$screen = WP_Screen::get('wpematico_page_wpematico_settings ');

				$helpcontent = apply_filters('wpematico_help_settings_rrewrites', '');

				$screen->add_help_tab(array(
					'id'		 => 'ramdomrewrites',
					'title'		 => 'Ramdom Rewrites',
					'content'	 => $helpcontent,
				));
			}
		}

		public static function ramdom_rewrites_form() {
			$ramdom_rewrites_options = get_option(self::RAMDOM_REWRITES_OPTION);
			$ramdom_rewrites_options = wp_parse_args($ramdom_rewrites_options, self::default_ramdom_rewrites_options(FALSE));
			?>
			<div class="wrap">

				<h3><?php _e('Ramdom Rewrites Settings', WPeMaticoPRO :: TEXTDOMAIN); ?></h3>			
				<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
					<?php wp_nonce_field('save_wpe_pro_ramdom_rewrites'); ?>
					<input type="hidden" name="action" value="save_wpe_pro_ramdom_rewrites"/>
					<table id="general-options" class="form-table">

						<b><label for="words_to_rewrites"><?php _e('Words to Rewrites:', WPeMaticoPRO :: TEXTDOMAIN); ?> <span class="dashicons dashicons-warning help_tip" title="<?php _e('See tips in Help tab above right.', WPeMaticoPRO :: TEXTDOMAIN); ?>"></span></label></b><br>
						<textarea style="width: 90%;min-height: 200px;" id="words_to_rewrites" name="words_to_rewrites"><?php echo $ramdom_rewrites_options['words_to_rewrites']; ?></textarea><br>

						<?php _e('Enter a comma-separated list of words for rewrites use each line for different rewriting patterns.', WPeMaticoPRO :: TEXTDOMAIN); ?>

					</table>	
					<?php submit_button(); ?>				
				</form>

			</div>
			<?php
		}

		public static function save_ramdom_rewrites_form() {
			if(!wp_verify_nonce($_POST['_wpnonce'], 'save_wpe_pro_ramdom_rewrites')) {
				wp_die(__('Security check', WPeMaticoPRO :: TEXTDOMAIN));
			}
			update_option(self::RAMDOM_REWRITES_OPTION, $_POST);
			WPeMatico::add_wp_notice(array('text' => __('Settings saved.', WPeMaticoPRO :: TEXTDOMAIN), 'below-h2' => false));
			wp_redirect($_POST['_wp_http_referer']);
			exit;
		}

		/**
		 * ******************************************************************************
		 * word_to_taxonomy_section added since 2.2
		 */
		public static function word_to_taxonomy_section($sections) {
			$sections['word_to_taxonomy'] = __('Word to Taxonomy', 'wpematico');
			return $sections;
		}

		public static function default_word_to_taxonomy_options($never_set = FALSE) {
			$default_options = array(
				'word'	 => array(''),
				'title'	 => array(false),
				'regex'	 => array(false),
				'cases'	 => array(false),
				'post'	 => array('post'),
				'tax'	 => array('-1'),
				'term'	 => array('-1')
			);

			return $default_options;
		}

		public static function help_word_to_taxonomy_form() {
			if(( isset($_GET['page']) && $_GET['page'] == 'wpematico_settings' ) &&
				( isset($_GET['post_type']) && $_GET['post_type'] == 'wpematico' ) &&
				( isset($_GET['tab']) && $_GET['tab'] == 'prosettings' ) &&
				( isset($_GET['section']) && $_GET['section'] == 'word_to_taxonomy' )) {

				$screen = WP_Screen::get('wpematico_page_wpematico_settings ');

				$helpcontent = apply_filters('wpematico_help_settings_word2tax', '');

				$screen->add_help_tab(array(
					'id'		 => 'word2tax',
					'title'		 => 'Word To Taxonomies',
					'content'	 => $helpcontent,
				));
			}
		}

		public static function word_to_taxonomy_ajax_tax() {
			if(!wp_verify_nonce($_POST['nonce'], 'wpepro-word-tax-terms-nonce')) {
				wp_send_json_error(array('message' => __('Security check.', WPeMaticoPRO::TEXTDOMAIN)));
			}
			$tax = (!empty($_POST['tax']) ? $_POST['tax'] : '');
			$tax = sanitize_text_field($tax);
			if(empty($tax)) {
				wp_send_json_error(array('message' => __('A error has been ocurred.', WPeMaticoPRO::TEXTDOMAIN)));
			}


			$terms = get_terms(array(
				'taxonomy'	 => $tax,
				'hide_empty' => false,
			));
			wp_send_json_success($terms);
		}

		public static function word_to_taxonomy_form() {
			$word_to_taxonomy_options	 = get_option(self::WORDS_TO_TAX_OPTION);
			$word_to_taxonomy_options	 = wp_parse_args($word_to_taxonomy_options, self::default_word_to_taxonomy_options(FALSE));
			?>
			<div class="wrap">
				<div class="meta-box-word2tax-custom">
					<h3><?php _e('Word to Taxonomy', WPeMaticoPRO :: TEXTDOMAIN); ?></h3>	

					<script type="text/html" id="tmpl-word-to-taxonomy-entity">

						<div id="w2t_ID_{{data.ID}}" class="row_word_to_tax">
							<div class="pDiv jobtype-select p7">
								<div id="w1">
									<label><?php _e('Word:', 'wpematico'); ?> <input type="text" size="25" class="regular-text" id="section_word2tax_word_{{data.ID}}" name="section_word2tax[word][{{data.ID}}]" value="{{data.word_value}}"></label><br>
									<label><input name="section_word2tax[title][{{data.ID}}]" id="section_word2tax_title_{{data.ID}}" class="checkbox w2ctitle" value="1" type="checkbox" <# if ( data.ontitle ) { #> checked="checked" <# } #> ><?php _e('on Title', 'wpematico'); ?>&nbsp;&nbsp;</label>
									<label><input name="section_word2tax[regex][{{data.ID}}]" id="section_word2tax_regex_{{data.ID}}" class="checkbox w2cregex" value="1" type="checkbox" <# if ( data.onregex ) { #> checked="checked" <# } #> ><?php _e('RegEx', 'wpematico'); ?>&nbsp;&nbsp;</label>
									<label><input name="section_word2tax[cases][{{data.ID}}]" id="section_word2tax_cases_{{data.ID}}" class="checkbox w2ccases" value="1" type="checkbox" <# if ( data.oncases ) { #> checked="checked" <# } #> ><?php _e('Case sensitive', 'wpematico'); ?>&nbsp;&nbsp;</label>
								</div>
								<div id="c1">
									<select name="section_word2tax[post][{{data.ID}}]" id="section_word2tax_post_{{data.ID}}" class="form-no-clear word2tax_post">
										<option value="-1"><?php _e('Select a post type', 'wpematico'); ?></option>
										{{{data.options_select_post}}}
									</select>
									<select name="section_word2tax[tax][{{data.ID}}]" id="section_word2tax_tax_{{data.ID}}" class="form-no-clear word2tax_tax">
										<option value="-1"><?php _e('Select a taxonomy', 'wpematico'); ?></option>
										{{{data.options_select_tax}}}
									</select>
									<select style="width: 150px;" name="section_word2tax[term][{{data.ID}}]" id="section_word2tax_term_{{data.ID}}" class="form-no-clear">
										{{{data.options_select_term}}}
									</select>
								</div>
								<span class="wi10" id="w2cactions">
									<label title="Delete this item" class="bicon delete left btn_delete_w2t"></label>
								</span>
							</div>
						</div>

						</script>		
						<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
							<?php wp_nonce_field('save_wpe_pro_word_to_taxonomy'); ?>
							<input type="hidden" name="action" value="save_wpe_pro_word_to_taxonomy"/>
							<div id="container_word_to_taxonomy">
							</div>
							<div id="paging-box-word2tax">
								<a href="#" class="button-primary add" id="addmorerew-word2tax"><?php _e('Add more.', WPeMaticoPRO::TEXTDOMAIN); ?></a>
							</div>
							<?php submit_button(); ?>				
						</form>
						<script type="text/javascript">

							jQuery(document).ready(function ($) {

								//wpepro_update_taxonomy_id(jQuery);

								<?php foreach($word_to_taxonomy_options['word'] as $key => $val) : ?>
									section_word2tax_add_new_input_group(<?php var_export($word_to_taxonomy_options['word'][$key]); ?>, <?php var_export($word_to_taxonomy_options['title'][$key]); ?>, <?php var_export($word_to_taxonomy_options['regex'][$key]) ?>, <?php var_export($word_to_taxonomy_options['cases'][$key]) ?>, <?php var_export($word_to_taxonomy_options['post'][$key]); ?>, <?php var_export($word_to_taxonomy_options['tax'][$key]); ?>, <?php echo $word_to_taxonomy_options['term'][$key]; ?>);

								<?php endforeach; ?>


								jQuery('#addmorerew-word2tax').click(function (e) {
									section_word2tax_add_new_input_group();
									section_word2tax_events_rows();
									e.preventDefault();
								});

								section_word2tax_events_rows();
							});

						</script>
					</div>
				</div>
				<?php
			}

			public static function save_word_to_taxonomy_form() {
				if(!wp_verify_nonce($_POST['_wpnonce'], 'save_wpe_pro_word_to_taxonomy')) {
					wp_die(__('Security check', WPeMaticoPRO :: TEXTDOMAIN));
				}
				$new_options			 = array();
				$new_options['word']	 = array();
				$new_options['title']	 = array();
				$new_options['regex']	 = array();
				$new_options['cases']	 = array();
				$new_options['post']	 = array();
				$new_options['term']	 = array();

				if(!empty($_POST['section_word2tax'])) {

					if(!empty($_POST['section_word2tax']['word'])) {

						foreach($_POST['section_word2tax']['word'] as $id => $value) {

							$word	 = ($_POST['section_word2tax']['word'][$id]);
							$title	 = (isset($_POST['section_word2tax']['title'][$id]) && $_POST['section_word2tax']['title'][$id] == 1) ? true : false;
							$regex	 = (isset($_POST['section_word2tax']['regex'][$id]) && $_POST['section_word2tax']['regex'][$id] == 1) ? true : false;
							$cases	 = (isset($_POST['section_word2tax']['cases'][$id]) && $_POST['section_word2tax']['cases'][$id] == 1) ? true : false;
							$post	 = (isset($_POST['section_word2tax']['post'][$id]) && !empty($_POST['section_word2tax']['post'][$id]) ) ? $_POST['section_word2tax']['post'][$id] : '';
							$tax	 = (isset($_POST['section_word2tax']['tax'][$id]) && !empty($_POST['section_word2tax']['tax'][$id]) ) ? $_POST['section_word2tax']['tax'][$id] : '';
							$term	 = (isset($_POST['section_word2tax']['term'][$id]) && !empty($_POST['section_word2tax']['term'][$id]) ) ? $_POST['section_word2tax']['term'][$id] : '';


							if(!empty($word)) {
								$new_options['word'][]	 = ($regex) ? $word : htmlspecialchars($word);
								$new_options['title'][]	 = $title;
								$new_options['regex'][]	 = $regex;
								$new_options['cases'][]	 = $cases;
								$new_options['post'][]	 = $post;
								$new_options['tax'][]	 = $tax;
								$new_options['term'][]	 = $term;
							}
						}
					}
				}
				//Output: sort the array by post(type) and then by word.
				$ar = $new_options;
				array_multisort($ar['post'], SORT_ASC, SORT_STRING,
					$ar['word'], SORT_ASC, SORT_STRING,
					$ar['tax'], SORT_ASC, SORT_STRING,
					$ar['term'], SORT_ASC, SORT_STRING,
					$ar['title'], SORT_NUMERIC, SORT_DESC,
					$ar['regex'], SORT_NUMERIC, SORT_DESC,
					$ar['cases'], SORT_NUMERIC, SORT_DESC
				);
				update_option(self::WORDS_TO_TAX_OPTION, $ar);
				WPeMatico::add_wp_notice(array('text' => __('Settings saved.', WPeMaticoPRO :: TEXTDOMAIN), 'below-h2' => false));
				wp_redirect($_POST['_wp_http_referer']);
				exit;
			}

		}

		endif;
	WPeMaticoPro_ExtraSettings::hooks();
