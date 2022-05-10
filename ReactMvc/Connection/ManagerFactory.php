<?php

declare(strict_types=1);

namespace ReactMvc\Connection;

use ReactMvc\Config\Config;
use ReactMvc\DependencyInjection\Injector;
use ReactMvc\Logger\Logger;

/**
 * ManagerFactory
 *
 * @package ReactMvc\Connection
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class ManagerFactory
{
    private readonly ConnectionManager $connectionManager;

    /**
     * @param \ReactMvc\Config\Config $config
     * @param \ReactMvc\DependencyInjection\Injector $injector
     */
    public function __construct(private readonly Config $config, private readonly Injector $injector)
    {
        /** @var \ReactMvc\Connection\ConnectionManager connectionManager */
        $this->connectionManager = $this->injector->create(ConnectionManager::class);
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
                    $this->connectionManager->getConnection($config['connection']),
                    $config['table'],
                    sprintf('App\%s', $config['entity']),
                    $config['primary_key'] ?? null
                ]
            ]);
        }
    }
}