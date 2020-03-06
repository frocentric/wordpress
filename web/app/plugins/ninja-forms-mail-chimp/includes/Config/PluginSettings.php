<?php if ( ! defined( 'ABSPATH' ) ) exit;

return apply_filters( 'nf_mail_chimp_plugin_settings', array(

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    */

    'ninja_forms_mc_api' => array(
        'id'    => 'ninja_forms_mc_api',
        'type'  => 'textbox',
        'label' => __( 'API Key', 'ninja-forms' ),
        'desc'  => sprintf(
            __( 'Grab your %sAPI Key%s from your MailChimp Account.', 'ninja-forms-mail-chimp' ),
            '<a href="http://kb.mailchimp.com/accounts/management/about-api-keys" target="_blank">', '</a>'
        ),
    ),
));
