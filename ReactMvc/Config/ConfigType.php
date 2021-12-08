<?php

namespace ReactMvc\Config;

use ReactMvc\Config\Exception\ConfigTypeNotSupportedException;
use ReactMvc\Config\Exception\UnableToCreateConfigException;
use ReactMvc\Config\Interpreter\ConfigInterpreter;

/**
 * ConfigType
 *
 * @package ReactMvc\Config
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
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
        $classPath = "ReactMvc\Config\Interpreter\\{$className}";

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