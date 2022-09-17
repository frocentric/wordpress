<?php

namespace  NFMailchimp\EmailCRM\NfBridge\DataHandlers;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler as InterfacesActionSettingsDataHandler;

// class ActionSettingsDataHandler implements InterfacesActionSettingsDataHandler, \Iterator
class ActionSettingsDataHandler implements InterfacesActionSettingsDataHandler
{
    /**
     * $action_settings from ->process()
     *
     * @var [type]
     */
    protected $actionSettings;

    /**
     * Set incoming NF action settings
     *
     * @param  array  $actionSettings  Incoming NF action settings
     *
     * @return  ActionSettingsDataHandler
     */
    public function setActionSettings(array $actionSettings): ActionSettingsDataHandler{
        $this->actionSettings = $actionSettings;
        return $this;
    }


    /**
     * Return keyed value from action settings
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getValue(string $key, $default = ''){
        $return = $default;
        if(isset($this->actionSettings[$key])){
            $return = $this->actionSettings[$key];
        }

        return $return;
    }
}
