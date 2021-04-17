<?php

namespace NF_FU_VENDOR\Composer\Installers;

class CiviCrmInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('ext' => 'ext/{$name}/');
}
