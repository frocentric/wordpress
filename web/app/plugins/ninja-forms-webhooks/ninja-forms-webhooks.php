<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Webhooks
 * Plugin URI: https://ninjaforms.com/extensions/webhooks/
 * Description: Send submission data collected by Ninja Forms to an external API/URL.
 * Version: 3.0.5
 * Author: The WP Ninjas
 * Author URI: http://ninjaforms.com
 * Text Domain: ninja-forms-webhooks
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Copyright 2016 The WP Ninjas.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    // Define our plugin version.
    if ( ! defined( 'NF_WH_VERSION' ) )
        define( 'NF_WH_VERSION', '3.0.5' );

    include 'deprecated/webhooks.php';

} else {

    /**
     * Class NF_Webhooks
     */
    final class NF_Webhooks
    {
        const VERSION = '3.0.5';
        const SLUG    = 'webhooks';
        const NAME    = 'Webhooks';
        const AUTHOR  = 'The WP Ninjas';
        const PREFIX  = 'NF_Webhooks';

        /**
         * @var NF_Webhooks
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
         * @return NF_Webhooks Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_Webhooks)) {
                self::$instance = new NF_Webhooks();

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
            /*
             * Required for all Extensions.
             */
            add_action( 'admin_init', array( $this, 'setup_license') );
            
            /*
             * Optional. If your extension processes or alters form submission data on a per form basis...
             */
            add_filter( 'ninja_forms_register_actions', array($this, 'register_actions'));
        }

        /**
         * Optional. If your extension processes or alters form submission data on a per form basis...
         */
        public function register_actions($actions)
        {
            $actions[ 'webhooks' ] = new NF_Webhooks_Actions_Webhooks(); // includes/Actions/WebhooksExample.php

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
        
        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         */
        public static function template( $file_name = '', array $data = array() )
        {
            if( ! $file_name ) return;

            extract( $data );

            include self::$dir . 'includes/Templates/' . $file_name;
        }
        
        /**
         * Config
         *
         * @param $file_name
         * @return mixed
         */
        public static function config( $file_name )
        {
            return include self::$dir . 'includes/Config/' . $file_name . '.php';
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
    function NF_Webhooks()
    {
        return NF_Webhooks::instance();
    }

    NF_Webhooks();
}

add_filter( 'ninja_forms_upgrade_action_webhooks', 'NF_Webhooks_Upgrade' );
 function NF_Webhooks_Upgrade( $action ){

 	// commenting out NF 2.9 settings so we can check for them below
    $data = array(
        'active'            => '1',
        'name'              => __( 'Webhooks', 'ninja-forms-webhooks' ),
        'type'              => 'webhooks',
//        'wh-remote-url'     => $action[ 'wh_remote_url' ],
//        'wh-remote-method'  => $action[ 'wh_remote_method' ],
//        'wh-encode-json'    => $action[ 'wh_json_encode' ],
//        'wh-json-use-arg'   => $action[ 'wh_json_use_arg' ],
//        'wh-json-arg'       => $action[ 'wh_json_arg' ],
//        'wh-debug-mode'     => $action[ 'wh_debug' ],
        'wh-args'           => nf_webhooks_format_args( $action[ 'wh-args' ] )
    );

    /*
     * We need to check for 2.9 settings b/c this may or may not be an import
     *  from a 2.9 export
     */
    if( isset( $action[ 'wh_remote_url' ] ) ) {
    	$data[ 'wh-remote-url' ] = $action[ 'wh_remote_url' ];
    } elseif( isset( $data[ 'wh-remote-url' ] ) ) {
	    $data[ 'wh-remote-url' ] = $action[ 'wh-remote-url' ];
    }

	 if( isset( $action[ 'wh_remote_method' ] ) ) {
		 $data[ 'wh-remote-method' ] = $action[ 'wh_remote_method' ];
	 } elseif( isset( $data[ 'wh-remote-method' ] ) )  {
		 $data[ 'wh-remote-method' ] = $action[ 'wh-remote-method' ];
	 }

	 if( isset( $action[ 'wh_json_encode' ] ) ) {
		 $data[ 'wh-encode-json' ] = $action[ 'wh_json_encode' ];
	 } elseif( isset( $data[ 'wh-encode-json' ] ) ) {
		 $data[ 'wh-encode-json' ] = $action[ 'wh-encode-json' ];
	 }

	 if( isset( $action[ 'wh_json_use_arg' ] ) ) {
		 $data[ 'wh-json-use-arg' ] = $action[ 'wh_json_use_arg' ];
	 } elseif( isset( $data[ 'wh-json-use-arg' ] ) ) {
		 $data[ 'wh-json-use-arg' ] = $action[ 'wh-json-use-arg' ];
	 }

	 if( isset( $action[ 'wh_json_arg' ] ) ) {
		 $data[ 'wh-json-arg' ] = $action[ 'wh_json_arg' ];
	 } elseif( isset( $data[ 'wh-json-arg'] ) ) {
		 $data[ 'wh-json-arg' ] = $action[ 'wh-json-arg' ];
	 }

	 if( isset( $action[ 'wh_debug' ] ) ) {
		 $data[ 'wh-debug-mode' ] = $action[ 'wh_debug' ];
	 } elseif( isset( $action[ 'wh-debug-mode' ] ) ) {
		 $data[ 'wh-debug-mode' ] = $action[ 'wh-debug-mode' ];
	 }

    $action = array_merge( $action, $data );
    return $action;

}

function nf_webhooks_format_args( $args ) {

    foreach( $args as $key => $arg ){

    	$value = $arg[ 'value' ];

    	// if $arg has a field attribute, do stuff with it
    	if( isset( $arg[ 'field' ] ) ) {
		    $value = explode( '`', $arg['field'] );

		    if ( is_array( $value ) ) {
			    $value = array_map( 'nf_webhooks_convert_arg_value', $value );
			    $value = implode( '', $value );
		    } else {
			    $value = nf_webhooks_convert_arg_value( $value );
		    }
	    }

        $args[ $key ][ 'order' ] = $key;
        $args[ $key ][ 'value' ] = $value;
        unset( $args[ $key ][ 'field' ] );
    }

    return array_values( $args );
}

function nf_webhooks_convert_arg_value( $value ){
    $parts = explode( '_', $value );

    if( ! is_array( $parts ) ) return $value;

    if( ! isset( $parts[0] ) || ! isset( $parts[1] ) ) return $value;

    if( 'field' != $parts[0] || ! is_numeric( $parts[1] ) ) return $value;

    return '{field:' . $parts[1] . '}';
}