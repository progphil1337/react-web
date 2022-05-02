<?php

use ReactMvc\Config\BasicConfig;
use ReactMvc\Config\Exception\ConfigFileNotFoundException;
use ReactMvc\Config\Exception\ConfigFileNotInterpretableException;
use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Main;
use ReactMvc\Mvc\Routing\Exception\RoutesFileNotFoundException;
use ReactMvc\Session\SessionManager;

require_once 'autoload.php';

const APP_PATH = PROJECT_PATH . 'App' . DIRECTORY_SEPARATOR;

$config = APP_PATH . 'config.yaml';

try {
    $config = new BasicConfig($config);

    $main = Main::create($config);

    $sessionManager = new SessionManager($config);
    $sessionManager->open();

    $main->run();
} catch (RoutesFileNotFoundException|ConfigFileNotFoundException|ConfigFileNotInterpretableException $e) {
    echo $e->getMessage();

    exit(BasicActionEnum::ERROR);
}