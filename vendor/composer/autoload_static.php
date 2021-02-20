<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9588d518165624b75d7756fc08e6e2e5
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Carbon_Fields\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Carbon_Fields\\' => 
        array (
            0 => __DIR__ . '/..' . '/htmlburger/carbon-fields/core',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9588d518165624b75d7756fc08e6e2e5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9588d518165624b75d7756fc08e6e2e5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9588d518165624b75d7756fc08e6e2e5::$classMap;

        }, null, ClassLoader::class);
    }
}
