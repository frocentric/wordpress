<?php

namespace NF_FU_VENDOR\Composer\Installers;

class TheliaInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => 'local/modules/{$name}/', 'frontoffice-template' => 'templates/frontOffice/{$name}/', 'backoffice-template' => 'templates/backOffice/{$name}/', 'email-template' => 'templates/email/{$name}/');
}
