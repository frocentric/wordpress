<?php 
namespace NinjaForms\UserManagement\Contracts;

/**
 * Retrieve stored role based permission settings
 */
interface RetrieveRoleBasedPermissionSettings{

    /**
     * Retrieve roles permitted to view own submissions
     *
     * @return array Indexed array of string roles
     */
    public function viewOwnRoles():array;

    /**
     * Retrieve roles permitted to edit own submissions
     *
     * @return array Indexed array of string roles
     */
    public function editOwnRoles():array;

    /**
     * Retrieve roles permitted to view other's submissions
     *
     * @return array Indexed array of string roles
     */
    public function viewOthersRoles():array;
    
    /**
     * Retrieve roles permitted to edit other's submissions
     *
     * @return array Indexed array of string roles
     */
    public function editOthersRoles():array;

}