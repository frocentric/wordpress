<?php

use NinjaForms\UserManagement\Admin\EnableSubmissionsByPermissions;
use NinjaForms\UserManagement\Handlers\RequirementsCheck;

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - User Management
 * Plugin URI: https://ninjaforms.com/user-managemnet
 * Description: Register and manage users on your WordPress website with Ninja Forms.
 * Version: 3.2.0
 * Author: WP Ninjas
 * Author URI: https://ninjaforms.com
 * Text Domain: ninja-forms-user-management
 *
 * Copyright 2016 WP Ninjas.
 * Release Description: Merge branch 'release-3.2.0'
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

} else {

    /**
     * Class NF_UserManagement
     */
    final class NF_UserManagement
    {
        const VERSION = '3.2.0';
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

        /** @var RequirementsCheck */
        public $requirementsCheck;

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

                self::vendorAutoloader();
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

            add_action( 'ninja_forms_loaded', array( $this, 'ninjaFormsLoaded' ) );

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

            /**
             * Enqueue WP element for use access settings
             */
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_settings_scripts' ] );

            /** 
             * Add User Acces tab to Dashboard menu
            */
            //add_filter( 'ninja_forms_dashboard_menu_items', [ $this, 'add_dashboard_menu_item' ] );

            /**
             * Filter data passed to Core Submissions table script
             */
            add_filter( 'ninja_forms_submissions_view_localize_data', [ $this, 'submissions_view_add_localized_data' ] );

            /**
             * Save User Access Settings
             */
            add_action( 'rest_api_init', [ $this, 'nf_user_management_submissions_access_settings' ] );
            
        }
        
        /**
         * Instantiate classes dependent on Ninja Forms core
         *
         * @return void
         */
        public function ninjaFormsLoaded(): void
        {
            $this->requirementsCheck = new RequirementsCheck();

            if ($this->requirementsCheck->canLimitSubmissionsByUser()) {

                new EnableSubmissionsByPermissions();
            }else{

                add_filter( 'nf_admin_notices', [ $this, 'updateCoreForUserSubmissions' ] );
            }
        }

        public function plugins_loaded()
        {
            // Filter the domain path to allow it to be loaded externally without overwrites on update.
            $domain_path = apply_filters( 'nf_user_management_text_domain', basename( dirname( __FILE__ ) ) . '/lang');
            // Load our text domain.
            load_plugin_textdomain( 'ninja-forms-user-management', false, $domain_path );

        }

        /**
         * Notify users to update core for new functionality
         *
         * @param Array $notices Incoming notices
         * @return Array $notices Appended with request to update core
         */
        public function updateCoreForUserSubmissions($notices)
        {
            $notices['userManagementUpdateCore'] = [
                'title' => esc_html__('Update Ninja Forms for enhanced functionality', 'ninja-forms-user-management'),
                'msg' => sprintf(esc_html__('We recently added new submission management features to our User Management plugin.  To enjoy these features, please update the Ninja Forms plugin to the latest version.', 'ninja-forms-user-management'), '<br />'),
                'int' => 0
            ];


            return $notices;
        }

        public function enqueue_scripts()
        {
            wp_enqueue_script( 'nf_user_management', self::$url . 'assets/js/errorHandling.js', array() );
            global $wp_scripts;
        }

        /**
         * Enqueue User access settings view to dashboard
         * 
         * function passed to 'admin_enqueue_scripts' action hook
         * 
         * @return void
         */
        public function enqueue_settings_scripts($page)
        {
            // let's check and make sure we're on the dashboard page.
            if( isset( $page ) && $page === "toplevel_page_ninja-forms") {
                global $wp_version;
                //Enqueue Settings element for dashboard
                if( $wp_version >= "5.4" ){
                    //Get Dependencies and Version from build asset.php generated by wp-scripts
                    $settings_asset_php = [
                        "dependencies" => ['jquery'],
                        "version"   => false
                    ];
                    if( file_exists( self::$dir . "build/settings.asset.php" ) ){
                        $asset_php = include( self::$dir . "build/settings.asset.php" );
                        $settings_asset_php["dependencies"] = array_merge( $settings_asset_php["dependencies"], $asset_php["dependencies"]);
                        $settings_asset_php["version"] = $asset_php["version"];
                    }
                    //Get JS settings assets details
                    if( file_exists( self::$dir . "build/settings.scss.asset.php" ) ){
                        $asset_scss = include( self::$dir . "build/settings.scss.asset.php" );
                    }
                    $settings_asset_scss_version = isset($asset_scss) ? $asset_scss["version"] : self::VERSION;
                    //Register Settings script
                    wp_register_script( 'nf_user_management_settings', self::$url . 'build/settings.js',  $settings_asset_php["dependencies"], $settings_asset_php["version"] );
                    wp_enqueue_script( 'nf_user_management_settings' );
                    wp_set_script_translations( "nf_user_management_settings", "ninja-forms-user-management", self::$dir . 'lang' );
                    //Enqueue settings style
                    wp_enqueue_style( 'nf_user_management_settings_style', self::$url . 'build/settings.scss.css',  ['wp-edit-blocks'], $settings_asset_scss_version );
    
                    //Prepare data for User Access settings
                    $roles_array = self::format_roles_array();
                    $roles_menu = self::get_settings_options_status();
                    $display_status = self::get_display_settings_status();
                    $user_access_settings = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');
                    wp_localize_script('nf_user_management_settings', 'nf_user_management_data', [
                        'siteUrl'       =>  esc_url_raw( site_url() ),
                        'adminUrl'      =>  esc_url_raw( admin_url() ),
                        'restUrl'       =>  esc_url_raw( get_rest_url() ),
                        'ajaxUrl'       =>  esc_url_raw( admin_url( 'admin-ajax.php' ) ),
                        'token'         =>  wp_create_nonce( 'wp_rest' ),
                        'settings'      =>  $user_access_settings,
                        'roles'         =>  $roles_array,
                        'roles_menu'    =>  $roles_menu,
                        'display_status'    => $display_status
                    ]);
                    
                }
    
            }
        
        }

        /**
         * Format roles array for settings select fields
         * 
         * @return array $roles_array
         */
        public function format_roles_array(){
            $roles_array[] = [ 
                "label"     => __("Select Role", "ninja-forms-user-management"),
                "value"     => false
            ];
            $roles = wp_roles();
            if(isset($roles->role_names)){
                foreach($roles->role_names as $slug => $name){
                    if($slug !== "administrator"){
                        $roles_array[] = [ 
                            "label"     => $name,
                            "value"     => $slug
                        ];
                    }
                }
            }

            return $roles_array;
        }
        
        /**
         * Fetch the roles status in the select fields of Dashboard settings
         * 
         * @return array of arrays of each setting type status
         * 
         */
        public function get_settings_options_status() {

            $roles_array = self::format_roles_array();
            $settings_types = ["view_own_submissions","view_others_submissions", "edit_own_submissions", "edit_others_submissions"];
            //Create array for status of each setting type
            if(!empty($roles_array )){
                $roles_menu = [];
                foreach($settings_types as $type){
                    $roles_menu[$type] = $roles_array;
                }
            }
            
            $user_access_settings = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');
            $user_access_settings_array = json_decode($user_access_settings, true);
            if(!empty( $user_access_settings_array) && isset($roles_menu)){
                //Mark already selected roles as disabled for select field
                foreach($roles_menu as $type => $array){
                    foreach($array as $i => $in_array){
                        $roles_menu[$type][$i]["disabled"] = in_array( $in_array["value"], $user_access_settings_array[$type] );
                    }
                }
            }

            return $roles_menu;
        }

        /**
         * Format display settings status
         * 
         * @return array of roles to display status
         */
        public function get_display_settings_status(){
            
            $roles_array = self::format_roles_array();
            $settings_types = ["view_own_submissions","view_others_submissions", "edit_own_submissions", "edit_others_submissions"];
            $roles_display = [];
            foreach($settings_types as $type){
                $roles_display[$type] = [];
            }
            $option = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');
            $user_access_settings_array = json_decode($option, true);
            if(!empty($user_access_settings_array)){
                foreach($user_access_settings_array as $type => $roles_set){
                    foreach($roles_array as $role_element){
                        if(in_array($role_element["value"], $roles_set)){
                            if("view" === substr($type, 0, 4)){
                                if("own" === substr($type, 5, 3) && in_array($role_element["value"], $user_access_settings_array["edit_own_submissions"])){
                                    $role_element["disabled"] = true;
                                }
                                if("others" === substr($type, 5, 6) && in_array($role_element["value"], $user_access_settings_array["edit_others_submissions"])){
                                    $role_element["disabled"] = true;
                                }
                            }
                            if("own" === substr($type, 5, 3)){
                                if("view" === substr($type, 0, 4) && in_array($role_element["value"], $user_access_settings_array["view_others_submissions"])){
                                    $role_element["disabled"] = true;
                                }
                                if("edit" === substr($type, 0, 4) && in_array($role_element["value"], $user_access_settings_array["edit_others_submissions"])){
                                    $role_element["disabled"] = true;
                                }
                            }
                                
                            $roles_display[$type][] = $role_element;
                        }
                    }
                }
            }

            return $roles_display;
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
         * Load an autoloader from vendor subdirectory
         *
         */
        public static function vendorAutoloader(): bool
        {
            $autoloader = dirname(__FILE__) . '/vendor/autoload.php';

            if (file_exists($autoloader)) {
                include_once $autoloader;
                $return = true;
            } else {
                $return = false;
            }
            return $return;
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


        /**
         * Add a menu item on main Ninja Forms dashboard using ninja_forms_dashboard_menu_items filter hooks
         * 
         * the filter hooks brings $items already listed for the dashboard menu
         *  
         * @return array $items
         */
        public function add_dashboard_menu_item($items){

            $items['user_access'] = [
                'slug' => 'user-access',
                'niceName' => esc_html__( 'User access', 'ninja-forms' )
            ];

            return $items;
        }

        /**
         * Add a menu item on main Ninja Forms dashboard using ninja_forms_dashboard_menu_items filter hooks
         * 
         *  !!! The filter hooks brings $localized_data already passed via core, merge data to incoming array, don't forget to return incoming data along the data needed here  !!!
         *  
         * @return array $localized_data
         */
        public function submissions_view_add_localized_data( $localized_data )
        {
            $localized_data['nf_user_management_data_access'] = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');
            $localized_data['nf_user_management_current_user'] = wp_get_current_user();

            return $localized_data;
        }

        /**
         * Prepare routes to handle User management dashboard settings
         */
        public function nf_user_management_submissions_access_settings() {
            register_rest_route( 'nf-user-management', '/save-submissions-access-settings', [
                'methods' => 'POST',
                'args' => [
                    'settings' => [
                        'required' => true,
                        'description' => esc_attr__('Save Settings', 'ninja-forms-user-management'),
                        'type' => 'string',
                        'validate_callback' => 'rest_validate_request_arg',
                    ],           
                ],
                'callback' => [ $this, 'save_submissions_access_settings' ],
                // Uses the same permissions as the `download-all` request
                'permission_callback' => [ $this, 'save_submissions_access_settings_permission_callback' ],
                ] 
            );

            register_rest_route( 'nf-user-management', '/get-submissions-access-settings-meta', [
                'methods' => 'GET',
                'callback' => [ $this, 'get_submissions_access_settings_meta' ],
                // Uses the same permissions as the `download-all` request
                'permission_callback' => [ $this, 'get_submissions_access_settings_permission_callback' ],
                ] 
            );

        }

        /**
         * Prepare routes to handle User management dashboard settings
         */
        public function save_submissions_access_settings_permission_callback(WP_REST_Request $request) {
            //Set default to false
            $allowed = false;

            // Allow only admin to export personally identifiable data
            $permissionLevel = 'manage_options';  
            $allowed = \current_user_can($permissionLevel);
            
            /**
             * Filter permissions for Reading Submissions
            *
            * @param bool $allowed Is request authorized?
            * @param WP_REST_Request $request The current request
            */
            return apply_filters( 'ninja_forms_user_management_api_allow_save_submissions_access_settings', $allowed, $request );
        }

        /**
         * Prepare routes to get settings for User management dashboard
         */
        public function get_submissions_access_settings_permission_callback(WP_REST_Request $request) {
            //Set default to false
            $allowed = false;

            $permissionLevel = 'manage_options';  
            $allowed = \current_user_can($permissionLevel);
            
            /**
             * Filter permissions for Getting submissions user access
            *
            * @param bool $allowed Is request authorized?
            * @param WP_REST_Request $request The current request
            */
            return apply_filters( 'ninja_forms_user_management_api_allow_get_submissions_access_settings', $allowed, $request );
        }

        /**
         * Save User access to submissions settings
         */
        public function save_submissions_access_settings(WP_REST_Request $request) {
            //Extract required data
            $data = $request->get_json_params();
            $settings = $data['settings'];

            //Get data stored and create the new vamue for the correct setting
            $option = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');

            $response = (object)[];
            if ( $option ) {
                // option exist
                if ( $settings === $option ) {
                    $response->message = "unchanged";
                    $response->status = true;
                } else {
                    \Ninja_Forms()->update_setting('nf_user_management_data_access', $settings);
                    $response->message = "update_option";
                    $response->status = true;
                    
                }
            } else {
                \Ninja_Forms()->update_setting('nf_user_management_data_access', $settings);
                // option don't exist
                $response->message = "add_option";
                $response->status = true;
            }

            return rest_ensure_response( json_encode( $response ) );
        }

        /**
         * Save User access to submissions settings
         */
        public function get_submissions_access_settings_meta(WP_REST_Request $request) {

            //Get data stored and create the new vamue for the correct setting
            $option = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');

            $settings_select_options_status = self::get_settings_options_status();
            $display_settings_status = self::get_display_settings_status();

            $return = [
                'settings'   => $option,
                'select_options_status' => $settings_select_options_status,
                'display_settings_status' => $display_settings_status
            ];

            return rest_ensure_response( json_encode( $return) );
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
