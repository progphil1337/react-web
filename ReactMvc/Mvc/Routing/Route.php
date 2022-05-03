<?php

namespace ReactMvc\Mvc\Routing;

use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Http\Request;

/**
 * Route
 *
 * @package ReactMvc\Mvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Route
{
    /**
     * @param string $route
     * @param string $handler
     * @param array $httpMethods
     */
    public function __construct(
        public readonly string $route,
        public readonly string $handler,
        public readonly array $httpMethods
    )
    {
    }

    /**
     * @param Request $request
     * @param array $vars
     * @return AbstractResponse
     * @throws \ReflectionException
     */
    public function callHandler(Request $request, array $vars): AbstractResponse
    {
        return RouteHandler::callHandler($this->handler, $this, $request->method, $vars);
    }
}