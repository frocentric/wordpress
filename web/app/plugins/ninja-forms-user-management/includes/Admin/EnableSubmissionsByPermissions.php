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
        add_filter('ninja_forms_admin_submissions_capabilities', [$this, 'getUserCapability']); // Submissions Submenu

        add_filter('ninja_forms_api_allow_get_submissions', '__return_true', 10, 2);

        add_filter('ninja_forms_api_allow_handle_extra_submission',  '__return_true', 10, 2);

        add_filter('ninja_forms_api_allow_email_action',  '__return_true', 10, 2);

        add_filter('register_post_type_args', [$this, 'set_nf_sub_capabilities'], 10, 2);

        add_action('init', [$this, 'reset_nf_sub_roles_capabilities'], 10);

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

    /**
     * Filter nf_submissions custom post type and set new capability type
     *
     * @return array CPT $args
     */
    public function set_nf_sub_capabilities($args, $post_type)
    {
        if ('nf_sub' === $post_type) {
            $args['map_meta_cap'] = true;
            $args['capability_type'] = 'nf_sub';
            $args['capabilities'] = [
                'read_post' => 'nf_read_sub',
                'read_private_posts' => 'nf_read_private_subs',
                'edit_post' => 'nf_edit_sub',
                'delete_post' => 'nf_delete_sub',
                'edit_posts' => 'nf_edit_subs',
                'delete_posts' => 'nf_delete_subs',
                'delete_posts' => 'nf_delete_subs',
                'edit_others_posts' => 'nf_edit_others_subs',
                'delete_others_posts' => 'nf_delete_others_subs'       
            ];
        }

        return $args;
    }

    /**
     * Sets meta capabilities by role for the CPT access
     *
     * @return void
     */
    public function reset_nf_sub_roles_capabilities()
    {
        $roles = ['editor', 'author', 'contributor', 'subscriber'];
        $rolesSet = $this->usersSubmissionsPermissions->retrieveRoleBasedPermissionSettings()->userManagementSettings;
        foreach($roles as $listed_role){
            $role_obj = get_role( $listed_role );
            if (in_array($listed_role, $rolesSet['view_own_submissions'])) {
                $role_obj->add_cap('nf_read_sub');
            } else {
                $role_obj->remove_cap('nf_read_sub');
            }

            if(in_array($listed_role, $rolesSet['view_others_submissions'])){
                $role_obj->add_cap('nf_read_private_subs');
            } else {
                $role_obj->remove_cap('nf_read_private_subs');
            }
            
            if (in_array($listed_role, $rolesSet['edit_own_submissions'])){
                $role_obj->add_cap('nf_edit_sub');
                $role_obj->add_cap('nf_delete_sub');
                $role_obj->add_cap('nf_edit_subs');
                $role_obj->add_cap('nf_delete_subs');
            } else {
                $role_obj->remove_cap('nf_edit_sub');
                $role_obj->remove_cap('nf_delete_sub');
                $role_obj->remove_cap('nf_edit_subs');
                $role_obj->remove_cap('nf_delete_subs');
            }

            if(in_array($listed_role, $rolesSet['edit_others_submissions'])){
                $role_obj->add_cap('nf_edit_others_subs');
                $role_obj->add_cap('nf_delete_others_subs');
            } else {
                $role_obj->remove_cap('nf_edit_others_subs');
                $role_obj->remove_cap('nf_delete_others_subs');
            }
        }

    }

   
}
