<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Filters;

class NinjaFormsPluginSettingsGroups
{

    /** Register filter */
    public function addFilter(): void
    {
        add_filter('ninja_forms_plugin_settings_groups', array($this, 'pluginSettingsGroups'), 10, 1);
    }

    /**
     * Register actions with Ninja Forms
     *
     * @return array
     */
    public function pluginSettingsGroups($settings): array
    {
        // Ensure we return the existing collection
        $return = $settings;

        // Push our action, keyed on action key
        $return['mail_chimp'] = [
            'id' => 'mail_chimp',
            'label' => 'Mailchimp'
        ];

        // Register your additional actions by keying an instatiated object here

        // Ensure you RETURN the array
        return $return;
    }
}
