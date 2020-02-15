<?php
if ( ! class_exists( 'wpe_partial_content_curl' ) ) {
class wpe_partial_content_curl {

	public static $range_byte = 5242880;  //(1024*1024)*5
	public static $content_length = 0;
	public static $start = 0;
	public static $max_ranges = 100;
	public static function start($url, $dest_file, $mbs = 5) {
		$ch = curl_init($url);
		if(!$ch) {
			return false;
		} 
		self::$range_byte = (int)$mbs*(1024*1024);
		self::$start = 0;
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		preg_match('/^Content-Length: (\d+)/m', $data, $matches);
		self::$content_length = (int)$matches[1];
		file_put_contents($dest_file, '');
		self::get_ranges($url, $dest_file);
		return true;
	}
	public static function get_range($url, $dest_file) {
		if (self::$start >= self::$content_length) {
			return false;
		}
		$end = self::$start+self::$range_byte;
		if ($end >= self::$content_length) {
			$end = self::$content_length;
		}
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Range: bytes=".self::$start."-".$end.""));
		$data = curl_exec($ch);
		file_put_contents($dest_file, $data, FILE_APPEND);
		self::$start = (self::$start+self::$range_byte)+1;
		
		return true;
	}
	public static function get_ranges($url, $dest_file) {
		$range_index = 0;
		while(self::get_range($url, $dest_file)) {

			if (self::$max_ranges < $range_index) {
				break;
			}
			$range_index++;
		}
	}
}
}

?>