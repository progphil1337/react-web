<?php

declare(strict_types=1);

namespace ReactWeb\Config;

use ReactWeb\Config\Exception\ConfigFileNotFoundException;
use ReactWeb\Config\Exception\ConfigFileNotInterpretableException;
use ReactWeb\Config\Exception\ConfigTypeNotSupportedException;
use ReactWeb\Config\Exception\UnableToCreateConfigException;
use ReactWeb\DependencyInjection\Singleton;

/**
 * AbstractConfig
 *
 * @package ReactWeb\App\Exception\Config
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class Config implements Singleton
{
    private array $data = [];

    private const HIERARCHY_OPERATOR = '::';

    /**
     * @throws ConfigFileNotFoundException
     * @throws ConfigFileNotInterpretableException
     */
    public function __construct(private readonly string $configFile)
    {
        if (!file_exists($this->configFile)) {
            throw new ConfigFileNotFoundException(sprintf('Configurationfile %s not found', $this->configFile));
        }

        try {
            $this->data = $this->interpretConfig();
        } catch (ConfigTypeNotSupportedException|UnableToCreateConfigException) {
            throw new ConfigFileNotInterpretableException(sprintf('Configurationfile %s is not interpretable', $this->configFile));
        }
    }

    /**
     * @param string|null $configFile
     * @return array
     * @throws \ReactWeb\Config\Exception\ConfigTypeNotSupportedException
     * @throws \ReactWeb\Config\Exception\UnableToCreateConfigException
     */
    private function interpretConfig(?string $configFile = null): array
    {
        $configFile = $configFile ?? $this->configFile;
        $explodedConfigFile = explode('.', mb_strtolower($configFile));
        $fileEnding = end($explodedConfigFile);

        $configType = ConfigType::getByString($fileEnding);
        $interpreter = $configType->getInterpreter();

        return $interpreter->fromFile($configFile);
    }

    /**
     * @param string $node
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $node, mixed $default = null): mixed
    {
        if (str_contains($node, self::HIERARCHY_OPERATOR)) {
            $data = $this->data;
            $nodes = explode(self::HIERARCHY_OPERATOR, $node);

            foreach ($nodes as $node) {
                $data = $data[$node] ?? $default;
            }
        } else {
            $data = $this->data[$node] ?? $default;
        }

        return $data;
    }

    /**
     * @param string $separator
     * @param array $nodes
     * @return string
     */
    public function implode(string $separator, array $nodes): string
    {
        $data = [];

        foreach ($nodes as $node) {
            $data[] = $this->get($node);
        }

        return implode($separator, $data);
    }
}