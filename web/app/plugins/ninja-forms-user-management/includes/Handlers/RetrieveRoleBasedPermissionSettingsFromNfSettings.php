<?php

namespace NinjaForms\UserManagement\Handlers;

use NinjaForms\UserManagement\Contracts\RetrieveRoleBasedPermissionSettings;

/**
 * Retrieve role based user management settings from NF Settings
 */
class RetrieveRoleBasedPermissionSettingsFromNfSettings implements RetrieveRoleBasedPermissionSettings
{

    /**
     * Role based user management settings
     *
     * @var array
     */
    public $userManagementSettings;

    /** @inheritDoc */
    public function viewOwnRoles(): array
    {
        $this->populateSettings();
        return $this->userManagementSettings['view_own_submissions'];
    }

    /** @inheritDoc */
    public function editOwnRoles(): array
    {
        $this->populateSettings();
        return $this->userManagementSettings['edit_own_submissions'];
    }

    /** @inheritDoc */
    public function viewOthersRoles(): array
    {
        $this->populateSettings();
        return $this->userManagementSettings['view_others_submissions'];
    }

    /** @inheritDoc */
    public function editOthersRoles(): array
    {
        $this->populateSettings();
        return $this->userManagementSettings['edit_others_submissions'];
    }

    /**
     * Ensure settings property is populated
     *
     * @return void
     */
    protected function populateSettings(): void
    {
        if (!isset($this->userManagementSettings)) {
            $this->retrieveSettingFromNfSettings();
        }
    }

    /**
     * Retrieve settings from NF Settings storage
     *
     * @return void
     */
    protected function retrieveSettingFromNfSettings(): void
    {
        $settingString = $this->callNinjaFormsSetting();

        $rawSettingsArray = \is_string($settingString) ? json_decode($settingString, true) : [];

        if (!\is_array($rawSettingsArray)) {
            $rawSettingsArray = [];
        }

        $this->userManagementSettings = $this->validateSettings($rawSettingsArray);
    }

    /**
     * Call Ninja_Forms to get stored setting
     *
     * @return void
     */
    protected function callNinjaFormsSetting(): string
    {
        $return = \Ninja_Forms()->get_setting('nf_user_management_data_access', '');

        if (!is_string($return)) {
            $return = '';
        }

        return $return;
    }
    /**
     * Ensure each required setting
     *
     * @param array $rawSettings
     * @return array Array of keyed settings, each with array value
     */
    protected function validateSettings(array $rawSettings): array
    {
        $return = [];
        $requiredKeys = [
            'view_own_submissions',
            'view_others_submissions',
            'edit_own_submissions',
            'edit_others_submissions'
        ];

        foreach ($requiredKeys as $requiredKey) {

            if (
                isset($rawSettings[$requiredKey])
                && \is_array($rawSettings[$requiredKey])
            ) {
                $return[$requiredKey] = $rawSettings[$requiredKey];
            } else {
                $return[$requiredKey] = [];
            }
        }

        return $return;
    }
}
