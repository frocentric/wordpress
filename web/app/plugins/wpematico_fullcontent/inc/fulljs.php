<?php
if(!class_exists('WPeMatico_FullContent_JS')) :

	/**
	 * Main WPeMatico_FullContent_JS class
	 * @since       1.7.0
	 */
	class WPeMatico_FullContent_JS {

		private static $api_url = 'https://fulljs.wpematico.com/wp-admin/admin-post.php';

		/**
		 * Static function request
		 * @access public
		 * @return void
		 * @since version
		 */
		public static function request($html) {
			$license_status	 = wpematico_licenses_handlers::get_license_status('fullcontent');
			$license_status	 = 'valid';
			if($license_status != false && $license_status == 'valid') {
				return self::do_request($html);
			}else {
				trigger_error(__('FULLJS-ERROR: your lisence is have a invalid status: ', 'wpematico') . $license_status, E_USER_WARNING);
				return $html;
			}
		}

		/**
		 * Static function do_request
		 * @access public
		 * @return $html String, has the new HTML DOM content.
		 * @since 1.7.0
		 */
		public static function do_request($html) {
			$api_params	 = array(
				'action' => 'full_js_api',
				'key'	 => wpematico_licenses_handlers::get_key('fullcontent'),
				'url'	 => home_url(),
				'source' => wpefull_base64url_encode($html)
			);
			$response	 = wp_remote_post(esc_url_raw(self::$api_url), array('timeout' => 120, 'sslverify' => false, 'body' => $api_params));
			if(is_wp_error($response)) {
				trigger_error(__('FULLJS-ERROR: wp_remote_post error:', 'wpematico') . $response->get_error_message(), E_USER_WARNING);
				return $html;
			}
			$html_api = wp_remote_retrieve_body($response);
			if(strpos($html_api, 'FULLJS-ERROR') !== false) {
				trigger_error(esc_html($html_api), E_USER_WARNING);
				return $html;
			}
			return $html_api;
		}

	}
	
endif;