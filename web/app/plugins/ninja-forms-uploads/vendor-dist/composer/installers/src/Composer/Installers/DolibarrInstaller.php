<?php

namespace NF_FU_VENDOR\Composer\Installers;

/**
 * Class DolibarrInstaller
 *
 * @package Composer\Installers
 * @author  Raphaël Doursenaud <rdoursenaud@gpcsolutions.fr>
 */
class DolibarrInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    //TODO: Add support for scripts and themes
    protected $locations = array('module' => 'htdocs/custom/{$name}/');
}
