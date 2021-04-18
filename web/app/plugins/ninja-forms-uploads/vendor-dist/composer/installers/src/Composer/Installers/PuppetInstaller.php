<?php

namespace NF_FU_VENDOR\Composer\Installers;

class PuppetInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'modules/{$name}/');
}
