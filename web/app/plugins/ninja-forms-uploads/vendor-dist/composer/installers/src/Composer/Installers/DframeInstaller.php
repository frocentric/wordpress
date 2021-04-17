<?php

namespace NF_FU_VENDOR\Composer\Installers;

class DframeInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'modules/{$vendor}/{$name}/');
}
