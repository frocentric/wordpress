<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MailChimp_Admin_Settings
 */
final class NF_MailChimp_Admin_Settings
{
    public function __construct()
    {
        add_filter( 'ninja_forms_plugin_settings',                  array( $this, 'plugin_settings'             ), 10, 1 );
        add_filter( 'ninja_forms_plugin_settings_groups',           array( $this, 'plugin_settings_groups'      ), 10, 1 );
        add_filter( 'ninja_forms_check_setting_ninja_forms_mc_api', array( $this, 'validate_ninja_forms_mc_api' ), 10, 1 );
    }

    public function plugin_settings( $settings )
    {
        $settings[ 'mail_chimp' ] = NF_MailChimp()->config( 'PluginSettings' );
        return $settings;
    }

    public function plugin_settings_groups( $groups )
    {
        $groups = array_merge( $groups, NF_MailChimp()->config( 'PluginSettingsGroups' ) );
        return $groups;
    }

    /**
     * Validate Ninja Forms MC API
     * Makes a call to the Mailchimp API to ensure that our credentials are working.
     *
     * @param $setting
     * @return mixed
     */
    public function validate_ninja_forms_mc_api( $setting )
    {
        // Make a call to the Mailchimp API
        $mailchimp = new NF_MailChimp();
        $response = $mailchimp->api_request( 'GET', '/lists' );

        /*
         * Check to make sure we don't have any errors and return our settings if we do.
         */
        if( ! isset( $response->errors )
            && ! empty( $response[ 'response' ][ 'code' ]  )
            && 200 == $response[ 'response' ][ 'code' ]  ) {
            return $setting;
        } else {
            $setting[ 'errors' ][] = __( 'The MailChimp API key you have entered appears to be invalid.', 'ninja-forms-mail-chimp');
        }
        return $setting;
    }

} // End Class NF_MailChimp_Admin_Settings
