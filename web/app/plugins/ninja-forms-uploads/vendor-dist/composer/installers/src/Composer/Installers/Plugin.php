<?php

namespace NF_FU_VENDOR\Composer\Installers;

use NF_FU_VENDOR\Composer\Composer;
use NF_FU_VENDOR\Composer\IO\IOInterface;
use NF_FU_VENDOR\Composer\Plugin\PluginInterface;
class Plugin implements \NF_FU_VENDOR\Composer\Plugin\PluginInterface
{
    public function activate(\NF_FU_VENDOR\Composer\Composer $composer, \NF_FU_VENDOR\Composer\IO\IOInterface $io)
    {
        $installer = new \NF_FU_VENDOR\Composer\Installers\Installer($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}
