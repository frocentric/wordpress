<?php

namespace NF_FU_VENDOR\Composer\Installers;

class OsclassInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('plugin' => 'oc-content/plugins/{$name}/', 'theme' => 'oc-content/themes/{$name}/', 'language' => 'oc-content/languages/{$name}/');
}
