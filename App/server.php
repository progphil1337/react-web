<?php

declare(strict_types=1);

use ReactMvc\Config\DefaultConfig;
use ReactMvc\Config\Exception\ConfigFileNotFoundException;
use ReactMvc\Config\Exception\ConfigFileNotInterpretableException;
use ReactMvc\DependencyInjection\ClassLookup;
use ReactMvc\DependencyInjection\Injector;
use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Logger\Logger;
use ReactMvc\Server;
use ReactMvc\Routing\Exception\RoutesFileNotFoundException;

require_once 'autoload.php';

const APP_PATH = PROJECT_PATH . 'App' . DIRECTORY_SEPARATOR;

$config = APP_PATH . 'config.yaml';

try {
    $config = new DefaultConfig($config);
    Logger::setConfig($config);

    $lookup = new ClassLookup();
    $injector = new Injector($lookup);
    require_once 'di_registry.php';

    $server = Server::create($config, $injector);

    $managerFactory = new \ReactMvc\Connection\ManagerFactory($config, $injector);

    $managerFactory->registerManagers();

    $server->run();
} catch (RoutesFileNotFoundException|ConfigFileNotFoundException|ConfigFileNotInterpretableException $e) {
    echo $e->getMessage();

    exit(BasicActionEnum::ERROR);
}