<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Filters;

class NinjaFormsPluginSettings
{

    /** Register filter */
    public function addFilter(): void
    {
        add_filter('ninja_forms_plugin_settings', array($this, 'pluginSettings'), 10, 1);
    }

    /**
     * Register actions with Ninja Forms
     *
     * @return array
     */
    public function pluginSettings($settings): array
    {
        // Ensure we return the existing collection
        $return = $settings;

        // Push our action, keyed on action key
        $return['mail_chimp'] =
            [
                'ninja_forms_mc_api' => [
                    'id' => 'ninja_forms_mc_api',
                    'label' => 'API Key',
                    'type' => 'textbox'
                ]
            ];

        // Register your additional actions by keying an instatiated object here

        // Ensure you RETURN the array
        return $return;
    }
}
