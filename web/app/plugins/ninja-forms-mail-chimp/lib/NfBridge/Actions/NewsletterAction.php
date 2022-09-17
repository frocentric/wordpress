<?php

namespace  NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler as InterfacesActionSettingsDataHandler;

use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataDataHandler as InterfacesSubmissionDataDataHandler;

use NFMailchimp\EmailCRM\NfBridge\DataHandlers\ActionSettingsDataHandler;
use NFMailchimp\EmailCRM\NfBridge\DataHandlers\SubmissionDataDataHandler;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ProcessAction as InterfacesProcessAction;
// use NinjaForms\ReplaceMe\Library\Interfaces\ProcessAction as InterfacesProcessAction;



use \NF_Abstracts_ActionNewsletter as NfAbstractActionNewsletter;

abstract class NewsletterAction extends NfAbstractActionNewsletter
{

    /** @var InterfacesActionSettingsDataHandler */
    protected $actionSettingsDataHandler;

    /** @var  InterfacesSubmissionDataDataHandler*/
    protected $submissionDataDataHandler;

    /** @var InterfacesProcessAction */
    protected $processAction;

    /**
     * Keyed array of runtime data passed into ProcessAction object
     *
     * @var array
     */
    protected $runtimeData=[];

    /** @inheritDoc */
    public function __construct()
    {
        $this->setActionProperties();
        parent::__construct();
        $this->setActionSettings();
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

    /**
     * Set your own action properties here
     *
     *   $this->_settings['key']=[actionSettingsArray]
     * @return void
     */
    abstract protected function setActionSettings(): void;

    /**
     * Return array of lists
     *
     * Indexed array of associative arrays
     * 
     *   array keys: 'value'=>(string) 'label'=>(string) 'fields'=>(array) 'groups'=>(array)
     * @return array
     */
    abstract protected function get_lists();

    /** @inheritDoc */
    public function process($actionSettings, $formId, $data): array
    {
        $this->instantiateActionProcessor();

        $actionSettingsDataHandler = (new ActionSettingsDataHandler())->setActionSettings($actionSettings);
        $submissionDataDataHandler = (new SubmissionDataDataHandler())
            ->setSubmissionData($data)
            ->setActionKey($this->getActionKey());

            $this->constructRuntimeData($actionSettings, $formId, $data);
            
        $processAction = $this->instantiateActionProcessor();

        $submissionDataDataHandlerAfterProcessing = $processAction->process($actionSettingsDataHandler, $formId, $submissionDataDataHandler,$this->runtimeData);

        return $submissionDataDataHandlerAfterProcessing->getData();
    }

    /**
     * Instantiate your own Action Processor
     *
     * @return InterfacesProcessAction
     */
    abstract protected function instantiateActionProcessor(): InterfacesProcessAction;

    /**
     * Use raw process data to adjust runtime data passed into ProcessAction
     *
     * @param array $actionSettings
     * @param [type] $formId
     * @param array $data
     * @return void
     */
    protected function constructRuntimeData(array $actionSettings, $formId, array $data):void{}

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
