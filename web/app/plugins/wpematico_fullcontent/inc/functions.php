<?php
/**
 *  @package WPeMatico Full Content
 * */
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/**
 * wpematico_load_custom_txt
 *
 * @since 2.4
 * @return void
 */
function wpematico_load_custom_txt() {
	$data = $_POST['data'];
	if(!wp_verify_nonce($_POST['_wpnonce'], 'nonce-' . basename($data))) {
		wp_send_json_error(array('error' => true, 'message' => __('File not found.', 'wpematico')));
	}
	if(is_writable($data)) {
		//$content = @WPeMatico::wpematico_get_contents($data, false);
		$content = wpematico_fullcontent_filereader($data);
		if(!$content)
			wp_send_json_error(array('error' => true, 'message' => __('Export location or file not readable', 'wpematico')));
		else {
			$ret = array('pathfilename' => $data, 'filename' => basename($data), 'textfile' => $content,);
			wp_send_json_success($ret);
		}
	}else {
		wp_send_json_error(array('error' => true, 'message' => __(sprintf('ERROR: File "%s" not writeable.', basename($data)), 'wpematico')));
	}
}

add_action('wp_ajax_wpematico_load_custom_txt', 'wpematico_load_custom_txt');

class wpematico_falseitem {

	public $url;

	function __construct($url) {
		$this->url = $url;
	}

	function get_permalink() {
		return $this->url;
	}

	function get_title() {
		return '';
	}

}

class extratests {

	function getthecontent($url = null) {
		if(is_null($url))
			return 'ERROR: url';
		$current_item							 = array('content' => '', 'title' => '', 'date' => '', 'author' => '');
		$item									 = new wpematico_falseitem($url);
		$campaign['campaign_usecurl']			 = true;
		$campaign['avoid_search_redirection']	 = true;  //test_url must be complete and no redirection
		$campaign['campaign_striphtml']			 = false;

		$campaign['campaign_fullmultipage']	 = true;
		$campaign['campaign_fulltitle']		 = true;
		$campaign['campaign_fulldate']		 = true;
		$campaign['campaign_fullauthor']	 = true;
		$campaign['campaign_fullcontent']	 = true;
		$feed								 = '';
		add_filter('wpepro_getfullcontent', 'wpemfullcontent_getcontent', 10, 3);

		error_reporting(E_ERROR | E_PARSE);

		$html					 = wpemfullcontent_GetFullContent($current_item, $campaign, $feed, $item, true);
		$meta					 = '<div style="float:right; border: 1px solid black;margin: 0 0 10px;padding: 5px;">';
		$meta					 .= '<span> <b>Title: </b>' . $current_item['title'] . '</span><br>';
		$meta					 .= '<span> <b>Date: </b>' . date_i18n(get_option('date_format'), $current_item['date']) . '</span><br>';
		$meta					 .= '<span> <b>Author: </b>' . $current_item['author'] . '</span><br>';
		$meta					 .= '</div>';
		$current_item['content'] = $html;
		return $meta . $html;
	}

}

// class

function wpematico_test_fullcontent() {
	global $wpefull_meta_tags_templates;

	if(!wp_verify_nonce($_POST['_wpnonce'], 'wpematicopro-fullcontent')) {
		wp_send_json_error(array('error' => true, 'message' => __('Denied.', 'wpematico')));
	}
	check_admin_referer('wpematicopro-fullcontent');
	$url = esc_url(trim($_POST['test_url']));

//	error_reporting(-1);
//	ini_set('display_errors', '1');

	$test	 = new extratests();
	$html	 = $test->getthecontent($url);

	$html_meta_tags = '';
	foreach($wpefull_meta_tags_templates as $template_var => $value) {
		$html_meta_tags .= '<p class="wfc_meta_row"><span class="wfc_tag">' . $template_var . '</span> = ' . $value . '</p>';
	}

	if(!is_null($html)) {
		if(!is_string($html))
			wp_send_json_error(array('error' => true, 'message' => __('There is no html in content.', 'wpematico')));
		else {
			$ret = array('htmlcontent' => $html, 'html_meta_tags' => $html_meta_tags);
			wp_send_json_success($ret);
		}
	}else {
		wp_send_json_error(array(
			'error'		 => true,
			'message'	 => __(sprintf('ERROR: Cannot read "%s" content.', "test_url"), 'wpematico') . '  ' .
			__('You can try with "autodetect_on_failure: yes" or search another @class/@id that envelopes the content.', 'wpematico') . '<br>' .
			__('A good tip for this is use Firebug or Right Click -> "Inspect Element" on source URL.', 'wpematico')
		));
	}
}

add_action('wp_ajax_wpematico_test_fullcontent', 'wpematico_test_fullcontent');


add_action('wp_ajax_wpematico_movetouploads_fullcontent', 'wpematico_movetouploads_fullcontent');

function wpematico_movetouploads_fullcontent() {
	if(!wp_verify_nonce($_POST['_wpnonce'], 'wpematicopro-fullcontent')) {
		wp_send_json_error(array('error' => true, 'message' => __('File not found.', 'wpematico')));
	}
	check_admin_referer('wpematicopro-fullcontent');
	$src = wpematico_fullcontent_foldercreator();
	$ret = wpematico_fullcontent_foldercreator(true);
	if(!is_dir(wpematico_fullcontent_foldercreator(false))) {
		wp_send_json_error(array('error' => true, 'message' => __('There was an error creating directory.', 'wpematico')));
	}
	//copy_dir($src, $ret);
	$files		 = wscandir($src);
	$someerror	 = false;
	foreach($files as $f) {
		if(is_dir($f)) {  //don't shows directories
		}elseif(!in_array(str_replace('.', '', strrchr($f, '.')), explode(',', 'txt'))) { //allowed extensions
		}else {
			$copy = copy($src . $f, $ret . $f);
			if(!$copy) {
				$someerror = true;
			}
		}
	}
	if($someerror) {
		wp_send_json_error(array('error' => true, 'message' => __('There was an error copying files.', 'wpematico')));
	}else {
		if($files === FALSE) {
			wp_send_json_error(array('error' => true, 'message' => __('Can\'t copy files!', 'wpematico')));
		}
	}
	//reload the page on return;
	wp_send_json_success(array('success' => true));
}

function wscandir($cwdir) {
	if(function_exists("scandir")) {
		return scandir($cwdir);
	}else {
		$cwdh		 = opendir($cwdir);
		while (false !== ($filename	 = readdir($cwdh)))
			$files[]	 = $filename;
		return $files;
	}
}

function get_current_os() {
	$current_os = '';
	if(strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
		$current_os = 'win';
	}else {
		$current_os = 'nix';
	}
	return $current_os;
}

add_action('admin_post_full_filelist_upload_action', 'full_filelist_upload_action');

function full_filelist_upload_action() {
	if(!wp_verify_nonce($_POST['full_wpnonce'], 'full_filelist_upload')) {
		wp_die(__('Security check', 'wpematico'));
	}
	$full_path_custom = wpematico_fullcontent_folder();

	if(in_array(str_replace('.', '', strrchr($_FILES['f']['name'], '.')), explode(',', 'txt'))) {
		if(!@move_uploaded_file($_FILES['f']['tmp_name'], $full_path_custom . $_FILES['f']['name'])) {
			WPeMatico::add_wp_notice(array('text' => __("** Can't upload!", 'wpematico'), 'below-h2' => false, 'error' => true));
		}
	}else {
		WPeMatico::add_wp_notice(array('text' => __("** Can't upload! Just .txt files allowed!", 'wpematico'), 'below-h2' => false, 'error' => true));
	}
	wp_redirect($_POST['_wp_http_referer']);
	exit;
}

function wpefullcontent_folder_notice($param) {
	?><div class="notice notice-error is-dismissible"><p><?php
			_e('Your Custom folder stil remains inside own plugin directory. This means that your files will be replaced/deleted when update the plugin.');
			echo "<br />";
			_e('It\'s strongly recommended that you move your files to Wordpress uploads directory to don\'t loose your files later.');
			?></div></p><?php
}

function fullcontent_is_folder_exist() {
	if(is_dir(wpematico_fullcontent_foldercreator(false)))
		return true;
	else
		return false;
}

//wpematico_fullcontent_folder
function wpematico_fullcontent_folder() {
	$dir = plugin_dir_path(__FILE__) . 'content-extractor/config/custom/';
	if(!fullcontent_is_folder_exist(false)) {
		
	}else {
		$dir = wpematico_fullcontent_foldercreator(false);
	}
	return $dir;
}

/**
 * @param string|bool $customdir Optional: If 'true' return new dir in uploads folder.  If not exist create and copy all files in source dir.<br>
 * 						If 'false' return new dir in uploads folder, don't create if not exist.<br>
 * 						If string return trailingslashit $customdir as is. <br>
 * 						if null or !given return plugin original custom dir
 * @return string directory with trailingslash of custom txt config files for remote content extractor
 */
function wpematico_fullcontent_foldercreator($customdir = null) {
	$upload_dir	 = wp_upload_dir();
	$path_dst	 = '/inc/content-extractor/config/custom/';
	$src		 = trailingslashit(ABSPATH . PLUGINDIR) . 'wpematico_fullcontent' . $path_dst;
	$new_dst	 = 'wpematicopro/config/custom/';
	//	$config_dst_url = trailingslashit($upload_dir['url']). $path_dst;
	if(is_string($customdir))
		$ret		 = trailingslashit($customdir);
	elseif(is_null($customdir))
		$ret		 = $src;
	elseif(is_bool($customdir))
		$ret		 = trailingslashit($upload_dir['basedir']) . $new_dst;

	if(!is_dir($ret) && $customdir == true) {
		$parts	 = explode('/', $ret);
		$file	 = array_pop($parts);
		if(!is_dir($ret)) {
			@mkdir($ret, 0777, true);
		}
		//$dir = '';
		//foreach($parts as $part)   // don't use recursive creation to avoid php warnings
		//if(!is_dir($dir .= "/$part")) mkdir($dir,0777);  
	}
	return $ret;
}

function wpematico_filelist() {

	$current_os = get_current_os();

	$full_path_home = plugin_dir_path(__FILE__) . 'content-extractor/config/custom/';

	$full_path_custom = wpematico_fullcontent_folder();
	chdir($full_path_custom);

	echo $current_os . ' ' . __('Path', 'wpematico') . ':' . htmlspecialchars($full_path_custom);
	echo '<input type="hidden" name="c" value="' . htmlspecialchars($full_path_custom) . '"/><hr/>';

	if(!is_writable($full_path_custom)) {
		echo '<font color=red>' . __('(Not writable)', 'wpematico') . '</font><br/>';
	}

	$ls = wscandir($full_path_custom);
	foreach($ls as $f) {
		if(is_dir($f)) {  //don't shows directories
		}elseif(!in_array(str_replace('.', '', strrchr($f, '.')), explode(',', 'txt'))) { //allowed extensions
		}else {
			echo "<a class='fileonlist' href=# nonce='" . wp_create_nonce('nonce-' . $f) . "' data='" . $full_path_custom . $f . "'>";
			if(is_writable($full_path_custom . $f)) {
				echo "<font title='" . __("Click to edit", 'wpematico') . "'  color=green>" . $f . "</font>";
			}else {
				echo "<font title='" . __("(Not writable)", 'wpematico') . "' color=#999>" . $f . "</font>";
			}
			echo "</a><br />";
		}
	}

	echo '<hr>
		<form method="post" enctype="multipart/form-data" action="' . admin_url('admin-post.php') . '">
			<input type="hidden" name="c" value="' . $full_path_custom . '"/>
			<input type="hidden" name="action" value="full_filelist_upload_action"/>';
	wp_nonce_field('full_filelist_upload', 'full_wpnonce');
	echo 'Upload .txt file: <div class="upload-file"><label for="upload-config-file">Browse...</label><span id="config-file-name"></span><input type="file" id="upload-config-file" name="f" style="overflow: hidden;"/></div>
	<button class="button button-primary btn-upload" type="submit"><span class="dashicons dashicons-upload"></span></button></form>';
}

function wpematico_fullcontent_filereader($filepath) {
	global $wp_filesystem;
	/* checks if exists $wp_filesystem */
	if(empty($wp_filesystem) || !isset($GLOBALS['wp_filesystem']) || !is_object($GLOBALS['wp_filesystem'])) {

		if(file_exists(ABSPATH . '/wp-admin/includes/file.php')) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}
		$upload_dir	 = wp_upload_dir();
		$context	 = trailingslashit($upload_dir['path']); /* Used by request_filesystem_credentials to verify the folder permissions if it needs credentials. */

		ob_start();
		$creds = request_filesystem_credentials('edit.php?post_type=wpematico', '', false, $context);
		ob_end_clean();

		if($creds === false) {
			return false;
		}
		$init = WP_Filesystem($creds, $context);
		if(!$init)
			return false;
	}

	$file_content	 = '';
	// $wp_filesystem->get_contents in 'direct' method allows url downloads, other methods should work only on local files
//	if(defined('FS_METHOD')) {
	$file_content = $wp_filesystem->get_contents($filepath);
//	}
	return $file_content;
}

add_filter('wpematico_sysinfo_after_wpematico_config', 'full_debug_data', 4);

function full_debug_data($return) {
	// WPeMatico Full Content configuration

	$return	 .= "\n" . '-- WPeMatico Full Content Configuration' . "\n\n";
	$return	 .= 'Version:                  ' . WPEFULLCONTENT_VERSION . "\n";

	$plugins_args		 = array();
	$plugins_args		 = apply_filters('wpematico_plugins_updater_args', $plugins_args);
	$plugin_args_name	 = 'fullcontent';
	$args_plugin		 = $plugins_args[$plugin_args_name];
	$license			 = wpematico_licenses_handlers::get_key($plugin_args_name);
	$license_status		 = wpematico_licenses_handlers::get_license_status($plugin_args_name);
	$expire_license		 = 'No expiration';
	if($license != false) {
		$args_check		 = array(
			'license'	 => $license,
			'item_name'	 => urlencode($args_plugin['api_data']['item_name']),
			'url'		 => home_url(),
			'version'	 => $args_plugin['api_data']['version'],
			'author'	 => 'Esteban Truelsegaard'
		);
		$api_url		 = $args_plugin['api_url'];
		$license_data	 = wpematico_licenses_handlers::check_license($api_url, $args_check);
		if(is_object($license_data)) {

			$expires = $license_data->expires;
			$expires = substr($expires, 0, strpos($expires, " "));

			if(!empty($license_data->payment_id) && !empty($license_data->license_limit)) {
				$expire_license = $expires;
			}
		}
	}

	if($license_status == false) {
		$license_status = 'No license';
	}
	$return	 .= 'License Status:           ' . $license_status . "\n";
	$return	 .= 'License Expiration:       ' . $expire_license . "\n";
	return $return;
}

function wpefull_base64url_encode($data) {
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
