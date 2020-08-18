<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Multi-Part Forms
 * Plugin URI: https://ninjaforms.com/extensions/multi-part-forms/
 * Description: Multi-Part Forms add-on for Ninja Forms.
 * Version: 3.0.26
 * Author: The WP Ninjas
 * Author URI: http://ninjaforms.com
 * Text Domain: ninja-forms-multi-part
 *
 * Copyright 2014 The WP Ninjas.
 */

if( ! class_exists( 'NF_MultiPart_Conversion', false ) ) {
    require_once 'lib/conversion.php';
}

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    if( ! defined( 'NINJA_FORMS_MP_DIR' ) ) {
        define("NINJA_FORMS_MP_DIR", plugin_dir_path(__FILE__) . '/deprecated');
    }

    if( ! defined( 'NINJA_FORMS_MP_URL' ) ) {
        define("NINJA_FORMS_MP_URL", plugin_dir_url(__FILE__) . '/deprecated');
    }

    if( ! defined( 'NINJA_FORMS_MP_VERSION' ) ) {
        define("NINJA_FORMS_MP_VERSION", "3.0.26");
    }

    include 'deprecated/multi-part.php';

} else {

    if( class_exists( 'NF_MultiPart', false ) ) return;

    /**
     * Class NF_MultiPart
     */
    final class NF_MultiPart
    {
        const VERSION = '3.0.26';
        const SLUG    = 'ninja-forms-multi-part';
        const NAME    = 'Multi Part';
        const AUTHOR  = 'The WP Ninjas';
        const PREFIX  = 'NF_MultiPart';

        public function __construct()
        {
            // Ninja Forms Hooks
            add_action( 'ninja_forms_loaded', array( $this, 'setup_admin' ) );

            add_action( 'admin_init', array( $this, 'setup_license') );

            add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );
            add_action( 'ninja_forms_enqueue_scripts',   array( $this, 'frontend_templates' ) );

            add_action( 'nf_admin_enqueue_scripts',   array( $this, 'enqueue_builder' ), 11 );
            add_action( 'nf_display_enqueue_scripts', array( $this, 'enqueue_display' ) );

            /*
             * We need to sort fields in a different order than core.
             */
            add_filter( 'ninja_forms_get_fields_sorted', array( $this, 'filter_field_order' ), 9, 4 );
        }

        public function setup_admin()
        {
            if( ! is_admin() ) return;

            new NF_MultiPart_Admin_Settings();
        }

        public function builder_templates()
        {
            self::template( 'builder.html.php' );
        }

        public function frontend_templates()
        {
            self::template( 'frontend.html.php' );
        }

        public function enqueue_builder()
        {
            $ver = self::VERSION;
            wp_enqueue_style( 'nf-mp-builder', plugin_dir_url( __FILE__ ) . 'assets/css/builder.css', $ver );
            wp_enqueue_script( 'nf-mp-builder', plugin_dir_url( __FILE__ ) . 'assets/js/min/builder.js', array( 'nf-builder', 'jquery-effects-slide', 'jquery-effects-transfer' ), $ver );
        }

        public function enqueue_display()
        {
            $ver = self::VERSION;
            wp_enqueue_script( 'nf-mp-front-end', NF_MultiPart::$url . 'assets/js/min/front-end.js', array(), $ver );
            wp_localize_script( 'nf-mp-front-end', 'nfMPSettings', array(
                'prevLabel' => __( 'Previous', 'ninja-forms-multi-part' ),
                'nextLabel' => __( 'Next', 'ninja-forms-multi-part' ) )
            );

            if( Ninja_Forms()->get_setting( 'opinionated_styles' ) ) {
                if( 'light' == Ninja_Forms()->get_setting( 'opinionated_styles' ) ){
                    wp_enqueue_style( 'nf-mp-display', NF_MultiPart::$url . 'assets/css/display-opinions-light.css', $ver );
                }

                if( 'dark' == Ninja_Forms()->get_setting( 'opinionated_styles' ) ){
                    wp_enqueue_style('nf-mp-display', NF_MultiPart::$url . 'assets/css/display-opinions-dark.css', $ver );
                }
            } else {
                wp_enqueue_style( 'nf-mp-display', NF_MultiPart::$url . 'assets/css/display-structure.css', $ver );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Internal API Methods
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Plugin Properties and Methods
        |--------------------------------------------------------------------------
        */

        /**
         * @var NF_MultiPart
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
         * @return NF_MultiPart Highlander Instance
         */
        public static function instance()
        {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof NF_MultiPart ) ) {
                self::$instance = new NF_MultiPart();
                self::$dir = plugin_dir_path( __FILE__ );
                self::$url = plugin_dir_url( __FILE__ );
                spl_autoload_register( array( self::$instance, 'autoloader' ) );
            }
            return self::$instance;
        }

        /**
         * Autoloader
         *
         * @param $class_name
         */
        public function autoloader($class_name)
        {
            if( class_exists( $class_name ) ) return;

            if( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if ( file_exists( $classes_dir . $class_file ) ) {
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

        /**
         * Setup License
         */
        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( 'Multi-Part Forms', self::VERSION, self::AUTHOR, __FILE__, 'mp' );
        }

        public function filter_field_order( $order, $fields, $fields_by_key, $form_id ) {
            /*
             * We don't want the Layout & Styles filter to run if we are installed.
             */
             if( function_exists( 'NF_Layouts' ) ){
                 remove_filter( 'ninja_forms_get_fields_sorted', array( NF_Layouts(), 'filter_field_order' ) );
            }

            $form = Ninja_Forms()->form( $form_id )->get();
            $formContentData = $form->get_setting( 'formContentData' );

            $new_order = array();

            // If Not a Multi-Part Form, return original order.
            if( ! $formContentData ) return $order;

            foreach( $formContentData as $part ) {
                if( ! isset( $part[ 'formContentData' ] ) ) continue;
                $part_content = $part[ 'formContentData' ];
                /*
                 * If we have part_content['cells'], then we know we're dealing with Layout & Styles data.
                 */
                if ( is_array( $part_content[ 0 ] ) && isset ( $part_content[ 0 ][ 'cells' ] ) ) {

                    foreach ( $part_content as $row ) {

                        if( ! isset( $row[ 'cells' ] ) ) continue;
                        foreach ( $row['cells'] as $cell ) {

                            if( ! isset( $cell[ 'fields' ] ) ) continue;
                             foreach ( $cell[ 'fields' ] as $field_key ) {
                                if( ! isset($fields_by_key[ $field_key ] ) ) continue;
                                $field = $fields_by_key[ $field_key ];
                                $field_id = ( is_object( $field ) ) ? $field->get_id() : $field[ 'id' ];
                                $new_order[ $field_id ] = $field;
                            }
                        }
                    }
                } else {
                    foreach ( $part_content as $field_key ) {
                        if( ! isset( $fields_by_key[ $field_key ] ) ) continue;
                        $field = $fields_by_key[ $field_key ];
                        $field_id = ( is_object( $field ) ) ? $field->get_id() : $field[ 'id' ];
                        $new_order[ $field_id ] = $field;
                    }
                }
            }

            return $new_order;
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
     * @return NF_MultiPart Highlander Instance
     */
    function NF_MultiPart()
    {
        return NF_MultiPart::instance();
    }

    NF_MultiPart();

}
