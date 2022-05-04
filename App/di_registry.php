<?php
/**
 * @var \ReactMvc\Config\DefaultConfig $config
 * @var \ReactMvc\DependencyInjection\ClassLookup $lookup
 * @var \ReactMvc\DependencyInjection\Injector $injector
 */


$lookup
    ->alias(\ReactMvc\Config\Config::class, get_class($config))
    ->register($config)
    ->register(new Twig\Environment(
        new Twig\Loader\FilesystemLoader(APP_PATH . 'View')
    ))
    ->dismiss(\ReactMvc\Middleware\Middleware::class)
;

/** @var \ReactMvc\Session\Manager $sessionManager */
$sessionManager = $injector->create(\ReactMvc\Session\Manager::class);
$sessionManager->open();
