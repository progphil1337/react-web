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
    public function __construct(
        public readonly string $route,
        public readonly string $handler,
        public readonly array $httpMethods
    )
    {
    }

    public function callHandler(Request $request, array $vars): AbstractResponse
    {
        return RouteHandler::callHandler($this->handler, $this, $request->method, $vars);
    }
}