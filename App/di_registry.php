<?php
/**
 * @var \ReactWeb\Config\DefaultConfig $config
 * @var \ReactWeb\DependencyInjection\ClassLookup $lookup
 * @var \ReactWeb\DependencyInjection\Injector $injector
 */

$lookup
    // class aliases
    ->alias(get_class($config), \ReactWeb\Config\Config::class)

    // register singletons
    ->singleton(\ReactWeb\DependencyInjection\Singleton::class)
    ->singleton(Twig\Environment::class)

    // Register classes that cannot be created
    ->register($config)
    ->register(new Twig\Environment(
        new Twig\Loader\FilesystemLoader(APP_PATH . 'View')
    ))
;