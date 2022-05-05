<?php
/**
 * @var \ReactMvc\Config\DefaultConfig $config
 * @var \ReactMvc\DependencyInjection\ClassLookup $lookup
 * @var \ReactMvc\DependencyInjection\Injector $injector
 */

$lookup
    // class aliases
    ->alias(get_class($config), \ReactMvc\Config\Config::class)

    // register singletons
    ->singleton(\ReactMvc\DependencyInjection\Singleton::class)
    ->singleton(Twig\Environment::class)

    // Register classes that cannot be created
    ->register($config)
    ->register(new Twig\Environment(
        new Twig\Loader\FilesystemLoader(APP_PATH . 'View')
    ))
;