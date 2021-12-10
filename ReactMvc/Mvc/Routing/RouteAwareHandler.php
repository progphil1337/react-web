<?php

namespace ReactMvc\Mvc\Routing;

use ReactMvc\Mvc\Http\MethodEnum;
use ReactMvc\Mvc\Http\AbstractResponse;

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
     * @param MethodEnum $methodEnum
     * @param array $vars
     * @return AbstractResponse
     */
    public function call(string $route, MethodEnum $methodEnum, array $vars): AbstractResponse;
}