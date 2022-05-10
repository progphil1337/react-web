<?php

declare(strict_types=1);

namespace ReactWeb\Session;

use ReactWeb\Enum\BasicAction;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response\RedirectResponse;
use ReactWeb\Logger\Logger;
use ReactWeb\Middleware\Middleware as AbstractMiddleware;
use ReactWeb\Config\Config;
use ReactWeb\HTTP\Response;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * Middleware
 *
 * @package ReactWeb\Session
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class Middleware extends AbstractMiddleware
{
    private readonly string $key;

    /**
     * @param \ReactWeb\Session\Manager $sessionManager
     * @param \ReactWeb\Config\Config $config
     */
    public function __construct(private readonly Manager $sessionManager, private readonly Config $config)
    {
        $this->key = $this->config->get('Session::key');
    }

    /**
     * @param \ReactWeb\HTTP\Request $request
     * @param \ReactWeb\Routing\RouteAwareHandler $handler
     * @return \ReactWeb\Enum\BasicAction|\ReactWeb\HTTP\Response
     */
    public function evaluate(Request $request, RouteAwareHandler $handler): BasicAction|Response
    {
        $cookies = $request->cookies;

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

        $request->setSession($session);

        return BasicAction::SUCCESS;
    }
}