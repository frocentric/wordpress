<?php
/*
 * Plugin Name: Ninja Forms - Conditional Logic
 * Plugin URI: https://ninjaforms.com/extensions/conditional-logic/
 * Description: Conditional form logic add-on for Ninja Forms.
 * Version: 3.1
 * Author: The WP Ninjas
 * Author URI: https://ninjaforms.com
 * Text Domain: ninja-forms-conditionals
 * Domain Path: /lang/
 */

if( ! class_exists( 'NF_ConditionalLogic_Conversion', false ) ) {
    require_once 'lib/conversion.php';
}

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    if( ! defined( 'NINJA_FORMS_CON_DIR' ) ){
        define("NINJA_FORMS_CON_DIR", plugin_dir_path(__FILE__) . '/deprecated');
    }

    if( ! defined( 'NINJA_FORMS_CON_URL' ) ) {
        define("NINJA_FORMS_CON_URL", plugin_dir_url(__FILE__) . '/deprecated');
    }

    if( ! defined( 'NINJA_FORMS_CON_VERSION' ) ) {
        define("NINJA_FORMS_CON_VERSION", "3.1");
    }

    include 'deprecated/conditionals.php';

} else {

    if( class_exists( 'NF_ConditionalLogic', false ) ) return;
    /**
     * Class NF_ConditionalLogic
     */
    final class NF_ConditionalLogic
    {
        const VERSION = '3.1';
        const SLUG    = 'conditional-logic';
        const NAME    = 'Conditional Logic';
        const AUTHOR  = 'The WP Ninjas';
        const PREFIX  = 'NF_ConditionalLogic';

        /**
         * Condition Triggers
         *
         * @since 3.0
         * @var array
         */
        public $triggers = array();

        /**
         * Condition Comparators
         *
         * @since 3.0
         * @var array
         */
        public $comparators = array();

        /**
         * Integrations
         *
         * @since 3.0
         * @var array
         */
        public $integrations = array();

        /**
         * NF_ConditionalLogic constructor.
         */
        public function __construct()
        {
            // WordPress Hooks
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'admin_init', array( $this, 'setup_license' ) );

            // Ninja Forms Hooks
            add_action( 'ninja_forms_loaded', array( $this, 'setup_admin' ) );

            // Ninja Forms Admin Hooks
            add_filter( 'ninja_forms_field_settings_groups', array( $this, 'register_settings_groups' ) );
            add_filter( 'ninja_forms_action_settings', array( $this, 'register_action_settings' ) );
            add_filter( 'ninja_forms_actions_settings_all', array( $this, 'action_settings_all' ) );
            add_filter( 'nf_admin_enqueue_scripts', array( $this, 'builder_scripts' ) );
            add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );

            /*
             * Add action conditions to the form conditions localised data
             */
            add_filter( 'ninja_forms_display_form_settings', array( $this, 'localize_action_conditions' ), 10, 2 );
        }

        public function init()
        {
            new NF_ConditionalLogic_Submission();

            self::$instance->integrations = array(
                new NF_ConditionalLogic_Integrations_MultiPart()
            );

            self::$instance->triggers = NF_ConditionalLogic::config( 'Triggers' );
            self::$instance->comparators = NF_ConditionalLogic::config( 'Comparators' );
        }

        public function setup_admin()
        {
            if( ! is_admin() ) return;

            new NF_ConditionalLogic_Admin_Settings();
        }

        public function register_settings_groups( $settings_groups )
        {
            return array_merge( $settings_groups, self::config( 'ActionSettingsGroups' ) );
        }

        public function register_action_settings( $action_settings )
        {
            return array_merge( $action_settings, self::config( 'ActionSettings' ) );
        }

        public function action_settings_all( $settings_all )
        {
            array_push( $settings_all, 'conditions' ); // TODO: Add registered settings names.
            return $settings_all;
        }

        public function builder_scripts()
        {
            $ver = self::VERSION;
            wp_enqueue_style(  'nf-cl-builder',  plugin_dir_url( __FILE__ ) . 'assets/css/builder.css', array(), $ver );
            wp_enqueue_script( 'nf-cl-builder',  plugin_dir_url( __FILE__ ) . 'assets/js/min/builder.js', array(), $ver );
            wp_localize_script( 'nf-cl-builder', 'nfcli18n', self::config( 'i18nCLBuilder' ) );
            wp_localize_script( 'nf-cl-builder', 'nfListCountries', Ninja_Forms::config( 'CountryList' ) );
        }

        public function builder_templates()
        {
            NF_ConditionalLogic::template( 'builder-edit-settings.html.php' );
        }

        /*
        |--------------------------------------------------------------------------
        | Internal API Methods
        |--------------------------------------------------------------------------
        */

        /**
         * Comparator
         *
         * @since 3.0.0
         * @param $key
         * @return NF_ConditionalLogic_Comparator
         * @throws Exception
         */
        public function comparator( $key )
        {
            if( isset( $this->comparators[ $key ] ) ) return $this->comparators[ $key ][ 'instance' ];
            return new NF_ConditionalLogic_Comparators_NullComparator();
        }

        /**
         * Trigger
         *
         * @since 3.0.0
         * @param $key
         * @return NF_ConditionalLogic_Trigger
         * @throws Exception
         */
        public function trigger( $key )
        {
            if( isset( $this->triggers[ $key ] ) ) return $this->triggers[ $key ][ 'instance' ];
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Plugin Properties and Methods
        |--------------------------------------------------------------------------
        */

        /**
         * @var NF_ConditionalLogic
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
         * @return NF_ConditionalLogic Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_ConditionalLogic)) {
                self::$instance = new NF_ConditionalLogic();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));
            }

            return self::$instance;
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
         * Autoloader
         *
         * Loads files using the class name to mimic the folder structure.
         *
         * @param $class_name
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
         * Setup License
         *
         * Registers the plugin with the extension updater.
         */
        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }

        public function localize_action_conditions( $settings, $form_id ) {
            /*
             * Get any action conditions
             */
            $action_conditions = array();

            $form_actions = Ninja_Forms()->form( $form_id )->get_actions();
            foreach( $form_actions as $action ){
                $conditions = $action->get_setting( 'conditions' );
                if( ! isset ( $conditions[ 'when' ][0][ 'key' ] ) || empty( $conditions[ 'when' ][0][ 'key' ] ) ) continue;
                
                /*
                 * Make sure that we have a valid "then" setting.
                 */
                if ( ! isset ( $conditions[ 'then' ][0][ 'key' ] ) || empty( $conditions[ 'then' ][0][ 'key' ] ) ) {
                    $conditions[ 'then' ][0][ 'key' ] = $action->get_id();
                    $conditions[ 'then' ][0][ 'trigger' ] = 'activate_action';
                    $conditions[ 'then' ][0][ 'type' ] = 'action';
                }

                /*
                 * Make sure that we have a valid "else" setting.
                 */
                if ( ! isset ( $conditions[ 'else' ][0][ 'key' ] ) || empty( $conditions[ 'else' ][0][ 'key' ] ) ) {
                    $conditions[ 'else' ][0][ 'key' ] = $action->get_id();
                    $conditions[ 'else' ][0][ 'trigger' ] = 'deactivate_action';
                    $conditions[ 'else' ][0][ 'type' ] = 'action';
                    $conditions[ 'else' ][0][ 'modelType' ] = 'else';
                }

                $settings[ 'conditions' ][] = $conditions;

            }

            return $settings;
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
     * @return NF_ConditionalLogic Highlander Instance
     */
    function NF_ConditionalLogic()
    {
        return NF_ConditionalLogic::instance();
    }

    NF_ConditionalLogic();

    /*
     * Localize our mock conditional logic data
     */
    function nf_cl_display_mock_data( $form_id ) {
        wp_enqueue_script( 'nf-cl-front-end', plugin_dir_url( __FILE__ ) . 'assets/js/min/front-end.js', array( 'nf-front-end' ) );
    }

    add_action( 'ninja_forms_enqueue_scripts', 'nf_cl_display_mock_data' );

}