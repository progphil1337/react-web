<?php

declare(strict_types=1);

namespace ReactWeb\Logger;

use DateTime;
use ReactWeb\Config\Config;

/**
 * Logger
 *
 * @package ReactWeb
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class Logger
{
    private static array $config;

    /**
     * @param Config $config
     * @return void
     */
    public static function setConfig(Config $config): void
    {
        self::$config = $config->get('Logging');

        if (self::$config['type'] >= Type::FILE->value) {
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

    public static function debug(object|string $o, string $string): void
    {
        self::log(Mode::DEBUG, $o, $string);
    }

    public static function info(object|string $o, string $string): void
    {
        self::log(Mode::INFO, $o, $string);
    }

    public static function notice(object|string $o, string $string): void
    {
        self::log(Mode::NOTICE, $o, $string);
    }

    public static function error(object|string $o, string $string): void
    {
        self::log(Mode::ERROR, $o, $string);
    }

    public static function dump(object|string $o, mixed $var): void
    {
        self::log(Mode::DUMP, $o, print_r($var, true));
    }

    /**
     * @param \ReactWeb\Logger\Mode $mode
     * @param object|string $o
     * @param string $string
     * @return void
     */
    private static function log(Mode $mode, object|string $o, string $string): void
    {
        if ($mode->value < self::$config['mode']) {
            return;
        }

        if (self::$config['enabled'] !== 1) {
            return;
        }

        $line = sprintf(
            '[%s]%s%s %s',
            $mode->getText(),
            self::$config['show_class'] === 1 ? sprintf('[%s]', is_string($o) ? $o : get_class($o)) : '',
            self::$config['show_time'] === 1 ? sprintf('[%s]', DateTime::createFromFormat('U.u', microtime(true))->format('H:i:s.u')) : '',
            $string);

        if (self::$config['type'] === Type::CONSOLE->value || self::$config['type'] === Type::BOTH->value) {
            self::writeLine($line);
        }

        if (self::$config['type'] >= Type::FILE->value) {
            $file = APP_PATH . self::$config['log_file'];
            $content = file_get_contents($file) . $line . PHP_EOL;
            file_put_contents($file, $content);
        }
    }
}