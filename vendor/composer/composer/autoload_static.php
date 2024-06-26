<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9dc48835bf6a21f6b137e9733261a724
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9dc48835bf6a21f6b137e9733261a724::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9dc48835bf6a21f6b137e9733261a724::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9dc48835bf6a21f6b137e9733261a724::$classMap;

        }, null, ClassLoader::class);
    }
}
