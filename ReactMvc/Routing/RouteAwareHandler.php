<?php

namespace ReactMvc\Routing;

use ReactMvc\Http\MethodEnum;
use ReactMvc\Http\Response;
use ReactMvc\Http\Request;

/**
 * RouteAwareHandler
 *
 * @package ReactMvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
interface RouteAwareHandler
{
    /**
     * @param \ReactMvc\Http\Request $request
     * @param array $vars
     * @return Response
     */
    public function handle(Request $request, array $vars): Response;
}