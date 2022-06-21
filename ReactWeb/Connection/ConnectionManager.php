<?php

declare(strict_types=1);

namespace ReactWeb\Connection;

use ReactWeb\Config\Config;
use ReactWeb\DependencyInjection\Singleton;

/**
 * ConnectionManager
 *
 * @package ReactWeb\Connection
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class ConnectionManager implements Singleton
{
    /** @var array<\ReactWeb\Connection\DatabaseConnection */
    private array $connections = [];
    private readonly string $mainConnectionName;

    public function __construct(private readonly Config $config)
    {
        foreach ($this->config->get('Connection') as $key => $entry) {
            if (array_key_exists('main', $entry) && $entry['main']) {
                $this->mainConnectionName = $key;
            }
        }
    }

    /**
     * @param string|null $name
     * @return \ReactWeb\Connection\DatabaseConnection
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
     * @return \ReactWeb\Connection\DatabaseConnection
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