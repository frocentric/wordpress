<?php

namespace NF_FU_VENDOR\Composer\Installers;

/**
 * Composer installer for 3rd party Tusk utilities
 * @author Drew Ewing <drew@phenocode.com>
 */
class TuskInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('task' => '.tusk/tasks/{$name}/', 'command' => '.tusk/commands/{$name}/', 'asset' => 'assets/tusk/{$name}/');
}
