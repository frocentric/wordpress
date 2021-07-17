<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - User Management
 * Plugin URI: https://ninjaforms.com/user-managemnet
 * Description: Register and manage users on your WordPress website with Ninja Forms.
 * Version: 3.0.12
 * Author: WP Ninjas
 * Author URI: https://ninjaforms.com
 * Text Domain: ninja-forms-user-management
 *
 * Copyright 2016 WP Ninjas.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

} else {

    /**
     * Class NF_UserManagement
     */
    final class NF_UserManagement
    {
        const VERSION = '3.0.12';
        const SLUG    = 'user-management';
        const NAME    = 'User Management';
        const AUTHOR  = 'WP Ninjas';
        const PREFIX  = 'NF_UserManagement';

        /**
         * @var NF_UserManagement
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
         * @return NF_UserManagement Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_UserManagement)) {
                self::$instance = new NF_UserManagement();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register( array( self::$instance, 'autoloader'));
            }
            
            return self::$instance;
        }

        /**
         * NF_UserManagement constructor.
         */
        public function __construct()
        {
            /*
             * Required for all Extensions.
             */
            add_action( 'admin_init', array( $this, 'setup_license' ) );
            
            add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

            /*
             * Register actions
             */
            add_filter( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );

            /*
             * Register merge tags.
             */
            add_action( 'ninja_forms_loaded', array( $this, 'register_mergetags' ) );

            /*
             * Register Update Profile Listener.
             */
            add_action( 'nf_get_form_id', array( $this, 'update_profile_listener' ) );

            add_action( 'ninja_forms_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            add_filter( 'ninja_forms_new_form_templates', array( $this, 'register_templates' ) );

            add_filter( 'ninja_forms_enable_password_fields', '__return_true' );
        }
        
        public function plugins_loaded()
        {
            // Filter the domain path to allow it to be loaded externally without overwrites on update.
            $domain_path = apply_filters( 'nf_user_management_text_domain', basename( dirname( __FILE__ ) ) . '/lang' );
            // Load our text domain.
            load_plugin_textdomain( 'ninja-forms-user-management', false, $domain_path );
        }

        public function enqueue_scripts()
        {
            wp_enqueue_script( 'nf_user_management', self::$url . 'assets/js/errorHandling.js', array() );
        }

        /**
         * Register Mergetags
         *
         * Register the merge tags that are used by the user management add-on.
         */
        public function register_mergetags()
        {
            Ninja_Forms()->merge_tags[ 'user_management' ] = new NF_UserManagement_MergeTags();
        }

        /**
         * Get User Roles
         *
         * Returns an array containing the all role in a key/value pair.
         */
        public function get_user_roles()
        {
            //Possible remove these values from the array.
//            $basic_roles = array( 'subscriber', 'contributor', 'author', 'editor', 'administrator' );

            $roles = wp_roles();
            return $roles->role_names;
        }


        /**
         * Register Actions
         *
         * Registers all actions that are used by user management add-on.
         *
         * @param $actions
         * @return mixed
         */
        public function register_actions( $actions )
        {
            $actions[ 'login-user' ]        = new NF_UserManagement_Actions_LoginUser();

            $actions[ 'register-user' ]     = new NF_UserManagement_Actions_RegisterUser();

            $actions[ 'update-profile' ]    = new NF_UserManagement_Actions_UpdateProfile();

            return $actions;
        }

        /**
         * Update Profile Listener
         *
         * This method listens to see if an update-profile action exists on a form and runs a file that pre-populates
         * the user data in the fields select in the action.
         */
        public function update_profile_listener( $form_id )
        {
            if ( ! function_exists('Ninja_Forms' ) ) return;

            //Loops over fields and gets all actions
            $actions = Ninja_Forms()->form( $form_id )->get_actions();
            foreach ( $actions as $action ) {

                //Checks for update-profile action, continues if action is present.
                if ( 'update-profile' != $action->get_setting( 'type' ) ) continue;
                //Exit if the action isn't active.
                if ( ! intval( $action->get_setting( 'active' ) ) ) continue;

                //Build args arrays to pass to the UpdateProfileListener.
                $settings = $action->get_settings( 'username_nickname', 'password', 'email', 'first_name', 'last_name', 'url' );
                $custom_meta = $action->get_settings( 'custom_meta' );
                new NF_UserManagement_UpdateProfileListener( $settings, $custom_meta );
            }
        }

        /**
         * Strip Merge Tags
         *
         * Accepts a key/value pair of $action_settings removes the merge tag formatting from the Value only of each pair.
         *
         * @param $action_settings
         * @return array of setting_value(with mergetag formatting removed)/setting_name
         *
         * example return username_1489426787243 => username
         */
        public function strip_merge_tags( $action_settings )
        {
            //Build our return array.
            $settings = array();

            //Loop over action settings.
            foreach( $action_settings as $key => $value ) {
                //Removes the merge tag formatting
                $settings_value = str_replace( '{field:', '', $value );
                $settings_value = str_replace( '}', '', $settings_value );

                //Builds $fields array for return value.
                $settings[ $settings_value ] = $key;
            }
            return $settings;
        }

        /**
         * Get Field ID
         *
         * Loops over fields and action setting keys to build an array of setting name.
         *
         * @param $fields
         * @param $settings
         * @return array
         *
         * example return username => 5
         */
        public function get_field_id( $fields, $settings )
        {
            //Creating the array we will use to return.
            $field_ids = array();

            //Loop over $fields array
            foreach( $fields as $field ) {

                //Get the field key of each field in the fields array.
                $field_key = $field->get_setting( 'key' );

                //Loop over array of setting keys.
                foreach( $settings as $setting_key => $setting_value ){

                    //Compares setting key and field key to ensure they are the same.
                    if( $setting_key == $field_key ) {
                        //Builds the return array of the setting value and field IDs
                        $field_ids[ $setting_value ] = $field->get_id();
                    }
                }
            }
            return $field_ids;
        }

        /**
         * Register Templates
         *
         * Registers our custom form templates.
         *
         * @param $templates
         * @return mixed
         */
        public function register_templates( $templates )
        {
            //Register the login form template.
            $templates[ 'login-form' ] = array(
                'id'            => 'login-form',
                'title'         => __( 'Login Form', 'ninja-forms-user-management' ),
                'template-desc' => __( 'Allow your users to login. You can add and remove fields as needed.',
                                        'ninja-forms-user-management' ),
                'form'          => self::form_templates( 'login-form.nff' ),
            );

            $templates[ 'register-user' ] = array(
                'id'            => 'register-user',
                'title'         => __( 'Register User', 'ninja-forms-user-management' ),
                'template-desc' => __( 'Register new users, including custom user meta. You can add and remove fields as needed.', 'ninja-forms-user-management' ),
                'form'          => self::form_templates( 'register-user.nff' ),
            );

            $templates[ 'update-profile' ] = array(
                'id'            => 'update-profile',
                'title'         => __( 'Update Profile', 'ninja-forms-user-management' ),
                'template-desc' => __( 'Allow users to update their profiles, including custom user meta. You can add and remove fields as needed.',
                                        'ninja-forms-user-management'),
                'form'          => self::form_templates( 'update-profile.nff' ),
            );

            return $templates;
        }

        /**
         * autoloader - built by Kozo Generator/
         *
         * @param $class_name
         */
        public function autoloader( $class_name )
        {
            if ( class_exists( $class_name ) ) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';

            if ( file_exists( $classes_dir . $class_file ) ) {
                require_once $classes_dir . $class_file;
            }
        }

        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         * @return string
         */
        public static function template( $file_name = '', array $data = array() )
        {
            if( ! $file_name ) return;

            extract( $data );

            include self::$dir . 'includes/Templates/' . $file_name;
        }

        /**
         * Form Templates
         *
         * This method is used to load the form templates
         *
         * @param string $file_name
         * @param array $data
         * @return string
         */
        public static function form_templates( $file_name = '', array $data = array() )
        {
            $path = self::$dir . 'includes/Templates/' . $file_name;

            if( ! file_exists(  $path ) ) return '';

            extract( $data );

            ob_start();

            include $path;

            return ob_get_clean();
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
    function NF_UserManagement()
    {
        return NF_UserManagement::instance();
    }

    NF_UserManagement();
}
