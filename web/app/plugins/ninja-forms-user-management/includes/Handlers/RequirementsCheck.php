<?php 
namespace NinjaForms\UserManagement\Handlers;

/**
 * Checks system and version requirements for usage
 */
class RequirementsCheck{


    /**
     * Ensure that NF core can limit submissions by user
     *
     * This is a key requirement for providing user-level access to submissions.
     * Without this functionality, we cannot allow access to submissions page
     * because we cannot limit users to their own submissions.
     *
     * @return boolean
     */
    public function canLimitSubmissionsByUser( ): bool
    {
        // If submission filter method exists
        $return = $this->doesSubmissionFilterFactoryMethodExist();

        // Also do a version check to be sure
        if($return){
            $return = $this->coreVersionNumberCheck();
        }

        return $return;
    }


    /**
     * Check if the SubmissionFilter factory with user limit method exists
     *
     * @return boolean
     */
    protected function doesSubmissionFilterFactoryMethodExist( ): bool
    {
        $requiredClass = '\\NinjaForms\\Includes\\Factories\\SubmissionFilterFactory';
        $requiredMethod = 'maybeLimitByLoggedInUser';

        $return = \method_exists($requiredClass,$requiredMethod);
        
        return $return;
    }

    /**
     * Ensure version number of core is allowed
     *
     * @return boolean
     */
    protected function coreVersionNumberCheck( ): bool
    {
        $return = false;

        if(\class_exists('Ninja_Forms')){

            if(\version_compare(\Ninja_Forms::VERSION,'3.6.13', '>=') || '3.0-versionNumberPlaceholder'==\Ninja_Forms::VERSION){

                $return = true;
            }
        }

        return $return;
    }

    /**
     * Ensure that the User Access settings tab is available
     *
     * The React component will override core's default value with the User
     * Management functionality; this boolean ensures that the element is
     * available to prevent a failure.
     *
     * @return boolean
     */
    public function isUserAccessSettingsDivAvailable( ): bool
    {
        $return = false;

        // @todo Add check for the required DOM element for the React component insertion

        return $return;
    }

}