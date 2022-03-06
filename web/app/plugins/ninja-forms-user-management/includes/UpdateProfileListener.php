<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class NF_UserManagement_Actions_UpdateProfileListner
 *
 * Pre-populates user data specified by update-profile action.
 */
class NF_UserManagement_UpdateProfileListener
{

    /**
     * Action Settings
     *
     * An array of the settings from the update-profile action.
     *
     * @var array
     */
    private $_action_settings;

    /**
     * Custom Meta
     *
     * An array of custom meta values from the update-profile action.
     *
     * @var array
     */
    private $_custom_meta = array();

    /**
     * Constructor.
     *
     * @param $action_settings - An array that contains the action settings.
     * @param $custom_meta - An array that contains contains custom meta from action settings.
     */
    public function __construct( $action_settings, $custom_meta )
    {
        //Loops over custom meta and puts a value/key pair of data in the _custom_meta property.
        foreach( $custom_meta as $setting )
        {
            $this->_custom_meta[ $setting[ 'value' ] ] = $setting[ 'key' ];
        }

        //Flips actions_settings to a value/key pair of data and then assigns it to _action_settings property.
        $this->_action_settings = array_flip( $action_settings );

        //Removes any empty strings in _action_settings property array.
        unset( $this->_action_settings[ '' ] );

        //Strips merge tag formatting from _action_settings and _custom_meta.
        $this->_action_settings = $this->strip_merge_tags( $this->_action_settings );
        $this->_custom_meta = $this->strip_merge_tags( $this->_custom_meta );

        //Pre-populates user data to fields.
        add_filter( 'ninja_forms_render_default_value', array( $this, 'pre_populate_field_data' ), 10, 3 );

    }

    /**
     * Pre-populate Field Data
     *
     * Call back method for the ninja_forms_render_default_value filer. Pre-populates user data in fields associated
     * with the action settings.
     *
     * @param $default_value
     * @param $field_type
     * @param $field_settings
     * @return mixed
     */
    public function pre_populate_field_data( $default_value, $field_type, $field_settings )
    {
        //Grabs field key, user id, and the user meta.
        $field_key = $field_settings[ 'key' ];
        $user_id = get_current_user_id();
        $user_meta = get_userdata( $user_id );

        //Checks if the _action_setting and field key match
        if( isset( $this->_action_settings[ $field_key ] ) ){

            //If the case parameter matches pre-populates user data in the associated field.
            switch ( $this->_action_settings[ $field_key ] ) {
                case 'username_nickname' :
                    $default_value = $user_meta->nickname;
                    break;
                case 'email' :
                    $default_value = $user_meta->user_email;
                    break;
                case 'first_name' :
                    $default_value = $user_meta->first_name;
                    break;
                case 'last_name' :
                    $default_value = $user_meta->last_name;
                    break;
                case 'url' :
                    $default_value = $user_meta->user_url;
                    break;
            }
        }

        //Checks if the _custom_meta_ and field key match
        if( isset( $this->_custom_meta[ $field_key ] ) ) {
            //Pre-populates custom meta value in associated field.
            $default_value = get_user_meta( $user_id, $this->_custom_meta[ $field_key ] );
        }

        return $default_value;
    }

    /**
     * Strip Merge Tags
     *
     * Accepts a key/value pair of $action_settings removes the merge tag formatting from the KEY only of each pair.
     *
     * @param $action_settings
     * @return array
     */
    public function strip_merge_tags( $action_settings )
    {
        //Build our return array.
        $fields = array();

        foreach( $action_settings as $key => $value ) {
            //Removes the merge tag formatting
            $field_key = str_replace( '{field:', '', $key );
            $field_key = str_replace( '}', '', $field_key );

            //Builds $fields array for return value.
            $fields[ $field_key ] = $value;
        }
        return $fields;
    }
}