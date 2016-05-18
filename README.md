[![Build Status](https://travis-ci.org/thecodingmachine/twig-universal-module.svg?branch=1.0)](https://travis-ci.org/thecodingmachine/twig-universal-module)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/twig-universal-module/badge.svg?branch=1.0&service=github)](https://coveralls.io/github/thecodingmachine/twig-universal-module?branch=1.0)


# Twig universal module

This package integrates Twig in any [container-interop/service-provider](https://github.com/container-interop/service-provider) compatible framework/container.

## Installation

```
composer require thecodingmachine/twig-universal-module
```

Once installed, you need to register the [`TheCodingMachine\TwigServiceProvider`](src/TwigServiceProvider.php) into your container.

If your container supports Puli integration, you have nothing to do. Otherwise, refer to your framework or container's documentation to learn how to register *service providers*.

## Introduction

This service provider is meant to create a base `Twig_Environment` instance.

Out of the box, the instance should be usable with sensible defaults. We tried to keep the defaults usable for most of the developer, while still providing caching for good performances.
If you are looking for the best performances, you will need to tweak the settings.

### Usage

```php
$twig = $container->get('Twig_Environement');
echo $twig->render('views/my.twig', [ 'foo' => 'bar' ]);
```

### Default values

By default:

- Caching is enabled, in a directory under the temporary system directory. In production, if you are running a multi-user environment, you might want to change that to a directory only readable by you.
- `autoreload = true`: You can safely modify any Twig file without needing to purge the cache. In production, if you are looking for best performance, put this to `false`.
- Twig files will be loaded from the root of your project (the directory where the `composer.json` file is). You can change that by overloading the `Twig_LoaderInterface` entry or the `Twig_Loader_Filesystem` entry.
- By default, `debug = true` unless your provide a value in the `DEBUG` entry of your container.

## Expected values / services

**Important**: when this service provider looks for a service, it will first look for the service prefixed with the package name, then for the service directly.
So if this documentation states that the `DEBUG` entry is used, the service provider will first look into `thecodingmachine.twig-universal-module.DEBUG` and then into `DEBUG`.
This allows you to keep your container clean (with only one `DEBUG` entry), and in case there are several service providers using that `DEBUG` entry and you want to pass different values, you can still edit `thecodingmachine.twig-universal-module.DEBUG` for this service provider only.


This *service provider* expects the following configuration / services to be available:

| Name            | Compulsory | Description                            |
|-----------------|------------|----------------------------------------|
| `DEBUG`         | *no*       | The debug mode of Twig |


## Provided services

This *service provider* provides the following services:

| Service name                | Description                          |
|-----------------------------|--------------------------------------|
| `Twig_Environment::class`  | The Twig_Environment instance   |
| `Twig_LoaderInterface::class`  | An alias to the loader chain (by default, the Twig_Environment is using a loader chain)   |
| `Twig_Loader_Chain::class`  |  Instance of the loader chain.  |
| `twig_options`  | An array containing options passed to Twig (see default values in previous chapter).  |
| `twig_loaders`  | An array of loaders. Contains by default a single instance of the `Twig_Loader_Filesystem`.   |
| `Twig_Loader_Filesystem::class`  |  The default instance of the `Twig_Loader_Filesystem`.  |
| `twig_directory`  | The base directory storing the Twig files. Defaults to root directory of the project. Used by `Twig_Loader_Filesystem`.   |
| `twig_cache_directory`  |  Default directory that stores Twig compiled templates. |

## Extended services

This *service provider* does not extend any service.
