<?php

namespace NF_FU_VENDOR\Composer\Installers;

class KohanaInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'modules/{$name}/');
}
