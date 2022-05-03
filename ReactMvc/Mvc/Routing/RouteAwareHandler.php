<?php

namespace ReactMvc\Mvc\Routing;

use ReactMvc\Mvc\Http\MethodEnum;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Http\Request;

/**
 * RouteAwareHandler
 *
 * @package ReactMvc\Mvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
interface RouteAwareHandler
{
    /**
     * @param string $route
     * @param \ReactMvc\Mvc\Http\Request $request
     * @param array $vars
     * @return AbstractResponse
     */
    public function call(string $route, Request $request, array $vars): AbstractResponse;
}