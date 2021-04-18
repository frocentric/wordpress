<?php

namespace NF_FU_VENDOR\Composer\Installers;

use NF_FU_VENDOR\Composer\DependencyResolver\Pool;
class CakePHPInstaller extends \NF_FU_VENDOR\Composer\Installers\BaseInstaller
{
    protected $locations = array('plugin' => 'Plugin/{$name}/');
    /**
     * Format package name to CamelCase
     */
    public function inflectPackageVars($vars)
    {
        if ($this->matchesCakeVersion('>=', '3.0.0')) {
            return $vars;
        }
        $nameParts = \explode('/', $vars['name']);
        foreach ($nameParts as &$value) {
            $value = \strtolower(\preg_replace('/(?<=\\w)([A-Z])/', 'NF_FU_VENDOR\\_\\1', $value));
            $value = \str_replace(array('-', '_'), ' ', $value);
            $value = \str_replace(' ', '', \ucwords($value));
        }
        $vars['name'] = \implode('/', $nameParts);
        return $vars;
    }
    /**
     * Change the default plugin location when cakephp >= 3.0
     */
    public function getLocations()
    {
        if ($this->matchesCakeVersion('>=', '3.0.0')) {
            $this->locations['plugin'] = $this->composer->getConfig()->get('vendor-dir') . '/{$vendor}/{$name}/';
        }
        return $this->locations;
    }
    /**
     * Check if CakePHP version matches against a version
     *
     * @param string $matcher
     * @param string $version
     * @return bool
     */
    protected function matchesCakeVersion($matcher, $version)
    {
        if (\class_exists('NF_FU_VENDOR\\Composer\\Semver\\Constraint\\MultiConstraint')) {
            $multiClass = 'NF_FU_VENDOR\\Composer\\Semver\\Constraint\\MultiConstraint';
            $constraintClass = 'NF_FU_VENDOR\\Composer\\Semver\\Constraint\\Constraint';
        } else {
            $multiClass = 'NF_FU_VENDOR\\Composer\\Package\\LinkConstraint\\MultiConstraint';
            $constraintClass = 'NF_FU_VENDOR\\Composer\\Package\\LinkConstraint\\VersionConstraint';
        }
        $repositoryManager = $this->composer->getRepositoryManager();
        if ($repositoryManager) {
            $repos = $repositoryManager->getLocalRepository();
            if (!$repos) {
                return \false;
            }
            $cake3 = new $multiClass(array(new $constraintClass($matcher, $version), new $constraintClass('!=', '9999999-dev')));
            $pool = new \NF_FU_VENDOR\Composer\DependencyResolver\Pool('dev');
            $pool->addRepository($repos);
            $packages = $pool->whatProvides('cakephp/cakephp');
            foreach ($packages as $package) {
                $installed = new $constraintClass('=', $package->getVersion());
                if ($cake3->matches($installed)) {
                    return \true;
                }
            }
        }
        return \false;
    }
}
