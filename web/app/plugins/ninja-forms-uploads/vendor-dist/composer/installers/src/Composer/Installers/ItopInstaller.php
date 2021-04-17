<?php

namespace NF_FU_VENDOR\Composer\Installers;

class ItopInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('extension' => 'extensions/{$name}/');
}
