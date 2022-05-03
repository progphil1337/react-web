<?php

declare(strict_types=1);

namespace ReactMvc\Session;

use ReactMvc\Logger\Logger;
use ReactMvc\Middleware\Middleware as AbstractMiddleware;
use ReactMvc\Config\AbstractConfig;

/**
 * Middleware
 *
 * @package ReactMvc\Session
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Middleware extends AbstractMiddleware
{
    private readonly string $key;

    /**
     * @param \ReactMvc\Session\Manager $sessionManager
     * @param \ReactMvc\Config\AbstractConfig $config
     */
    public function __construct(private readonly Manager $sessionManager, private readonly AbstractConfig $config)
    {
        $this->key = $this->config->get('Session::key');
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        $cookies = $this->getRequest()->cookies;

        if (!array_key_exists($this->key, $cookies)) {
            return false;
        }

        $session = null;

        try {
            $session = $this->sessionManager->getByHash($cookies[$this->key]);
        } catch (\Exception $e) {

            Logger::error($this, $e->getMessage());
        }

        $this->getRequest()->setSession($session);

        return $session !== null;
    }
}