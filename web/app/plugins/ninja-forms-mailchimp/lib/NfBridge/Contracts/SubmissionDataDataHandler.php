<?php

namespace  NFMailchimp\EmailCRM\NfBridge\Contracts;

/**
 * Class to provide standard access to the submission data arary
 */
interface SubmissionDataDataHandler
{

    /**
     * Set the $data value from a submission
     *
     * @param array $data
     * @return SubmissionDataDataHandler
     */
    public function setSubmissionData(array $data): SubmissionDataDataHandler;

    /**
     * Set action name used for storing data
     *
     * @param  string  $actionName  Action name used for storing data
     * @return  SubmissionDataDataHandler
     */
    public function setActionKey(string $actionName): SubmissionDataDataHandler;

    /**
     * Return keyed value from data
     *
     * Note that $data is a complex array construct.  You may want to use
     * getFieldValueByFieldKey for submission data
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getValue(string $key, $default = '');

    /**
     * Return a submission value for a given field key on the form
     *
     * @param string $fieldKey
     * @param string $default
     * @return mixed
     */
    public function getFieldValueByFieldKey(string $fieldKey, $default = '');

    /**
     * Push an error message into the error collection
     *
     * @param string $errorMessage
     * @return SubmissionDataDataHandler
     */
    public function pushError(string $errorMessage): SubmissionDataDataHandler;
   
    /**
     * Push response data to the `extra` collection
     *
     * @param mixed $args
     * @return SubmissionDataDataHandler
     */
    public function pushExtra($args): SubmissionDataDataHandler;

    /**
     * Get form $data
     *
     * Used after processing to pass updated submission data to the next action
     *
     * @return array
     */
    public function getData(): array;
}
