<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class NF_UserManagement_Actions_UpdateProfile
 */
final class NF_UserManagement_Actions_UpdateProfile extends NF_Abstracts_Action
{
    /**
     * @var string
     */
    protected $_name  = 'update-profile';

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
     * lookup @var array
     *
     * is used to build the array of meta data based on what action_settings are in use.
     */
    protected $_lookup = array(
        'nickname'      => 'username_nickname',
        'user_email'    => 'email',
        'first_name'    => 'first_name',
        'last_name'     => 'last_name',
        'user_url'      => 'url',
        'user_pass'     => 'password',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->filterTimingPriority();

        $this->_nicename = __( 'Update Profile', 'ninja-forms-user-management' );

        add_action( 'admin_init', array( $this, 'init_settings' ) );

        add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );

        //Stops for display if user isn't logged in.
        add_filter( 'ninja_forms_display_show_form', array( $this, 'login_message' ), 10, 3 );
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

        $filteredTiming = apply_filters('nf_user_management_update_profile_timing', $defaultTiming);

        // Ensure only valid timing values are used
        // If not, then fallback to default
        if (in_array($filteredTiming, ['early', 'normal', 'late'])) {
            $this->_timing = $filteredTiming;
        }

        $defaultPriority = '10';

        $filteredPriority = apply_filters('nf_user_management_update_profile_priority', $defaultPriority);

        // Ensure only valid priority values are used
        // If not, then fallback to default
        // must be string value of an integer
        if (is_string($filteredPriority) && (int)$filteredPriority==(string)$filteredPriority) {
            $this->_priority = $filteredPriority;
        } 
    }

    /*
    * PUBLIC METHODS
    */

    /**
     * Login Message
     *
     * Call back for the ninja_forms_display_show_form filter.
     * Stops the forms from being displayed if user is not logged in.
     *
     * @param $boolean
     * @param $form_id
     * @param $form
     * @return bool
     */
    public function login_message( $boolean, $form_id, $form )
    {
        //Checks if filter has been set false anywhere else.
        if( ! $boolean ) return false;

        //Loops over forms to get actions.
        $actions = Ninja_Forms()->form( $form_id )->get_actions();
        foreach( $actions as $action ) {
            //Checks if user is logged in and if an active update-profile action is on the form.
            if ( ! is_user_logged_in()
                 && 'update-profile' == $action->get_setting( 'type' )
                 && intval( $action->get_setting( 'active' ) ) ) {
                echo $form->get_setting( 'not_logged_in_msg' );
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
        $settings = NF_UserManagement::config( 'ActionUpdateProfileSettings' );

        $roles = NF_UserManagement()->get_user_roles();

        foreach( $roles as $key => $value ) {
            $settings[ 'role' ][ 'options' ][] = array(
                'label' => $value, 'value' => $key
            );
        }

        $this->_settings = array_merge( $this->_settings, $settings );
        
    }

    /**
     * Builder Templates
     *
     * Gets custom meta repeater template.
     */
    public function builder_templates()
    {
        NF_UserManagement::template( 'custom-meta-repeater-row.html.php' );
    }

    public function save( $action_settings )
    {

    }

    /**
     * Action Processing.
     *
     * Updates user profile upon submission.
     *
     * @param $action_settings
     * @param $form_id
     * @param $data
     * @return mixed
     */
    public function process( $action_settings, $form_id, $data )
    {
        //gets the current users ID.
        $user_id = get_current_user_id();

        //Builds an array of the user meta based off the look up table property.
        $user_meta = array();
        foreach( $this->_lookup as $key => $value ) {
            if( isset($action_settings[ $value ]) ) {
                $user_meta[ $key ] = $action_settings[ $value ];
            }
        }

        //Get the login action settings for the current form.
        $action = Ninja_Forms()->form( $form_id )->get_action( $action_settings[ 'id' ] );

        //Get the username and email settings.
        $settings = NF_UserManagement()->strip_merge_tags( $action->get_settings( 'username_nickname', 'email' ) );

        //Get the fields for the current form.
        $fields = Ninja_Forms()->form( $form_id )->get_fields();

        //Gets a key/value pair in the form of action_setting => field_id
        $field_id = NF_UserManagement()->get_field_id( $fields, $settings );

        //Get user meta parameters to use as comparators in a check.
        $user_data      = get_userdata( $user_id );
        $user_nickname  = get_user_meta( $user_id, 'nickname' );

        /*
         * TODO: Refactor and simplify this logic.
         *
         * Check to see if email address is in use, if it is throw an error on the field the setting is mapped to.
         */
        if(isset($user_meta[ 'user_email' ] )&& email_exists( $user_meta[ 'user_email' ] ) && $user_meta[ 'user_email' ] != $user_data->user_email ) {
            //If email address is used in as nickname setting and the email address is take throw error.
            if( isset( $field_id[ 'username_nickname' ] ) ) {
                $data[ 'errors' ][ 'fields' ][ $field_id[ 'username_nickname' ] ] = array(
                    'message'   => __( 'This email address is in use. Please try a different email address.',
                        'ninja-forms-user-management' ),
                    'slug'      => 'user-management'
                );
                //otherwise if the email address is already used throw error.
            } else {
                $data[ 'errors' ][ 'fields' ][ $field_id[ 'email' ] ] = array(
                    'message' => __( 'This email address is in use. Please try a different email address.',
                        'ninja-forms-user-management' ),
                    'slug' => 'user-management'
                );
            }
        } elseif( username_exists( $user_meta[ 'nickname' ] ) && $user_meta[ 'nickname' ] != $user_nickname[ 0 ] ) {
            $data[ 'errors' ][ 'fields' ][ $field_id[ 'username_nickname' ] ] = array(
                'message'   => __( 'This username is take please try another.', 'ninja-forms-user-management' ),
                'slug'      => 'user-management'
            );
        }

        // If a role was specified...
        if( ! empty( $action_settings[ 'role' ] ) ) {
            // Update the user's role.
            $user_meta[ 'role' ] = $action_settings[ 'role' ];
        }

        $this->update_profile( $user_id, $user_meta );
        
        //Checks to see if custom meta exists and then sent it a helper method for processing
        if( ! empty( $action_settings[ 'custom_meta' ] ) ) {
            $this->process_custom_meta( $user_id, $action_settings[ 'custom_meta' ] );
        }

        return $data;
    }

    /**
     * Update Profile
     *
     * Helper method that updates the users profile
     *
     * @param $user_id
     * @param $user_meta
     */
    public function update_profile( $user_id, $user_meta  )
    {
        //loops over and updates custom meta.
        foreach( $user_meta as $key => $value ) {
            if( isset($key[ 'user_email' ] ) && $key[ 'user_email' ] && ! empty( $value ) ) {
                wp_update_user( array(
                    'ID' => $user_id,
                    $key => $value,
                ) );
            } else {
                update_user_meta( $user_id, $key, $value );
            }
        }
    }

    /**
     * Process Custom Meta
     *
     * Helper method that updates custom meta.
     *
     * @param $user_id
     * @param $user_meta
     */
    public function process_custom_meta( $user_id, $user_meta )
    {
        //loops over and updates custom meta.
        foreach( $user_meta as $meta ) {
            update_user_meta( $user_id, $meta[ 'key' ], $meta[ 'value' ] );
        }
    }
}
