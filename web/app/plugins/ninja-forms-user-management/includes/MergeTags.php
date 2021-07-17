<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_UserManagement_MergeTags_PasswordReset
 */
final class NF_UserManagement_MergeTags extends NF_Abstracts_MergeTags
{
    protected $id = 'user-management';

    public function __construct()
    {
        parent::__construct();

        $this->title = __( 'User Management', 'ninja-forms-user-management' );

        $this->merge_tags = NF_UserManagement()->config( 'MergeTags' );

    }

    public function __call($name, $arguments)
    {
        // If the mergetag property is not set, then return an empty string.
        return ( isset( $this->$name ) ) ? $this->$name : '';
    }

    public function set( $property, $value )
    {
        $this->$property = $value;
    }

    /**
     * Get Password Reset
     *
     * Callback for the password reset merge tag.
     * Displays password reset link.
     *
     * @return string
     */
    public function get_password_reset()
    {
        //Gets the site URL.
        $site_url = get_site_url();

        //Builds the password reset link.
        $message = 'Reset Password';
        $password_link =  '<a href="' . $site_url . '/wp-login.php?action=lostpassword">'. $message .'</a>';
        apply_filters( 'nf_password_reset', $message );

        return $password_link;
    }

    /**
     * Logout link
     *
     * Displays logout link.
     *
     * @since 3.0.10
     *
     * @return string
     */
    public function logout_link()
    {
        // Build our logout link
        $message = 'Logout';
        $password_link =  '<a href="' . wp_logout_url() . '">'. $message .'</a>';
        apply_filters( 'nf_logout_link', $message );

        return $password_link;
    }

} // END CLASS NF_UserManagement_MergeTags_PasswordReset
