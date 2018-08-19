<?php

namespace TheCodingMachine;

use Psr\Container\ContainerInterface;
use Interop\Container\Factories\Alias;
use Interop\Container\Factories\Parameter;
use Interop\Container\ServiceProvider;
use Interop\Container\ServiceProviderInterface;
use Twig_Environment;
use Twig_LoaderInterface;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;

class TwigServiceProvider implements ServiceProviderInterface
{
    const PACKAGE = 'thecodingmachine.twig-universal-module';

    public function getFactories()
    {
        return [
            Twig_Environment::class => [self::class,'createTwigEnvironment'],
            Twig_LoaderInterface::class => new Alias(Twig_Loader_Chain::class),
            Twig_Loader_Chain::class => [self::class,'createLoaderChain'],
            'twig_options' => [self::class,'createOptions'],
            'twig_loaders' => [self::class,'createLoadersArray'],
            Twig_Loader_Filesystem::class => [self::class,'createTwigLoaderFilesystem'],
            'twig_directory' => new Parameter(dirname(__DIR__, 4)),
            'twig_cache_directory' => [self::class,'createTwigCacheDirectory'],
        ];
    }

    public function getExtensions()
    {
        return [];
    }

    /**
     * Returns the entry named PACKAGE.$name, of simply $name if PACKAGE.$name is not found.
     *
     * @param ContainerInterface $container
     * @param string             $name
     *
     * @return mixed
     */
    private static function get(ContainerInterface $container, string $name, $default = null)
    {
        $namespacedName = self::PACKAGE.'.'.$name;

        return $container->has($namespacedName) ? $container->get($namespacedName) : ($container->has($name) ? $container->get($name) : $default);
    }

    public static function createTwigEnvironment(ContainerInterface $container) : Twig_Environment
    {
        $environment = new Twig_Environment($container->get(\Twig_LoaderInterface::class), self::get($container, 'twig_options', []));
        $environment->setExtensions(self::get($container, 'twig_extensions', []));
        $debug = self::get($container, 'DEBUG', true);
        if ($debug) {
            $environment->addExtension(new \Twig_Extension_Debug());
        }

        return $environment;
    }

    public static function createOptions(ContainerInterface $container) : array
    {
        // By default, we look at the DEBUG constant in the container. If not found, we are in DEBUG.
        return [
            'debug' => self::get($container, 'DEBUG', true),
            'auto_reload' => true,
            'cache' => self::get($container, 'twig_cache_directory'),
        ];
    }

    public static function createLoaderChain(ContainerInterface $container) : Twig_Loader_Chain
    {
        return new Twig_Loader_Chain(self::get($container, 'twig_loaders', []));
    }

    public static function createLoadersArray(ContainerInterface $container) : array
    {
        return [
            $container->get(Twig_Loader_Filesystem::class),
        ];
    }

    public static function createTwigLoaderFilesystem(ContainerInterface $container) : Twig_Loader_Filesystem
    {
        return new Twig_Loader_Filesystem(self::get($container, 'twig_directory'));
    }

    public static function createTwigCacheDirectory() : string
    {
        // If we are running on a Unix environment, let's prepend the cache with the user id of the PHP process.
        // This way, we can avoid rights conflicts.
        if (function_exists('posix_geteuid')) {
            $posixGetuid = posix_geteuid();
        } else {
            $posixGetuid = '';
        }

        return rtrim(sys_get_temp_dir(), '/\\').'/twig_compiled_cache_'.$posixGetuid.str_replace(':', '', dirname(__DIR__, 4));
    }
}
