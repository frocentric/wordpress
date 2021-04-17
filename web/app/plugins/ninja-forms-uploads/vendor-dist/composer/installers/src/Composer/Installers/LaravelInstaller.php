<?php

namespace NF_FU_VENDOR\Composer\Installers;

class LaravelInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('library' => 'libraries/{$name}/');
}
