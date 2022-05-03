<?php
/**
 * @var \ReactMvc\Config\BasicConfig $config
 * @var \ReactMvc\DependencyInjection\Injector $injector
 */

use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;


$injector
    ->alias(\ReactMvc\Config\AbstractConfig::class, get_class($config))
    ->register($config)
    ->register(new TwigEnvironment(
        new TwigFilesystemLoader(APP_PATH . 'View')
    ))
    ->dismiss(\ReactMvc\Session\Middleware::class)
;

/** @var \ReactMvc\Session\Manager $sessionManager */
$sessionManager = $injector->create(\ReactMvc\Session\Manager::class);
$sessionManager->open();

$injector->register($sessionManager);
