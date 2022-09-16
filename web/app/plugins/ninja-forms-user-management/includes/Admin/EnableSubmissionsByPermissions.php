<?php

namespace NinjaForms\UserManagement\Admin;

use NinjaForms\UserManagement\Contracts\UsersSubmissionsPermissions;

use NinjaForms\UserManagement\Handlers\DeterminePermissionsByRole;
use NinjaForms\UserManagement\Handlers\RetrieveRoleBasedPermissionSettingsFromNfSettings;

/**
 * Conditionally display and filter NF submissions per permissions
 *
 * Permissions establish if given user can: view own submissions, view/edit own
 * submissions, view other's submissions, view/edit other's submissions
 */
class EnableSubmissionsByPermissions
{
    /** @var UsersSubmissionsPermissions */
    protected $usersSubmissionsPermissions;

    /**
     * Capability for current user
     *
     * @var string
     */
    protected $userCapability;

    public function __construct()
    {
        $this->usersSubmissionsPermissions = new DeterminePermissionsByRole( new RetrieveRoleBasedPermissionSettingsFromNfSettings());

        $this->conditionallyDisplaySubmissionsPage();

        $this->conditionallyLimitSubmissionsAccess();

        $this->conditionallyEnableGlobalSubmissionEditing();
    }

    /**
     * Display submissions page if user is allowed to view own or other's
     *
     * @return void
     */
    protected function conditionallyDisplaySubmissionsPage(): void
    {
        if (
            $this->usersSubmissionsPermissions->viewOthersSubmissions()
            || $this->usersSubmissionsPermissions->viewOwnSubmissions()
        ) {
            $this->addFilterSubmissionsPageDisplay();
        }
    }

    /**
     * Display core's submission page for given user
     *
     * @return void
     */
    protected function addFilterSubmissionsPageDisplay(): void
    {
        add_filter('ninja_forms_admin_submissions_capabilities',   [$this, 'getUserCapability']); // Submissions Submenu

        add_filter('ninja_forms_api_allow_get_submissions', '__return_true', 10, 2);

        add_filter('ninja_forms_api_allow_handle_extra_submission',  '__return_true', 10, 2);

        add_filter('ninja_forms_api_allow_email_action',  '__return_true', 10, 2);
    }

    /**
     * Return capability for the current user
     *
     * @return string
     */
    public function getUserCapability(): string
    {
        if (!isset($this->userCapability)) {
            $this->determineUserCapability();
        }

        return $this->userCapability;
    }

    /**
     * Determine a capability for the current user
     *
     * @return void
     */
    protected function determineUserCapability(): void
    {
        $capability = '';

        $user = \wp_get_current_user();

        $capabilities = $user->allcaps;

        if (
            \is_array($capabilities)
            && 0 < count(\array_keys($capabilities))
        ) {
            $capabilityKeys = \array_keys($capabilities);
            $capability = $capabilityKeys[0];
        }

        $this->userCapability = $capability;
    }

    /**
     * If user is not permitted to view other's submission, add SubmissionFilter
     *
     * @return void
     */
    protected function conditionallyLimitSubmissionsAccess(): void
    {
        if (!$this->usersSubmissionsPermissions->viewOthersSubmissions()) {
            $this->addFilterLimitSubmissionsToUser();
        }
    }

    /**
     * Add filter to core limiting submissions to current user
     *
     * @return void
     */
    protected function addFilterLimitSubmissionsToUser(): void
    {
        add_filter('ninja_forms_limit_submissions_to_logged_in_user', '__return_true');
    }

    /**
     * Enables route permission to edit submission
     *
     * NOTE: if user is permitted to edit own submissions but only view other's
     * submissions, that logic is handled outside of this global permission
     *
     * @return void
     */
    protected function conditionallyEnableGlobalSubmissionEditing(): void
    {
        if (
            $this->usersSubmissionsPermissions->editOthersSubmissions()
            || $this->usersSubmissionsPermissions->editOwnSubmissions()
            || $this->isAdministrator()
        ) {
            add_filter('ninja_forms_api_allow_update_submission', '__return_true', 10, 2);
            add_filter('ninja_forms_api_allow_delete_submissions', '__return_true', 10, 2);
        } else {
            add_filter('ninja_forms_api_allow_update_submission',  '__return_false', 10, 2);
            add_filter('ninja_forms_api_allow_delete_submissions', '__return_false', 10, 2);
        }
    }

    /**
     * Check if user is administrator
     *
     * @return boolean true is administrator
     */
    protected function isAdministrator(): bool
    {
        $current_user = wp_get_current_user();
        $isAdministrator = in_array("administrator", $current_user->roles);

        return $isAdministrator;
    }
   
}
