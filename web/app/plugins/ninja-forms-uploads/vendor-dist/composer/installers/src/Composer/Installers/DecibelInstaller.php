<?php

namespace NF_FU_VENDOR\Composer\Installers;

class DecibelInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    /** @var array */
    protected $locations = array('app' => 'app/{$name}/');
}
