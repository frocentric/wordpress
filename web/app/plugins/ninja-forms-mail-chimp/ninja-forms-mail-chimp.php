<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Mail Chimp
 * Plugin URI: https://ninjaforms.com/extensions/mail-chimp/
 * Description: Sign up users for your Mail Chimp newsletter when submitting Ninja Forms
 * Version: 3.1.9
 * Author: The WP Ninjas
 * Author URI: http://wpninjas.com/
 * Text Domain: ninja-forms-mail-chimp
 *
 * Copyright 2016 The WP Ninjas.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    include 'deprecated/ninja-forms-mailchimp.php';

} else {

    if( ! class_exists( 'Mailchimp' ) ) {
        include_once 'includes/Libraries/Mailchimp.php';
    }

    /**
     * Class NF_MailChimp
     */
    final class NF_MailChimp
    {
        const VERSION = '3.1.9';
        const SLUG = 'mail-chimp';
        const NAME = 'MailChimp';
        const AUTHOR = 'The WP Ninjas';
        const PREFIX = 'NF_MailChimp';

        /**
         * @var NF_MailChimp
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
         * @var Mailchimp
         */
        private $_api;

        /**
         * @var Mailchimp API Key.
         */
        private $_three_api;

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_MailChimp Highlander Instance
         */
        public static function instance()
        {

            if ( !isset( self::$instance ) && !( self::$instance instanceof NF_MailChimp ) ) {
                self::$instance = new NF_MailChimp();

                self::$dir = plugin_dir_path( __FILE__ );

                self::$url = plugin_dir_url( __FILE__ );

                spl_autoload_register( array( self::$instance, 'autoloader' ) );

                new NF_MailChimp_Admin_Settings();
            }

            return self::$instance;
        }

        /**
         * NF_MailChimp constructor.
         *
         */
        public function __construct()
        {
            if ( !function_exists( 'curl_version' ) ) {
                add_action( 'admin_notices', array( $this, 'curl_error' ) );
                return false;
            }
            add_action( 'admin_init', array( $this, 'setup_license' ) );
            add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ) );
            add_filter( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );
            add_action( 'ninja_forms_loaded', array( $this, 'ninja_forms_loaded' ) );
            add_filter( 'ninja_forms_new_form_templates', array( $this, 'register_templates' ) );
        }

        public function ninja_forms_loaded()
        {
            new NF_MailChimp_Admin_Metaboxes_Submission();
        }

        /**
         * Register Fields
         *
         * @param array $actions
         * @return array $actions
         */
        public function register_fields( $actions )
        {
            $actions[ 'mailchimp-optin' ] = new NF_MailChimp_Fields_OptIn();

            return $actions;
        }

        /**
         * Register Actions
         *
         * @param array $actions
         * @return array $actions
         */
        public function register_actions( $actions )
        {
            $actions[ 'mailchimp' ] = new NF_MailChimp_Actions_MailChimp();

            return $actions;
        }

        /**
         * Autoloader
         *
         * @param $class_name
         */
        public function autoloader( $class_name )
        {
            if ( class_exists( $class_name ) ) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';

            if ( file_exists( $classes_dir . $class_file ) ) {
                require_once $classes_dir . $class_file;
            }
        }

        /**
         * Setup License
         */
        public function setup_license()
        {
            if ( !class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }

        /*
         * API
         */

        /**
         * Get Lists
         * Grabs all lists from the Mailchimp API and calls helper methodds to
         * populate data for each list.
         *
         * @return array $lists - Key/value pair of values, label, groups, and fields
         *  from the MailChimp API.
         */
        public function get_lists()
        {
            // Set up our lists array.
            $lists = array();

	        // Build our array to send to Mailchimp. Default returns 10
	        // lists, we want more
	        $data = array(
		        'count' => 100,
	        );
            // ...set the response variable to the value coming from the API.
            $response = $this->api_request(
            	'GET',
	            '/lists',
	            $data );

            if( empty( $response ) || is_wp_error( $response ) ) return;

            // Decode the body of the JSON data coming from the api.
            $response = json_decode( $response[ 'body' ] );

            // Loop over the response list data and....
            foreach ( $response->lists as $data ) {
                // Create/update a setting with the the ID and name of the list.
                Ninja_Forms()->update_setting( 'mail_chimp_list_' . $data->id, $data->name );

                // Build the array of lists.
                $lists[] = array(
                    'value' => $data->id,
                    'label' => $data->name,
                    'groups' => $this->get_list_interest_categories( $data->id ),
                    'fields' => $this->get_list_fields( $data->id )
                );
            }

            // Make/update an option every time the list is updated.
            update_option( 'ninja_forms_mailchimp_interests', $lists );
            return $lists;
        }

        /**
         * Get List Fields
         * Gets the fields attached to the Users list in MailChimp.
         *
         * @param $list_id - The id of the list coming from the API.
         * @return array $fields - Returns the fields in a key/value pair.
         */
        public function get_list_fields( $list_id )
        {
            // Get the response from the API.
            $response = $this->api_request( 'GET', '/lists/' . $list_id . '/merge-fields?count=100' );

            // Code the body of the response.
            $response = json_decode( $response[ 'body' ] );

            // Build our
            $fields = array();

            // Email field is required for all new mailing list sign ups,
            // but is not pulled in through the api so we need to build it ourselves.
            $fields[] = array(
                'value' => $response->list_id . '_email_address',
                'label' => 'Email' . ' <small style="color:red">(required)</small>',
            );

            // Loop over the fields and...
            foreach ( $response->merge_fields as $field ) {
                // If the has required text...
                if( true == $field->required ) {
                    // ...add html to apply a required tag.
                    $required_text = ' <small style="color:red">(required)</small>';
                } else {
                    // ...otherwise leave this variable empty.
                    $required_text = '';
                }

                // Build our fields array.
                $fields[] = array(
                    'value' => $field->list_id . '_' . $field->tag,
                    'label' => $field->name . $required_text
                );
            }
            return $fields;
        }

        /**
         * Get List Interest Categories
         * Grabs the Interest Categories and the interests associated with the
         * Interest Categories.
         *
         * @param $list_id
         * @return array
         */
        public function get_list_interest_categories( $list_id )
        {
            // Calls API and stores response, then decodes the body of the response.
            $response = $this->api_request( 'GET', '/lists/' . $list_id . '/interest-categories?count=100' );
            $response = json_decode( $response[ 'body' ] );

            // Set up our interests array.
            $categories = array();

            // Loop over the categories we get back from the API.
            foreach ( $response->categories as $category ) {
                // Gets our interests lists.
                $interests = $this->get_interests( $list_id, $category->id );
                // TODO: Refactor and find a way to pull out nested foreach.
                // Loops over interests and builds interest list.
                foreach( $interests as $interest ) {
                    $categories[] = array(
                      'value' => $list_id . '_group_' . $interest[ 'id' ] . '_' . $interest[ 'name' ],
                      'label' => $interest[ 'name' ],
                    );
                }
            }
            Ninja_Forms()->update_setting( 'nf_mailchimp_categories_' . $list_id, $categories );
            return $categories;
        }

        /**
         * Get Interests
         * Grabs the interest data from the API
         *
         * @param $list_id - This list ID from the API.
         * @param $interest_category_id - The interest category ID from the API
         * @return array $interests - returns name and ID in key value pair.
         *      array(
         *          [ 'name' ]  => 'interest name'
         *          [ 'id' ]    => 'interest ID'
         *       )
         */
        public function get_interests( $list_id, $interest_category_id )
        {
            // Use params to make a request to the API and stores response.
            $response = $this->api_request( 'GET', '/lists/' . $list_id . '/interest-categories/'
                . $interest_category_id . '/interests?count=100' );

            // Decodes the body of the response and create an array for our interests.
            $response = json_decode( $response[ 'body' ] );
            $interests = array();

            // Loop over our interests.
            foreach ( $response->interests as $interest ) {
                // Build our array.
                $interests[] = array(
                    'name' => $interest->name,
                    'id'   => $interest->id
                );
            }
            return $interests;
        }

        /**
         * Subscribe
         * Packages up our user data and subscribes the user to our Mailchimp list.
         *
         * @param $list_id - The Mailchimp List ID we want to subscribe the user to.
         * @param $merge_fields - The field data that's assigned in the user settings.
         * @param $interest_categories - The categories we are sending signing the user up for.
         * @param $email_address - The users email address.
         * @return array|mixed|object - A decoded dump of the requests body.
         */
        public function subscribe( $list_id, $merge_fields, $interest_categories,
	        $email_address, $double_opt_in = 0 )
        {

        	// Check for double opt-in
	        $status = 'subscribed';
	        if ( "1" == $double_opt_in ) {
	        	$status = 'pending';
	        }
	        
            // Build our array to send to Mailchimp.
            $data = array(
                'email_address' => $email_address,
                'status'        => $status,
                'merge_fields'  => $merge_fields,
                'interests'     => $interest_categories,
            );

	        // If don't have merge fields...
	        if( empty( $data[ 'merge_fields' ] ) ) {
	            // Remove them from the array.
	            unset( $data[ 'merge_fields' ] );
            }

	        // Make out email address lowercase and hash it to be used as a part of our api call.
	        $email_hash = strtolower( $email_address );
	        $email_hash = md5( $email_hash );

            // Remove interest categories if we don't have any.
            if( empty( $data[ 'interests' ] ) ) {
                unset( $data[ 'interests' ] );
            }

            // Send our data to Mailchimp.
            $request = $this->api_request( 'PUT', '/lists/' . $list_id . '/members/' . $email_hash,
                         json_encode( $data ) );

            return json_decode( $request[ 'body' ] );
        }

        /**
         * API Key
         * Gets the Mailchimp API key from the settings page, then splits it
         * on the hyphen. So that the api key and data center can be used
         * separately.
         *
         * @return array $api_key
         *  [ 0 ] => mailchimp_api_key
         *  [ 1 ] => data_center_value
         */
        public function api_key()
        {
            // Gets our API key for the settings page.
            $api_key = trim( Ninja_Forms()->get_setting( 'ninja_forms_mc_api' ) );

            if( empty( $api_key ) ) return array();

            // Splits the api key into an array that contains the api key and the data center.
            $api_key = explode( '-', $api_key );

            // Returns the api key.
            return $api_key;
        }

        /**
         * API Request
         * A wrapper for calls to the MailChimp 3.0 API.
         *
         * @param $method   - The request type ie GET or POST.
         * @param $endpoint - The endpoint your calling at MailChimp.
         * @param $body     - Any extra data that is needed to be sent in the call.
         *
         * @return json data - The response that comes back from the request.
         */
        public function api_request( $method, $endpoint, $body = array() )
        {
            // Set the api_key array to a variable.
            $api_key = $this->api_key();

            // If the api key method is empty return an empty array.
            if( empty( $api_key[ 0 ] ) ) return array();
            if( empty( $api_key[ 1 ] ) ) return array();

            // Use WP Remote Request to send/receive data from the MailChimp API
            $response = wp_remote_request(
                'https://' . $api_key[ 1 ] . '.api.mailchimp.com/3.0' . $endpoint,
                array(
                        'method' => $method,
                        'headers' => array(
                            'authorization' => 'apikey ' . $api_key[ 0 ],
                    ),
                        'body' => $body,
                )
            );
            return $response;
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
            $templates[ 'mailchimp-signup' ] = array(
                'id'            => 'mailchimp-signup',
                'title'         => __( 'MailChimp Signup', 'ninja-forms-mail-chimp' ),
                'template-desc' => __( 'Add a user to a list in MailChimp.', 'ninja-forms-mail-chimp' ),
                'form'          => self::form_templates( 'mailchimp-signup.nff' ),
            );

            return $templates;
        }

        /*
         * STATIC METHODS
         */
    
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
         * Load Template File
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
         * Load Config File
         *
         * @param $file_name
         * @return array
         */
        public static function config( $file_name )
        {
            return include self::$dir . 'includes/Config/' . $file_name . '.php';
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
     * @return NF_MailChimp
     */
    function NF_MailChimp()
    {
        return NF_MailChimp::instance();
    }

    NF_MailChimp();
}

add_filter( 'ninja_forms_upgrade_action_mailchimp', 'NF_MailChimp_Upgrade' );
function NF_MailChimp_Upgrade( $action ){

    // newsletter_list
	// if list_id isn't set then just return the action. If there is no list,
	// there can't be merge variables(meta data) or groups
    if( ! isset( $action[ 'list-id' ] ) ) return $action;

    $list_id = $action[ 'list-id' ];
    // Ninja Forms 3.x uses newsletter_list vs list_id
    $action[ 'newsletter_list' ] = $list_id;

    // 93c8c814a4_EMAIL
	// Get the metadata for this action
    if( isset( $action[ 'merge-vars' ] ) ) {
        $merge_vars = maybe_unserialize($action['merge-vars']);
        foreach ($merge_vars as $key => $value) {
        	// metadata is tied to the list here
            $action[$list_id . '_' . $key] = $value;
        }
    }

    //	93c8c814a4_group_8373_Group B
	// Get the group(s) that contacts will be associated with inside the
	// newsletter_list
    if( isset( $action[ 'groups' ] ) ) {
        $groups = maybe_unserialize($action['groups']);
        foreach ($groups as $id => $group) {
            foreach ($group as $key => $name) {
            	// Groups are tied to newsletter_list(list-id)
                $action[$list_id . '_group_' . $id . '_' . $name] = 1;
            }
        }
    }
	// If double opt-in is set in Ninja Forms 2.9, make sure it's carried
	// over into 3.x
    if( isset( $action[ 'double-opt' ] ) ) {
        if ('yes' == $action['double-opt']) {
            $action['double_opt_in'] = 1;
            unset($action['double-opt']);
        }
    }

    return $action;
}
