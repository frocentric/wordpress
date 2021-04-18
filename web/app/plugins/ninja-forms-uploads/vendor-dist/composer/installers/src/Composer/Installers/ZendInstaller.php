<?php

namespace NF_FU_VENDOR\Composer\Installers;

class ZendInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('library' => 'library/{$name}/', 'extra' => 'extras/library/{$name}/', 'module' => 'module/{$name}/');
}
