<?php

use ReactMvc\Config\DefaultConfig;
use ReactMvc\Config\Exception\ConfigFileNotFoundException;
use ReactMvc\Config\Exception\ConfigFileNotInterpretableException;
use ReactMvc\DependencyInjection\Injector;
use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Main;
use ReactMvc\Routing\Exception\RoutesFileNotFoundException;

require_once 'autoload.php';

const APP_PATH = PROJECT_PATH . 'App' . DIRECTORY_SEPARATOR;

$config = APP_PATH . 'config.yaml';

try {
    $config = new DefaultConfig($config);
    \ReactMvc\Logger\Logger::setConfig($config);

    $injector = new Injector();
    require_once 'di_registry.php';

    $main = Main::create($config, $injector);

    $main->run();
} catch (RoutesFileNotFoundException|ConfigFileNotFoundException|ConfigFileNotInterpretableException $e) {
    echo $e->getMessage();

    exit(BasicActionEnum::ERROR);
}