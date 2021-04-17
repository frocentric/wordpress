<?php

namespace NF_FU_VENDOR\Composer\Installers;

use NF_FU_VENDOR\Composer\Package\PackageInterface;
class SilverStripeInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('module' => '{$name}/', 'theme' => 'themes/{$name}/');
    /**
     * Return the install path based on package type.
     *
     * Relies on built-in BaseInstaller behaviour with one exception: silverstripe/framework
     * must be installed to 'sapphire' and not 'framework' if the version is <3.0.0
     *
     * @param  PackageInterface $package
     * @param  string           $frameworkType
     * @return string
     */
    public function getInstallPath(\NF_FU_VENDOR\Composer\Package\PackageInterface $package, $frameworkType = '')
    {
        if ($package->getName() == 'silverstripe/framework' && \preg_match('/^\\d+\\.\\d+\\.\\d+/', $package->getVersion()) && \version_compare($package->getVersion(), '2.999.999') < 0) {
            return $this->templatePath($this->locations['module'], array('name' => 'sapphire'));
        }
        return parent::getInstallPath($package, $frameworkType);
    }
}
