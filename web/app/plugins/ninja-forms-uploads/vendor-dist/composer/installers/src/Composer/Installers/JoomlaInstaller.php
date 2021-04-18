<?php

namespace NF_FU_VENDOR\Composer\Installers;

class JoomlaInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('component' => 'components/{$name}/', 'module' => 'modules/{$name}/', 'template' => 'templates/{$name}/', 'plugin' => 'plugins/{$name}/', 'library' => 'libraries/{$name}/');
    // TODO: Add inflector for mod_ and com_ names
}
