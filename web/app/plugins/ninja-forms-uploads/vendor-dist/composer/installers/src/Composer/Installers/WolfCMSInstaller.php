<?php

namespace NF_FU_VENDOR\Composer\Installers;

class WolfCMSInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('plugin' => 'wolf/plugins/{$name}/');
}
