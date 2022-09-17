<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class NF_Action_LoginUser
 */
final class NF_UserManagement_Actions_LoginUser extends NF_Abstracts_Action
{
    /**
     * @var string
     */
    protected $_name  = 'login-user';

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_timing = 'normal';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * NF_UserManagement_Actions_LoginUser constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->filterTimingPriority();

        $this->_nicename = __( 'Login User', 'ninja-forms-user-management' );

        //Build out settings for Username and Password Fields.
        $this->_settings[ 'username' ] = array(
            'name' => 'username',
            'type' => 'field-select',
            'label' => __( 'Username', 'ninja-forms-user-management' ),
            'width' => 'full',
            'group' => 'primary',
            'field_types' => array(
                'textbox','email'
            ),
        );

        $this->_settings[ 'password' ] = array(
            'name' => 'password',
            'type' => 'field-select',
            'label' => __( 'Password', 'ninja-forms-user-management'),
            'width' => 'full',
            'group' => 'primary',
            'field_types' => array(
                'password'
            ),
        );

        //Halts form display if user is logged in.
        add_filter( 'ninja_forms_display_show_form', array( $this, 'nf_logout_message' ), 10, 3 );
    }

    
    /**
     * Change action timing per applied filters
     * 
     * Only use allowed timing and priorities
     *
     * @return void
     */
    protected function filterTimingPriority()
    {

        $defaultTiming = 'normal';

        $filteredTiming = apply_filters('nf_user_management_login_user_timing', $defaultTiming);

        // Ensure only valid timing values are used
        // If not, then fallback to default
        if (in_array($filteredTiming, ['early', 'normal', 'late'])) {
            $this->_timing = $filteredTiming;
        }

        $defaultPriority = '10';

        $filteredPriority = apply_filters('nf_user_management_login_user_priority', $defaultPriority);

        // Ensure only valid priority values are used
        // If not, then fallback to default
        // must be string value of an integer
        if (is_string($filteredPriority) && (int)$filteredPriority == (string)$filteredPriority) {
            $this->_priority = $filteredPriority;
        }
    }


    /*
    * PUBLIC METHODS
    */

    /**
     * NF Logout Message
     *
     * Callback method for the ninja_forms_display_show_form.
     * Echoes a link to logout user.
     *
     * @param $boolean
     * @param $form_id
     * @param $form
     * @return bool
     */
    public function nf_logout_message( $boolean, $form_id, $form )
    {
        //Checks if filter has been set false anywhere else.
        if( ! $boolean ) return false;

        //Gets gets actions and loop over all actions.
        $actions = Ninja_Forms()->form( $form_id )->get_actions();
        foreach( $actions as $action ) {

            //Checks to see if user is logged and if login-user action exists on the form.
            if ( is_user_logged_in() && 'login-user' == $action->get_setting( 'type' ) ) {

                //Echoes a link to logout the user.
                echo '<a href="' . wp_logout_url( get_permalink() ) . '">' .
                    __( 'Please logout to view this form.', 'ninja-forms-user-management' ) .
                    '</a>';
                return false;
            }
        }
        return true;
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
     * Action Processing
     *
     * Handles all the processing to log the user in to the site.
     *
     * @param $action_settings
     * @param $form_id
     * @param $data
     * @return mixed
     */
    public function process( $action_settings, $form_id, $data )
    {
        /*
         * If the users site is on HTTPS we want to set the secure
         * login to true, if not leave it as false.
         */
        $secure_cookie_value = false;
        if( 'https' == $_SERVER[ 'REQUEST_SCHEME' ] || 'on' == $_SERVER[ 'HTTPS'] ) {
            $secure_cookie_value = true;
        }

        //Send log in info to WordPress.
        $login = wp_signon(
            array(
                'user_login'    => $action_settings[ 'username' ],
                'user_password' => $action_settings[ 'password' ]
            ),
            $secure_cookie_value
        );

        //Throws error if username or password field is empty.
        if( empty( $action_settings[ 'username' ] ) && empty( $action_settings[ 'password' ] ) ) {
            $data[ 'errors' ][ 'form' ][ 'user-management' ] = __( 'Please input username and password', 'ninja-forms-user-management' );
        }

        //Get the login action settings for the current form.
        $action = Ninja_Forms()->form( $form_id )->get_action( $action_settings[ 'id' ] );

        //Get the username and password settings.
        $settings = NF_UserManagement()->strip_merge_tags( $action->get_settings( 'username', 'password' ) );

        //Get the fields for the current form.
        $fields = Ninja_Forms()->form( $form_id )->get_fields();

        //Gets a key/value pair in the form of action_setting => field_id
        $field_id = NF_UserManagement()->get_field_id( $fields, $settings );

        //Checks for errors in username and password fields and throws field errors.
        if( isset( $login->errors[ 'invalid_username' ] ) ) {
            $data[ 'errors' ][ 'fields' ][ $field_id[ 'username' ] ] = array(
                'message'   => $login->errors[ 'invalid_username' ][ 0 ],
                'slug'      => 'user-management'
            );
        } elseif ( isset( $login->errors[ 'invalid_email' ] ) ) {
            $data[ 'errors' ][ 'fields' ][ $field_id[ 'username' ] ] = array(
                'message' => $login->errors[ 'invalid_email' ][ 0 ],
                'slug'      => 'user-management'
            );
        } elseif ( isset( $login->errors[ 'incorrect_password' ] ) ) {
            $data[ 'errors' ][ 'fields' ][ $field_id[ 'password' ] ] = array(
                'message' => $login->errors[ 'incorrect_password' ][ 0 ],
                'slug'      => 'user-management'
            );
        }

        //Redirects user back to the same form after submission.
        $data[ 'actions' ][ 'redirect' ] = wp_get_referer();

        return $data;
    }
}
