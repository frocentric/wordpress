<?php

/**
 * This file initializes plugin, once PHP version check passes
 *
 * IMPORTANT, plugins_loaded, priority 1 is the earliest hook you can use.
 */

use NFMailchimp\EmailCRM\Mailchimp\MailchimpApi;
use NFMailchimp\NinjaForms\Mailchimp\Admin\DiagnosticScreen;
/**
 * Load the plugin and initialize container
 *
 * @since 2.0.0
 */
add_action('plugins_loaded', 'ninjaFormsMailchimpBoostrap', 5);

/**
 * Instantiate NinjaFormsMailchimp instance
 */
function ninjaFormsMailchimpBoostrap() {

    $autoloader = dirname(__FILE__) . '/vendor/autoload.php';
    if (file_exists($autoloader)) {
        include_once $autoloader;
    }

    $instance = new \NFMailchimp\NinjaForms\Mailchimp\NinjaFormsMailchimp();

    $mailchimpApi = new MailchimpApi();

    $instance->setMailchimpApi($mailchimpApi);

    /**
     * Runs directly after creation of main plugin instance
     */
    do_action('nf_mailchimp_init', $instance);
}


/**
 * Initialize required functionality for NinjaFormsMailchimp instance
 * 
 * - Add NF Bridge
 * - Initialize REST API endpoints
 * - Add subscribe action
 * - register opt-in field
 * @since 4.0.0
 */
add_action('nf_mailchimp_init', function (\NFMailchimp\NinjaForms\Mailchimp\NinjaFormsMailchimp $instance) {

    // Set the NF Bridge
    setNfBridge($instance);
    // Initialize WordPress REST API endpoints
    add_action('rest_api_init', [$instance, 'initApi']);
    // Register the Subscribe to Mailchimp action
    $instance->addSubscribeAction();
    
    // Adds modal box to autogenerate form
    add_filter('ninja_forms_new_form_templates', array($instance, 'registerAutogenerateModal'));

    add_filter( 'ninja_forms_register_fields', array( $instance, 'registerOptIn' ) );
    add_action( 'ninja_forms_loaded', array( $instance, 'setupAdmin' ) );

    (new DiagnosticScreen())->registerHooks();
}, 2);

/**
 * Bind an NF Bridge to the NinjaFormsMailchimp instance
 * @param \NFMailchimp\NinjaForms\Mailchimp\NinjaFormsMailchimp $instance
 */
function setNfBridge(\NFMailchimp\NinjaForms\Mailchimp\NinjaFormsMailchimp $instance) {

    $nfBridge = (new NFMailchimp\EmailCRM\NfBridge\NfBridge())
            ->registerServices();
    $instance->setNfBridge($nfBridge);
}


