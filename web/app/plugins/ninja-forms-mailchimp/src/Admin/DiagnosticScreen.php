<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Admin;

/**
 * Class NF_Admin_Menus_Licenses
 */
class DiagnosticScreen
{
    
    /** Register hook */
    public function registerHooks(): void
    {
        \add_action('admin_init', [$this, 'addDiagnosticsMetabox'], 12);
    }

    public function addDiagnosticsMetabox()
    {
        \add_meta_box(
            'nf_settings_mailchimpdiagnostics',
            esc_html__('Mailchimp', 'ninja-forms'),
            array($this, 'display'),
            'nf_settings_diagnostics'
        );
    }

    public function display()
    {
        echo 'This is a placeholder for Mailchimp diagnostics';

    }
}
