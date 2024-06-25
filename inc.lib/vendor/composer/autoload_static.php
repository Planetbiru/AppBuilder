<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitec57b79da9c49fe88b5ef2780de2f2b6
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
        ),
        'M' => 
        array (
            'MagicObject\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'MagicObject\\' => 
        array (
            0 => __DIR__ . '/..' . '/planetbiru/magic-object/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'A' => 
        array (
            'AppBuilder\\' => 
            array (
                0 => __DIR__ . '/../..' . '/classes',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitec57b79da9c49fe88b5ef2780de2f2b6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitec57b79da9c49fe88b5ef2780de2f2b6::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitec57b79da9c49fe88b5ef2780de2f2b6::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitec57b79da9c49fe88b5ef2780de2f2b6::$classMap;

        }, null, ClassLoader::class);
    }
}
