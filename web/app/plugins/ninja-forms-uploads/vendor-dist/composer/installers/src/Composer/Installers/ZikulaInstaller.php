<?php

namespace NF_FU_VENDOR\Composer\Installers;

class ZikulaInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'modules/{$vendor}-{$name}/', 'theme' => 'themes/{$vendor}-{$name}/');
}
