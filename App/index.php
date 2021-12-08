<?php

use ReactMvc\Config\BasicConfig;

require_once 'autoload.php';

const APP_PATH = PROJECT_PATH . 'App' . DIRECTORY_SEPARATOR;

$config = APP_PATH . 'config.yaml';

$main = \ReactMvc\Main::create(new BasicConfig($config));
$main->run();