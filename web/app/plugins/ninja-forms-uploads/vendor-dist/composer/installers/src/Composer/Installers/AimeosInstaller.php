<?php

namespace NF_FU_VENDOR\Composer\Installers;

class AimeosInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('extension' => 'ext/{$name}/');
}
