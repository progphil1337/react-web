<?php

define('PROJECT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once PROJECT_PATH . 'vendor/autoload.php';


const PROJECT_NAMESPACES = [
    'ReactWeb',
    'App'
];

/**
 * Autoloader
 * Implement needed classes automatically
 */
spl_autoload_register(
    function (string $className): void {
        $namespace = explode('\\', $className);

        if (!in_array($namespace[0], PROJECT_NAMESPACES)) {
            return;
        }

        $filePath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $namespace) . '.php';

        require_once PROJECT_PATH . $filePath;
    }
);
