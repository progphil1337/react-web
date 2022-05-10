<?php

declare(strict_types=1);

namespace ReactWeb\Routing;

use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;

/**
 * Route
 *
 * @package ReactWeb\Routing
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class Route
{
    /**
     * @param string $route
     * @param string $handler
     * @param array $httpMethods
     * @param array $middlewares
     */
    public function __construct(
        public readonly string $route,
        public readonly string $handler,
        public readonly array $httpMethods,
        public readonly array $middlewares
    )
    {
    }

    /**
     * @param Request $request
     * @param array $vars
     * @return Response
     */
    public function callHandler(Request $request, array $vars): Response
    {
        return RouteHandleResolver::callHandler($this->handler, $this, $request, $vars);
    }
}