<?php

namespace  NFMailchimp\EmailCRM\NfBridge\DataHandlers;


use NFMailchimp\EmailCRM\NfBridge\Contracts\SubmissionDataDataHandler as InterfaceSubmissionDataDataHandler;

class SubmissionDataDataHandler implements InterfaceSubmissionDataDataHandler
{

    /**
     * $data from action ->process()
     *
     * @var array
     */
    private $data;

    /**
     * Action key used for storing data
     *
     * @var string
     */
    private $actionKey;

    /** @inheritDoc */
    public function getValue(string $key, $default = '')
    {
        $return = $default;

        if (isset($this->data[$key])) {
            $return = $this->data[$key];
        }

        return $return;
    }

    /** @inheritDoc */
    public function getFieldValueByFieldKey(string $fieldKey, $default = '')
    {
        $return = $default;

        if (isset($this->data['fields_by_key'][$fieldKey]['value'])) {

            $return = $this->data['fields_by_key'][$fieldKey]['value'];
        }

        return $return;
    }

    /** @inheritDoc */
    public function pushError(string $errorMessage): SubmissionDataDataHandler{

        $this->data['errors'][$this->actionKey]=$errorMessage;

        return $this;
    }
    /** @inheritDoc */
    public function pushExtra($extra): SubmissionDataDataHandler
    {
        $this->data['extra'][$this->actionKey]['responseData'][]=$extra;
        
        return $this;
    }

    /** @inheritDoc */
    public function setSubmissionData(array $data): InterfaceSubmissionDataDataHandler
    {
        $this->data = $data;

        return $this;
    }

    /** @inheritDoc */
    public function setActionKey(string $actionKey): InterfaceSubmissionDataDataHandler
    {
        $this->actionKey = $actionKey;

        return $this;
    }

    /** @inheritDoc */
    public function getData(): array
    {
        return $this->data;
    }
}

