<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NF_FU_Helper
 *
 * The Static Helper Class
 *
 * Provides helper functionality for File Uploads
 */
final class NF_FU_Helper {

	public static function random_string( $length = 10 ) {
		$characters    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$random_string = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
		}

		return $random_string;
	}

	/**
	 * Get the max chunk size for uploads to the server and services.
	 *
	 * @return bool|mixed|void
	 */
	public static function get_max_chunk_size() {
		return apply_filters( 'ninja_forms_upload_max_chunk_size', round( self::max_upload_bytes_int() * 0.9 ) );
	}

	/**
	 * Get the lowest integer in MB for upload max size.
	 *
	 * @return float|int|mixed
	 */
	public static function max_upload_bytes_int() {
		$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
		$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );

		return min( $u_bytes, $p_bytes );
	}

	/**
	 * Get the lowest integer in MB for upload max size.
	 *
	 * @return float|int|mixed
	 */
	public static function max_upload_mb_int() {
		$max = self::max_upload_bytes_int();

		$max = $max / MB_IN_BYTES;

		return $max;
	}

	/**
	 * Fix for overflowing signed 32 bit integers,
	 * works for sizes up to 2^32-1 bytes (4 GiB - 1):
	 *
	 * @param $size
	 *
	 * @return float
	 */
	public static function fix_integer_overflow( $size ) {
		if ( $size < 0 ) {
			$size += 2.0 * ( PHP_INT_MAX + 1 );
		}

		return $size;
	}

	public static function get_file_size( $file_path, $clear_stat_cache = false ) {
		if ( $clear_stat_cache ) {
			if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
				clearstatcache( true, $file_path );
			} else {
				clearstatcache();
			}
		}

		return self::fix_integer_overflow( filesize( $file_path ) );
	}

	/**
	 * Are we on the FU page?
	 *
	 * @param string $tab
	 * @param array  $args
	 *
	 * @return bool
	 */
	public static function is_page( $tab = '', $args = array() ) {
		global $pagenow;

		if ( 'admin.php' !== $pagenow ) {
			return false;
		}

		$defaults = array( 'page' => 'ninja-forms-uploads' );

		if ( $tab ) {
			$defaults['tab'] = $tab;
		}

		$args = array_merge( $args, $defaults );

		foreach ( $args as $key => $value ) {
			if ( ! isset( $_GET[ $key ] ) ) {
				return false;
			}

			if ( false !== $value && $value !== $_GET[ $key ] ) {
				return false;
			}
		}

		return true;
	}

	public static function remove_directory_from_file( $file ) {
		$file_dir = dirname( $file );
		if ( $file_dir != '.' ) {
			$file = str_replace( $file_dir, '', $file ); // Stop traversal exploits
		}
		$file = ltrim( $file, DIRECTORY_SEPARATOR );

		return $file;
	}

} // End Class WPN_Helper
