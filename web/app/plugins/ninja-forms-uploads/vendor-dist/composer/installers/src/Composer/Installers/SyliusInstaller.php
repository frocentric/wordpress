<?php

namespace NF_FU_VENDOR\Composer\Installers;

class SyliusInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('theme' => 'themes/{$name}/');
}
