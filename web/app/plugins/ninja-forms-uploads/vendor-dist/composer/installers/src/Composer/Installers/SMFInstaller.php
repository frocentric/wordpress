<?php

namespace NF_FU_VENDOR\Composer\Installers;

class SMFInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'Sources/{$name}/', 'theme' => 'Themes/{$name}/');
}
