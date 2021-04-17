<?php

namespace NF_FU_VENDOR\Composer\Installers;

class MakoInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('package' => 'app/packages/{$name}/');
}
