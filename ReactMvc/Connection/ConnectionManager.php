<?php

declare(strict_types=1);

namespace ReactMvc\Connection;

use ReactMvc\Config\Config;
use ReactMvc\DependencyInjection\Singleton;

/**
 * ConnectionManager
 *
 * @package ReactMvc\Connection
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class ConnectionManager implements Singleton
{
    /** @var array<\ReactMvc\Connection\DatabaseConnection */
    private array $connections = [];
    private readonly string $mainConnectionName;

    public function __construct(private readonly Config $config)
    {
        foreach ($this->config->get('Connection') as $key => $entry) {
            if ($entry['main']) {
                $this->mainConnectionName = $key;
            }
        }

    }

    /**
     * @param string|null $name
     * @return \ReactMvc\Connection\DatabaseConnection
     */
    public function getConnection(?string $name = null): DatabaseConnection
    {
        if ($name === null) {
            $name = $this->mainConnectionName;
        }

        if (!array_key_exists($name, $this->connections)) {
            $this->connections[$name] = $this->createConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * @param string $name
     * @return \ReactMvc\Connection\DatabaseConnection
     */
    private function createConnection(string $name): DatabaseConnection
    {
        $config = $this->config->get(sprintf('Connection::%s', $name));

        return new DatabaseConnection(
            $config['username'],
            $config['password'],
            $config['host'],
            $config['port'],
            $config['charset'],
            $config['dbname']
        );
    }
}