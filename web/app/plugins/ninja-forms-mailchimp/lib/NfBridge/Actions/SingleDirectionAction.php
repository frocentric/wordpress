<?php

namespace  NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler as InterfacesActionSettingsDataHandler;
// use NinjaForms\ReplaceMe\Library\Interfaces\ActionSettingsDataHandler as InterfacesActionSettingsDataHandler;
use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataDataHandler as InterfacesSubmissionDataDataHandler;
// use NinjaForms\ReplaceMe\Library\Interfaces\SubmissionDataDataHandler as InterfacesSubmissionDataDataHandler;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ProcessAction as InterfacesProcessAction;
// use NinjaForms\ReplaceMe\Library\Interfaces\ProcessAction as InterfacesProcessAction;

// use actionsettings
use NinjaForms\ReplaceMe\Library\DataHandlers\ActionSettings;
use NinjaForms\ReplaceMe\Library\DataHandlers\SubmissionDataDataHandler;

use \NF_Abstracts_Action as NfAbstractAction;

abstract class SingleDirectionAction extends NfAbstractAction
{

    /** @var InterfacesActionSettingsDataHandler */
    protected $actionSettingsDataHandler;

    /** @var  InterfacesSubmissionDataDataHandler*/
    protected $submissionDataDataHandler;

    /** @var InterfacesProcessAction */
    protected $processAction;

    /** @inheritDoc */
    public function __construct()
    {
        parent::__construct();

        $this->setActionProperties();
    }

    /**
     * Set your own action properties here
     *
     *   $this->_name = 'replace-me';
     *   $this->_nicename = 'Replace me(translateable)';
     *   $this->_tags = array();
     *   $this->_timing = 'normal';
     *   $this->_priority = '10';
     * @return void
     */
    abstract protected function setActionProperties(): void;

    /** @inheritDoc */
    public function process($actionSettings, $formId, $data): array
    {
        $this->instantiateActionProcessor();

        $actionSettingsDataHandler = (new ActionSettings())->setActionSettings($actionSettings);
        $submissionDataDataHandler = (new SubmissionDataDataHandler())
            ->setSubmissionData($data)
            ->setActionKey($this->getActionKey());

        $processAction = $this->instantiateActionProcessor();

        $processAction->process($actionSettingsDataHandler, $formId, $submissionDataDataHandler);

        $processedData = $submissionDataDataHandler->getData();

        return $processedData;
    }

    /**
     * Instantiate your own Action Processor
     *
     * @return InterfacesProcessAction
     */
    abstract protected function instantiateActionProcessor(): InterfacesProcessAction;

    /**
     * Return the action key used to register action
     *
     * @return string
     */
    public function getActionKey(): string
    {
        return $this->_name;
    }
}
