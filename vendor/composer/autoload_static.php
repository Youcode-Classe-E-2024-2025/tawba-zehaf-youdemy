<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit19f6845127f9417c3ee04f76e56c01a9
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Youdemy\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Youdemy\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit19f6845127f9417c3ee04f76e56c01a9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit19f6845127f9417c3ee04f76e56c01a9::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit19f6845127f9417c3ee04f76e56c01a9::$classMap;

        }, null, ClassLoader::class);
    }
}
