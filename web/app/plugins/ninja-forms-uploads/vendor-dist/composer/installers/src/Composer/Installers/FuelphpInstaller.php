<?php

namespace NF_FU_VENDOR\Composer\Installers;

class FuelphpInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('component' => 'components/{$name}/');
}
