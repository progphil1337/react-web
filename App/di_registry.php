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
;