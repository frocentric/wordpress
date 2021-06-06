<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Zapier
 * Plugin URI: http://ninjaforms.com/downloads/zapier
 * Description: Integrates Ninja Forms with Zapier.
 * Version: 3.0.8
 * Author: Fatcat Apps
 * Author URI: http://fatcatapps.com
 * Text Domain: zapier
 *
 * Copyright 2016 Fatcat Apps.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    include 'deprecated/ninja-forms-zapier.php';

} else {

    /**
     * Class NF_Zapier
     */
    final class NF_Zapier
    {
        const VERSION = '3.0.8';
        const SLUG    = 'zapier';
        const NAME    = 'Zapier';
        const AUTHOR  = 'Fatcat Apps';
        const PREFIX  = 'NF_Zapier';

        /**
         * @var NF_Zapier
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_Zapier Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_Zapier)) {
                self::$instance = new NF_Zapier();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));
            }
        }

        public function __construct()
        {

            add_action( 'admin_init', array( $this, 'setup_license') );
            add_filter( 'ninja_forms_register_actions', array($this, 'register_actions'));

        }

        public function register_actions($actions)
        {
            $actions[ 'zapier' ] = new NF_Zapier_Actions_ZapierAction(); // includes/Actions/ZapierAction.php

            return $actions;
        }


        /*
         * Optional methods for convenience.
         */

        public function autoloader($class_name)
        {
            if (class_exists($class_name)) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }

        /*
         * Required methods for all extension.
         */

        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }
    }

    /**
     * The main function responsible for returning The Highlander Plugin
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return {class} Highlander Instance
     */
    function NF_Zapier()
    {
        return NF_Zapier::instance();
    }
	
	function ninja_forms_zapier_activation() {
	  wp_schedule_event( time(), 'hourly', 'ninja_forms_zapier_cron_hourly' );
	}
	register_activation_hook( __FILE__, 'ninja_forms_zapier_activation' );
	
	function ninja_forms_zapier_deactivation() {
	  wp_clear_scheduled_hook( 'ninja_forms_zapier_cron_hourly' );
	}
	register_deactivation_hook( __FILE__, 'ninja_forms_zapier_deactivation' );
	
	function ninja_forms_zapier_cron_hourly() {
		$upload_dir = wp_upload_dir();
		$path = $upload_dir['basedir'] . '/ninja-forms-zapier/';

		if (file_exists($path)) {
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					if ( preg_match("/^([0-9]{10}\-[0-9a-z]+\.txt)$/sim", $entry) ) {
						// Read file
						$data = ninja_forms_zapier_read_cache_file($path . $entry);
						
						// Make sure this hasn't been in queue too long, otherwise skip check & delete
						$submission_date = strtotime( $data['fields']['Date'] );
						$now = strtotime( 'now' );
						$difference = ( $now - $submission_date );
												
						// Process request
						if (isset($data['url']) && isset($data['fields']) && ($difference < 345600)) {
							ninja_forms_zapier_post_to_webhook($data['url'], $data['fields']);
						}

						// Remove cache file
						unlink($path . $entry);
					}
				}
				closedir($handle);
			}
		}
	}
	add_action( 'ninja_forms_zapier_cron_hourly', 'ninja_forms_zapier_cron_hourly' );

	//-----------------------------------------------------------------------------

	/**
	 * Reads cache file
	 * Called from within ninja_forms_zapier_process_request_cache()
	 * @param  string $path  path to the file in filesystem
	 */
	function ninja_forms_zapier_read_cache_file($path) {
		$content = file_get_contents($path);
		$data = unserialize($content);

		return $data;
	}
	
	//-----------------------------------------------------------------------------

	/**
	 * Saves failed request to zapier.com to file (cache).
	 */
	function ninja_forms_zapier_save_to_file($url, $fields) {
		$upload_dir = wp_upload_dir();
		$path = $upload_dir['basedir'] . '/ninja-forms-zapier/';
		$filename = time() . '-' . uniqid() . '.txt';

		// Create cache store directory if needed
		ninja_forms_zapier_create_store_dir($path);

		// Write cached request if directory is writable
		if (is_writable(dirname($path . $filename))) {
			$data = array(
				'url' => $url,
				'fields' => $fields
			);

			$request = fopen($path . $filename, 'w+');
			fwrite($request, serialize($data));
			fclose($request);
		}
	}

	//-----------------------------------------------------------------------------

	/**
	 * Creates a store directory under wp-content/uploads for plugin cache files.
	 * Adds index.php to prevent directory listing.
	 */
	function ninja_forms_zapier_create_store_dir($path) {
	
		// Create path if not exists
		if (!file_exists($path)) {
			$is_created = mkdir($path, 0777, true);
			if (!$is_created) {
				return;
			}
		}

		// Add index.php to prevent directory listing
		if (!file_exists($path . 'index.php') &&
				is_writable(dirname($path . 'index.php'))) {

			$index = fopen($path . 'index.php', 'w');
			fwrite($index, "<?php" . "\n");
			fwrite($index, "// Silence is golden.");
			fclose($index);
		}
	}
	
	//-----------------------------------------------------------------------------

	/**
	 * Tries to submit request to zapier.com.
	 */
	function ninja_forms_zapier_post_to_webhook($url, $fields) {
	
		// Headers
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json'
		);

		$response = wp_remote_post(
			$url, array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $headers,
				'body' => $fields,
				'cookies' => array()
			)
		);

		// Cache request if failed
		if (is_wp_error($response)) {
			ninja_forms_zapier_save_to_file($url, $fields);
			return false;
		} else {
			return true;
		}
	}
	
	NF_Zapier();
}

add_filter( 'ninja_forms_upgrade_settings', 'NF_Zapier_Upgrade' );
function NF_Zapier_Upgrade( $data ){

	$actions = array();

	if( isset( $data[ 'settings' ][ 'zap_webhook_urls' ] ) ){

		if( is_array( $data[ 'settings' ][ 'zap_webhook_urls' ] ) ){
			foreach( $data[ 'settings' ][ 'zap_webhook_urls' ] as $key => $hook ){
				$actions[ $key ][ 'zapier-hook' ] = $hook;
			}
		}

		unset( $data[ 'settings' ][ 'zap_webhook_urls' ] );
	}

	if( isset( $data[ 'settings' ][ 'zap_statuss' ] ) ){

		if( is_array( $data[ 'settings' ][ 'zap_statuss' ] ) ){
			foreach( $data[ 'settings' ][ 'zap_statuss' ] as $key => $status ){
				$actions[ $key ][ 'active' ] = ( $status ) ? 1 : 0;
			}
		}

		unset( $data[ 'settings' ][ 'zap_statuss' ] );
	}

	if( isset( $data[ 'settings' ][ 'zap_names' ] ) ){

		if( is_array( $data[ 'settings' ][ 'zap_names' ] ) ){
			foreach( $data[ 'settings' ][ 'zap_names' ] as $key => $name ){
				$actions[ $key ][ 'label' ] = $name;
			}
		}

		unset( $data[ 'settings' ][ 'zap_names' ] );
	}

	if( is_array( $actions ) ){
		foreach( $actions as $key => $action ){
			$actions[ $key ][ 'type' ] = 'zapier';
		}

		$data[ 'actions' ] = array_merge( $data[ 'actions' ], $actions );
	}

	return $data;
}
