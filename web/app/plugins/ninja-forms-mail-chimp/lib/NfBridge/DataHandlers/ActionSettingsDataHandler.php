<?php

namespace  NFMailchimp\EmailCRM\NfBridge\DataHandlers;

use NFMailchimp\EmailCRM\NfBridge\Contracts\ActionSettingsDataHandler as InterfacesActionSettingsDataHandler;

class ActionSettingsDataHandler implements InterfacesActionSettingsDataHandler
{
    /**
     * $action_settings from ->process()
     *
     * @var array
     */
    protected $actionSettings;

    /** @inheritDoc */
    public function setActionSettings(array $actionSettings): ActionSettingsDataHandler{
        $this->actionSettings = $actionSettings;
        return $this;
    }


    /** @inheritDoc */
    public function getValue(string $key, $default = ''){
        $return = $default;
        if(isset($this->actionSettings[$key])){
            $return = $this->actionSettings[$key];
        }

        return $return;
    }

    /** @inheritDoc */
    public function toArray(): array{

        return $this->actionSettings;
    }


}
