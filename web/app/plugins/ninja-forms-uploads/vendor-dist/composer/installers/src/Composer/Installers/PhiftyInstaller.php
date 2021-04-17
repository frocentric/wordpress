<?php

namespace NF_FU_VENDOR\Composer\Installers;

class PhiftyInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('bundle' => 'bundles/{$name}/', 'library' => 'libraries/{$name}/', 'framework' => 'frameworks/{$name}/');
}
