<?php

declare(strict_types=1);

namespace ReactWeb\Config;

use ReactWeb\Config\Exception\ConfigTypeNotSupportedException;
use ReactWeb\Config\Exception\UnableToCreateConfigException;
use ReactWeb\Config\Interpreter\ConfigInterpreter;

/**
 * ConfigType
 *
 * @package ReactWeb\Config
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
enum ConfigType: string
{
    case YAML = 'yaml';
    case JSON = 'json';
    case INI = 'ini';

    /**
     * @throws ConfigTypeNotSupportedException
     */
    public static function getByString(string $str): self
    {
        return match ($str) {
            self::YAML->value, 'yml' => ConfigType::YAML,
            self::JSON->value => ConfigType::JSON,
            self::INI->value => ConfigType::INI,
            default => throw new ConfigTypeNotSupportedException(
                sprintf(
                    'Config type %s not supported. Supported types: %s',
                    $str,
                    implode(', ', array_map(fn(ConfigType $type) => $type->value, self::cases()))
                )
            )
        };
    }

    /**
     * @return ConfigInterpreter
     * @throws UnableToCreateConfigException
     */
    public function getInterpreter(): ConfigInterpreter
    {
        $className = ucfirst($this->value) . 'ConfigInterpreter';
        $classPath = "ReactWeb\Config\Interpreter\\{$className}";

        try {
            $reflectionClass = new \ReflectionClass($classPath);

            /** @var ConfigInterpreter $class */
            $class = $reflectionClass->newInstance();

            return $class;
        } catch (\ReflectionException) {
            throw new UnableToCreateConfigException(sprintf('Unable to create ConfigInterpreter %s (%s)', $className, $classPath));
        }
    }
}