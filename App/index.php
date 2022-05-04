<?php

use ReactMvc\Config\BasicConfig;
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
    $config = new BasicConfig($config);

    $main = Main::create($config);

    $injector = new Injector();
    require_once 'di_registry.php';

    $main->run($injector);
} catch (RoutesFileNotFoundException|ConfigFileNotFoundException|ConfigFileNotInterpretableException $e) {
    echo $e->getMessage();

    exit(BasicActionEnum::ERROR);
}