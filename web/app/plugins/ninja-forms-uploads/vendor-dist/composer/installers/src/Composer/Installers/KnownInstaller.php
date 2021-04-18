<?php

namespace NF_FU_VENDOR\Composer\Installers;

class KnownInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('plugin' => 'IdnoPlugins/{$name}/', 'theme' => 'Themes/{$name}/', 'console' => 'ConsolePlugins/{$name}/');
}
