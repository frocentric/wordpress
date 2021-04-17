<?php

namespace NF_FU_VENDOR\Composer\Installers;

class PortoInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('container' => 'app/Containers/{$name}/');
}
