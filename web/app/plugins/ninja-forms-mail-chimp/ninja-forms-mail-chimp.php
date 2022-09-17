<?php
/**
 * Plugin Name: Ninja Forms - Mailchimp 
 * Plugin URI: https://ninjaforms.com/extensions/mail-chimp/
 * Description: Sign up users for your Mailchimp newsletter when submitting Ninja Forms
 * Version: 3.3.2
 * Author: Saturday Drive
 * Author URI: https://ninjaforms.com/
 * Text Domain: ninja-forms-mail-chimp
 *
 * Release Description: Merge branch 'release-3.3.2'
 * Copyright 2016 The WP Ninjas.
 */
/** IMPORTANT: This file MUST be PHP 5.2 compatible */
add_action('plugins_loaded', 'nf_mailchimp_init', 0);

/**
 * Load plugin if possible
 *
 * @since 3.2.0
 */
function nf_mailchimp_init() {
    // Load deprecated version is NF < 3.0
    if (version_compare(get_option('ninja_forms_version', '0.0.0'), '3', '<') || get_option('ninja_forms_load_deprecated', FALSE)) {
        include 'src/deprecated/ninja-forms-mailchimp.php';
        return;
    }

    if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
        if (class_exists('Ninja_Forms')) {
            include_once __DIR__ . '/bootstrap.php';
        } else {
            //Ninja Forms is not active
        }
    } else {
        add_action('admin_notices', 'nf_mailchimp_php_nag');
    }
}

/**
 * Callback for admin notice shown when PHP version is not correct.
 *
 * @since 3.2.0
 */
function nf_mailchimp_php_nag() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            echo esc_html(
                    'Your version of PHP is incompatible with Ninja Forms Mailchimp and can not be used.',
                    'nf-mailchimp'
            );
            printf(
                    ' <a href="https://wordpress.org/php" target="__blank">%s</a>',
                    esc_html__('Learn More', 'nf-mailchimp')
            )
            ?>
        </p>
    </div>
    <?php
}

add_action('admin_init', 'nf_mailchimp_setupLicense');

function nf_mailchimp_setupLicense() {
    if (!class_exists('NF_Extension_Updater'))
        return;

    $name = 'MailChimp';
    $version = '3.3.2';
    $author = 'The WP Ninjas';
    $file = __FILE__;
    $slug = 'mail-chimp';

    new NF_Extension_Updater($name, $version, $author, $file, $slug);
}
