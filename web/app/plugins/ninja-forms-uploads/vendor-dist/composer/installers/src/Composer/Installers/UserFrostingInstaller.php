<?php

namespace NF_FU_VENDOR\Composer\Installers;

class UserFrostingInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('sprinkle' => 'app/sprinkles/{$name}/');
}
