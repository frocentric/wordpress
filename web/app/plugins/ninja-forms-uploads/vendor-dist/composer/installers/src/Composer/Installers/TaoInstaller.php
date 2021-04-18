<?php

namespace NF_FU_VENDOR\Composer\Installers;

/**
 * An installer to handle TAO extensions.
 */
class TaoInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('extension' => '{$name}');
}
