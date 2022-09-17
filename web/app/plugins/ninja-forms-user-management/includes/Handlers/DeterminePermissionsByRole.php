<?php

namespace NinjaForms\UserManagement\Handlers;

use NinjaForms\UserManagement\Contracts\UsersSubmissionsPermissions;
use NinjaForms\UserManagement\Contracts\RetrieveRoleBasedPermissionSettings;

/**
 * Evaluates User's permissions 
 */
class DeterminePermissionsByRole implements UsersSubmissionsPermissions
{

    /** @var  RetrieveRoleBasedPermissionSettings */
    protected $retrieveRoleBasedPermissionSettings;

    /** @var bool */
    protected $viewOwnSubmissions;

    /** @var bool */
    protected $editOwnSubmissions;

    /** @var bool */
    protected $viewOthersSubmissions;

    /** @var bool */
    protected $editOthersSubmissions;
    /**
     * Array of current user roles
     *
     * @var array
     */
    protected $userRoles = [];

    public function __construct(RetrieveRoleBasedPermissionSettings $retrieveRoleBasedPermissionSettings)
    {
        $this->retrieveRoleBasedPermissionSettings = $retrieveRoleBasedPermissionSettings;

        $this->determineCurrentUserRoles();

        $this->determinePermissionsByRoles();
    }

    /**
     * Determine current user's roles
     *
     * @return void
     */
    protected function determineCurrentUserRoles(): void
    {
        $currentUser =  wp_get_current_user();

        $this->userRoles = $currentUser->roles;
    }


    /**
     * Determine which permissions are granted by user's roles
     *
     * @return void
     */
    protected function determinePermissionsByRoles()
    {
        $this->viewOwnSubmissions = count(\array_intersect($this->userRoles, $this->retrieveRoleBasedPermissionSettings->viewOwnRoles())) > 0 ? true : false;
        $this->editOwnSubmissions = count(\array_intersect($this->userRoles, $this->retrieveRoleBasedPermissionSettings->editOwnRoles())) > 0 ? true : false;
        $this->viewOthersSubmissions = count(\array_intersect($this->userRoles, $this->retrieveRoleBasedPermissionSettings->viewOthersRoles())) > 0 ? true : false;
        $this->editOthersSubmissions = count(\array_intersect($this->userRoles, $this->retrieveRoleBasedPermissionSettings->editOthersRoles())) > 0 ? true : false;
    }

    /** @inheritDoc */
    public function viewOwnSubmissions(): bool
    {
        return $this->viewOwnSubmissions;
    }

    /** @inheritDoc     */
    public function editOwnSubmissions(): bool
    {
        return $this->editOwnSubmissions;
    }

    /** @inheritDoc   */
    public function viewOthersSubmissions(): bool
    {
        return $this->viewOthersSubmissions;
    }

    /** @inheritDoc     */
    public function editOthersSubmissions(): bool
    {
        return $this->editOthersSubmissions;
    }
}
