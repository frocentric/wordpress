<?php

namespace NF_FU_VENDOR\Composer\Installers;

class BonefishInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('package' => 'Packages/{$vendor}/{$name}/');
}
