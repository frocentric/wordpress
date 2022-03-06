<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class NF_UserManagement_Actions_UserRegistration
 */
final class NF_UserManagement_Actions_RegisterUser extends NF_Abstracts_Action
{
    /**
     * @var string
     */
    protected $_name  = 'register-user';

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_timing = 'early';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Register User', 'ninja-forms-user-management' );

        add_action( 'admin_init', array( $this, 'init_settings' ) );

        add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );

        // Halts form rendering and shows logout message.
        add_filter( 'ninja_forms_display_show_form', array( $this, 'logout_message' ), 10, 3 );
    }

    /*
    * PUBLIC METHODS
    */

    /**
     * Logout Message
     *
     * Callback method for the ninja_forms_display_show_form filter.
     *
     * @param $boolean
     * @param $form_id
     * @param $form
     * @return bool
     */
    public function logout_message( $boolean, $form_id, $form )
    {
        //Checks if filter has been set false anywhere else.
        if( ! $boolean ) return false;

        //Get all actions then loop over them.
        $actions = Ninja_Forms()->form( $form_id )->get_actions();
        foreach( $actions as $action ) {

            //Checks if user is logged in and if and active register-user action exists.
            //Includes bypass for preview.
            if ( is_user_logged_in()
                 && 'register-user' == $action->get_setting( 'type' )
                 && intval( $action->get_setting( 'active' ) )
                 && ! isset( $_GET[ 'nf_preview_form' ] ) ) {

                //Echoes a logout link to the page.
                echo '<a href="' . wp_logout_url( get_permalink() ) . '">' .
                    __( 'Please logout to view this form.', 'ninja-forms-user-management' ) .
                    '</a>';
                return false;
            }
        }
        return true;
    }

    /**
     * Init Settings
     *
     * Adds config file to action settings.
     */
    public function init_settings()
    {
        $settings = NF_UserManagement::config( 'ActionRegisterUserSettings' );

        $roles = NF_UserManagement()->get_user_roles();

        foreach( $roles as $key => $value ) {
            $settings[ 'role' ][ 'options' ][] = array(
                'label' => $value, 'value' => $key
            );
        }


        $this->_settings = array_merge( $this->_settings, $settings );
    }

    /**
     * Builder Template
     *
     * Gets custom meta repeater template.
     */
    public function builder_templates()
    {
        NF_UserManagement::template( 'custom-meta-repeater-row.html.php' );
    }

    /**
     * Save
     *
     * @param $action_settings
     */
    public function save( $action_settings )
    {

    }

    /**
     * Action Processing.
     *
     * Registers user upon form submission.
     *
     * @param $action_settings
     * @param $form_id
     * @param $data
     * @return mixed
     */
    public function process( $action_settings, $form_id, $data )
    {
        //Setting up array to send user info to WordPress
        $user_data = array(
            'user_login'    => $action_settings[ 'username' ],
            'user_email'    => $action_settings[ 'email' ],
            'first_name'    => $action_settings[ 'first_name' ],
            'last_name'     => $action_settings[ 'last_name' ],
            'user_url'      => $action_settings[ 'url' ],
            'role'          => $action_settings[ 'role' ],
            'user_pass'     => $action_settings[ 'password' ],
        );

        if( ! strpos( $_SERVER[ 'HTTP_REFERER' ], 'nf_preview_form=' ) ) {
	        //Checks to see if username or email exists.
	        $user_name  = username_exists( $user_data['user_login'] );
	        $user_email = email_exists( $user_data['user_email'] );

	        //Get the login action settings for the current form.
	        $action = Ninja_Forms()->form( $form_id )->get_action( $action_settings['id'] );

	        //Get the username and password settings.
	        $setting_key = NF_UserManagement()->strip_merge_tags( $action->get_settings( 'username', 'email' ) );

	        //Get the fields for the current form.
	        $fields = Ninja_Forms()->form( $form_id )->get_fields();

	        //Gets a key/value pair in the form of action_setting => field_id
	        $field_id = NF_UserManagement()->get_field_id( $fields, $setting_key );

	        //Ensure username and email address aren't taken.
	        if ( ! $user_name && ! $user_email ) {

		        //Send the user's data to WordPress DB.
		        $user_id = wp_insert_user( $user_data );

		        // If register_user_email setting is active, sends the user
		        if ( 1 == $action_settings['register_user_email'] ) {
			        wp_new_user_notification( $user_id, null, 'user' );
		        }

		        //logs user in if login user upon registration setting is in use.
		        if ( 1 == $action_settings['login_user_upon_registration'] ) {
			        wp_set_current_user( $user_id, $user_data['user_login'] );
			        wp_set_auth_cookie( $user_id );
			        do_action( 'wp_login', $user_data['user_login'], get_user_by( 'ID', $user_id ) );
		        }

		        //Register our custom meta.
		        $custom_meta = $this->register_custom_meta( $action_settings, $user_id );

		        //If custom meta is present, we assign it to a variable.
		        if ( ! empty( $custom_meta ) ) {
			        $data['actions']['user_management']['custom_meta'] = $custom_meta;
		        }

		        //Error handling for email address being taken.
	        } elseif ( email_exists( $user_data['user_email'] ) || username_exists( $user_data['user_login'] ) ) {
		        $data['errors']['fields'][ $field_id['email'] ] = array(
			        'message' => __( 'This email address is already in use, please use a different email.', 'ninja-forms-user-management' ),
			        'slug'    => 'user-management',
		        );

		        return $data;

		        //Error handling for username being taken.
	        } elseif ( username_exists( $user_data['user_login'] ) ) {
		        $data['errors']['fields'][ $field_id['username'] ] = array(
			        'message' => __( 'This username is taken, please use another.', 'ninja-forms-user-management' ),
			        'slug'    => 'user-management',
		        );

		        return $data;
	        }
        }

        // If both the login user and refresh upon registration setting are on then...
        if( 1 == $data[ 'actions' ][ 'login_user_upon_registration' ]
            && 1 == $data[ 'actions' ][ 'refresh_upon_registration' ] ) {
            // ...reload the page upon submission...
            $data[ 'actions' ][ 'redirect' ] = wp_get_referer();
            // ...and unset any success messages.
            unset( $data[ 'actions' ][ 'success_msg' ] );
        }

        return $data;
    }

    /**
     * Register Custom Meta
     *
     * Checks for custom meta, then processes if user meta exists.
     *
     * @param $action_settings
     * @param $user_id
     */
    private function register_custom_meta( $action_settings, $user_id )
    {
        if( ! empty( $action_settings[ 'custom_meta' ] ) ) {
            foreach ( $action_settings[ 'custom_meta' ] as $custom_meta ) {
                add_user_meta( $user_id, $custom_meta[ 'key' ], $custom_meta[ 'value' ] );
            }
        }
    }
}
