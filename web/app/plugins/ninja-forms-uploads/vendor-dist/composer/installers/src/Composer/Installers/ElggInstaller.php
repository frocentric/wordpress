<?php

namespace NF_FU_VENDOR\Composer\Installers;

class ElggInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('plugin' => 'mod/{$name}/');
}
