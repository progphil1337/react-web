<?php

namespace ReactMvc\Routing;

use ReactMvc\HTTP\Response;
use ReactMvc\HTTP\Request;

/**
 * Route
 *
 * @package ReactMvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
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