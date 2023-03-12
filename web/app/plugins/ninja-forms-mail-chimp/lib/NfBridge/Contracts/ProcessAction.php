<?php
namespace  NFMailchimp\EmailCRM\NfBridge\Contracts;
use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataDataHandler;
use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler;

interface ProcessAction
{

    /**
     * Given submission processing objects, processes the action
     *
     * @param ActionSettingsDataHandler $actionSettingsDataHandler
     * @param string $formId
     * @param SubmissionDataDataHandler $submissionDataDataHandler
     * @return SubmissionDataDataHandler data handler after processing
     */
    public function process(
        ActionSettingsDataHandler $actionSettingsDataHandler,
        string $formId,
        SubmissionDataDataHandler $submissionDataDataHandler,
        array $runtimeData
    ): SubmissionDataDataHandler;
}
