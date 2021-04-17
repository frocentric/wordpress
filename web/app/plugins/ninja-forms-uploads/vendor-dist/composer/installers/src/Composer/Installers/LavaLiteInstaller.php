<?php

namespace NF_FU_VENDOR\Composer\Installers;

class LavaLiteInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('package' => 'packages/{$vendor}/{$name}/', 'theme' => 'public/themes/{$name}/');
}
