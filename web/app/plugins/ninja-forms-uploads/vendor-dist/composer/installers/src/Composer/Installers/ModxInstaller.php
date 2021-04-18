<?php

namespace NF_FU_VENDOR\Composer\Installers;

/**
 * An installer to handle MODX specifics when installing packages.
 */
class ModxInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('extra' => 'core/packages/{$name}/');
}
