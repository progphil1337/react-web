<?php

namespace ReactMvc\Logger;

use DateTime;
use ReactMvc\Config\AbstractConfig;

/**
 * Logger
 *
 * @package ReactMvc
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Logger
{
    private function __construct()
    {
    }

    private static array $config;

    /**
     * @param AbstractConfig $config
     * @return void
     */
    public static function setConfig(AbstractConfig $config): void
    {
        self::$config = $config->get('Logging');

        if (self::$config['mode'] >= LogType::FILE->value) {
            $file = APP_PATH . self::$config['log_file'];
            if (!file_exists($file)) {
                touch($file);
            } else {
                file_put_contents($file, '');
            }
        }
    }

    /**
     * @param string $string
     * @return void
     */
    public static function write(string $string): void
    {
        echo $string;
    }

    /**
     * @param string $string
     * @return void
     */
    public static function writeLine(string $string): void
    {
        self::write($string . PHP_EOL);
    }

    /**
     * @param object $o
     * @param string $string
     * @return void
     */
    public static function log(object $o, string $string): void
    {
        if (self::$config['enabled'] !== 1) {

            return;
        }

        $line = sprintf(
            '[LOG]%s%s %s',
            self::$config['show_class'] === 1 ? sprintf('[%s]', get_class($o)) : '',
            self::$config['show_time'] === 1 ? sprintf('[%s]', DateTime::createFromFormat('U.u', microtime(true))->format('H:i:s.u')) : '',
            $string);

        if (self::$config['mode'] === LogType::CONSOLE->value || self::$config['mode'] === LogType::BOTH->value) {
            self::writeLine($line);
        }

        if (self::$config['mode'] >= LogType::FILE->value) {
            $file = APP_PATH . self::$config['log_file'];
            $content = file_get_contents($file) . $line . PHP_EOL;
            file_put_contents($file, $content);
        }
    }
}