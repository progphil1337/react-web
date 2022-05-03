<?php

declare(strict_types=1);

namespace ReactMvc\Session;

use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Logger\Logger;
use ReactMvc\Middleware\Middleware as AbstractMiddleware;
use ReactMvc\Config\AbstractConfig;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Http\RedirectResponse;

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
     * @return \ReactMvc\Enum\BasicActionEnum|\ReactMvc\Mvc\Http\AbstractResponse
     */
    public function run(): BasicActionEnum|AbstractResponse
    {
        $cookies = $this->getRequest()->cookies;

        if (!array_key_exists($this->key, $cookies)) {
            return new RedirectResponse('/');
        }

        $session = null;

        try {
            $session = $this->sessionManager->getByHash($cookies[$this->key]);
        } catch (\Exception $e) {
            Logger::error($this, $e->getMessage());
        }

        if ($session === null) {
            $response = new RedirectResponse('/');
            $response->writeHeader('Set-Cookie', 'expired; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT');
            return $response;
        }

        $this->getRequest()->setSession($session);

        return BasicActionEnum::SUCCESS;
    }
}