<?php
/**
* It will be used to manage cookies features.
* @package     WPeMatico Professional
* @subpackage  Cookies
* @since       1.9.0
*/
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
* WPeMatico Professional Cookies Class 
* @since 1.9.0
*/
if (!class_exists('WPeMaticoPro_Cookies')) :
class WPeMaticoPro_Cookies {
	public static $hosts = array();
	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.9.0
	*/
	public static function hooks() {
	
	}
	/**
	* Static function url_is_using_cookie
	* @access public
	* @return void
	* @since 1.9.0
	*/
	public static function url_is_using_cookie($parsed_url) {
		if (isset($parsed_url['host'])) {
			foreach (self::$hosts as $host) {
				if (stripos($host, $parsed_url['host']) !== false || stripos($parsed_url['host'], $host) !== false) {
					return $host;
				}
			}
		}
		return false;
	}
	/**
	* Static function get_path
	* @access public
	* @return void
	* @since 1.9.0
	*/
	public static function get_path() {
		$upload_dir = wp_upload_dir();

		$new_dst = 'wpematicopro/curl_cookies/';
		$ret = trailingslashit($upload_dir['basedir']).$new_dst;
		if(!is_dir($ret)) {
			@mkdir($ret,0777, true);  
		}
		
		return $ret;
	}
	/**
	* Static function get_file_path
	* @access public
	* @return void
	* @since 1.9.0
	*/
	public static function get_file_path($hash) {
		$file_path = self::get_path().$hash.'_cookie.txt';
		return $file_path;
	}

}
endif;
