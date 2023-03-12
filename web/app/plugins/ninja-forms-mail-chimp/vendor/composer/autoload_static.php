<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3493e98f53c33ecffbfa8de70086a92e
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'NFMailchimp\\NinjaForms\\Mailchimp\\' => 33,
            'NFMailchimp\\EmailCRM\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'NFMailchimp\\NinjaForms\\Mailchimp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'NFMailchimp\\EmailCRM\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3493e98f53c33ecffbfa8de70086a92e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3493e98f53c33ecffbfa8de70086a92e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3493e98f53c33ecffbfa8de70086a92e::$classMap;

        }, null, ClassLoader::class);
    }
}