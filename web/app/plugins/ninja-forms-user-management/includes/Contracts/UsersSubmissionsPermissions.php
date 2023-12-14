<?php 
namespace NinjaForms\UserManagement\Contracts;

/**
 * Interface for classes evaluating user's submissions permissions
 *
 * Required methods determining if user can: view own submissions, view/edit own
 * submissions, view other's submissions, view/edit other's submissions
 */
interface UsersSubmissionsPermissions{


    /**
     * Can the logged in user view their own submissions
     *
     * @return boolean
     */
    public function viewOwnSubmissions( ): bool;
    
    /**
     * Can the logged in user edit their own submissions
     *
     * @return boolean
     */
    public function editOwnSubmissions( ): bool;
    
    /**
     * Can the logged in user view other's submissions
     *
     * @return boolean
     */
    public function viewOthersSubmissions(): bool;

    /**
     * Can the logged in user edit other's submissions
     *
     * @return boolean
     */
    public function editOthersSubmissions(): bool;

    /**
     * Get roles settings
     *
     * @return RetrieveRoleBasedPermissionSettings
     */
    public function retrieveRoleBasedPermissionSettings(): RetrieveRoleBasedPermissionSettings;
}
