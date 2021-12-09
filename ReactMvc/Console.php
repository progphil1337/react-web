<?php

namespace ReactMvc;

use DateTime;
use ReactMvc\Config\AbstractConfig;

/**
 * Console
 *
 * @package ReactMvc
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Console
{
    private function __construct()
    {
    }

    private static AbstractConfig $config;

    public static function setConfig(AbstractConfig $config): void
    {
        self::$config = $config;
    }

    public static function write(string $string): void
    {
        echo $string;
    }

    public static function writeLine(string $string): void
    {
        self::write($string . PHP_EOL);
    }

    public static function log(object $o, string $string): void
    {
        if (self::$config->get('Logging') === 'on') {
            self::writeLine(sprintf('[LOG][%s][%s] %s', get_class($o), DateTime::createFromFormat('U.u', microtime(true))->format('H:i:s.u'), $string));
        }
    }
}