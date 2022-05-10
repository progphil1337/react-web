<?php

declare(strict_types=1);

namespace ReactWeb\Middleware;

use ReactWeb\Enum\BasicAction;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * Middleware
 *
 * @package ReactWeb\Middleware
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class Middleware
{
    protected readonly Request $request;
    protected readonly RouteAwareHandler $handler;

    public function createInstance(Request $request, RouteAwareHandler $handler): self
    {
        $this->request = $request;
        $this->handler = $handler;

        return $this;
    }

    abstract public function evaluate(): BasicAction|Response;
}