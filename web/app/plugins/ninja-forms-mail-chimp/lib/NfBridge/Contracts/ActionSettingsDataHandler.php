<?php

namespace  NFMailchimp\EmailCRM\NfBridge\Contracts;

interface ActionSettingsDataHandler
{

    /**
     * Set incoming NF action settings
     *
     * @param  array  $actionSettings  Incoming NF action settings
     *
     * @return  ActionSettingsDataHandler
     */
    public function setActionSettings(array $actionSettings): ActionSettingsDataHandler;


    /**
     * Return keyed value from action settings
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getValue(string $key, $default = '');

    /**
     * Return object as single level associative array
     *
     * @return array
     */
    public function toArray(): array;
}
