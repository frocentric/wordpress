<?php

namespace NF_FU_VENDOR\Composer\Installers;

class ClanCatsFrameworkInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('ship' => 'CCF/orbit/{$name}/', 'theme' => 'CCF/app/themes/{$name}/');
}
