<?php

declare(strict_types=1);

namespace ReactWeb\Connection;

use ReactWeb\Config\Config;
use ReactWeb\DependencyInjection\Injector;
use ReactWeb\Logger\Logger;

/**
 * ManagerFactory
 *
 * @package ReactWeb\Connection
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class ManagerFactory
{
    private readonly ConnectionManager $connectionManager;

    /**
     * @param \ReactWeb\Config\Config $config
     * @param \ReactWeb\DependencyInjection\Injector $injector
     */
    public function __construct(private readonly Config $config, private readonly Injector $injector)
    {
        /** @var \ReactWeb\Connection\ConnectionManager $connectionManager */
        $connectionManager = $this->injector->create(ConnectionManager::class);
        $this->connectionManager = $connectionManager;
    }

    /**
     * @return void
     */
    public function registerManagers(): void
    {
        $managers = $this->config->get('Managers');

        foreach ($managers as $managerClass => $config) {
            $managerClass = sprintf('App\%s', $managerClass);

            $this->injector->create($managerClass, [
                '__construct' => [
                    array_key_exists('connection', $config) ?
                        $this->connectionManager->getConnection($config['connection']) :
                        $this->connectionManager->getConnection(),
                    $config['table'],
                    sprintf('App\%s', $config['entity']),
                    $config['primary_key']
                ]
            ]);
        }
    }
}