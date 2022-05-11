<?php

declare(strict_types=1);

use ReactWeb\Config\DefaultConfig;
use ReactWeb\Config\Exception\ConfigFileNotFoundException;
use ReactWeb\Config\Exception\ConfigFileNotInterpretableException;
use ReactWeb\DependencyInjection\ClassLookup;
use ReactWeb\DependencyInjection\Injector;
use ReactWeb\Enum\BasicAction;
use ReactWeb\Logger\Logger;
use ReactWeb\Server;
use ReactWeb\Routing\Exception\RoutesFileNotFoundException;

require_once 'autoload.php';

const APP_PATH = PROJECT_PATH . 'App' . DIRECTORY_SEPARATOR;

$configFile = PROJECT_PATH . 'config.yaml';

try {
    $configList = new DefaultConfig($configFile);

    $config = null;
    foreach ($configList->get('Config') as $file) {
        $filePath = PROJECT_PATH . 'config' . DIRECTORY_SEPARATOR . $file;
        if ($config === null) {
            $config = new DefaultConfig($filePath);
        } else {
            $config->merge(new DefaultConfig($filePath));
        }
    }

    Logger::setConfig($config);

    $lookup = new ClassLookup();
    $injector = new Injector($lookup);
    require_once 'di_registry.php';

    $server = Server::create($config, $injector);

    $managerFactory = new \ReactWeb\Connection\ManagerFactory($config, $injector);

    $managerFactory->registerManagers();

    $server->run();
} catch (RoutesFileNotFoundException|ConfigFileNotFoundException|ConfigFileNotInterpretableException $e) {
    echo sprintf('%s\n%s', $e->getMessage(), $e->getFile());

    exit(BasicAction::ERROR->value);
}