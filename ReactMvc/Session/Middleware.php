<?php

declare(strict_types=1);

namespace ReactMvc\Session;

use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Logger\Logger;
use ReactMvc\Middleware\Middleware as AbstractMiddleware;
use ReactMvc\Config\Config;
use ReactMvc\HTTP\Response;
use ReactMvc\HTTP\RedirectResponse;

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
     * @param \ReactMvc\Config\Config $config
     */
    public function __construct(private readonly Manager $sessionManager, private readonly Config $config)
    {
        $this->key = $this->config->get('Session::key');
    }

    /**
     * @return \ReactMvc\Enum\BasicActionEnum|\ReactMvc\HTTP\Response
     */
    public function evaluate(): BasicActionEnum|Response
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