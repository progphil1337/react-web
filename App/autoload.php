<?php

define('PROJECT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once PROJECT_PATH . 'vendor/autoload.php';

/**
 * Autoloader
 * Implement needed classes automatically
 */
spl_autoload_register(
    function (string $className): void {
        $namespace = explode('\\', $className);

        $filePath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $namespace) . '.php';

        require_once PROJECT_PATH . $filePath;
    }
);
