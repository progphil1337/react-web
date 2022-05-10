<?php

declare(strict_types=1);

use ReactWeb\Config\DefaultConfig;
use ReactWeb\Config\Exception\ConfigFileNotFoundException;
use ReactWeb\Config\Exception\ConfigFileNotInterpretableException;
use ReactWeb\DependencyInjection\ClassLookup;
use ReactWeb\DependencyInjection\Injector;
use ReactWeb\Enum\BasicActionEnum;
use ReactWeb\Logger\Logger;
use ReactWeb\Server;
use ReactWeb\Routing\Exception\RoutesFileNotFoundException;

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

    $managerFactory = new \ReactWeb\Connection\ManagerFactory($config, $injector);

    $managerFactory->registerManagers();

    $server->run();
} catch (RoutesFileNotFoundException|ConfigFileNotFoundException|ConfigFileNotInterpretableException $e) {
    echo $e->getMessage();

    exit(BasicActionEnum::ERROR);
}