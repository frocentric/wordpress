<?php

namespace NF_FU_VENDOR\Composer\Installers;

class AglInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'More/{$name}/');
    /**
     * Format package name to CamelCase
     */
    public function inflectPackageVars($vars)
    {
        $vars['name'] = \preg_replace_callback('/(?:^|_|-)(.?)/', function ($matches) {
            return \strtoupper($matches[1]);
        }, $vars['name']);
        return $vars;
    }
}
