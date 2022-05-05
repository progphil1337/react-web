<?php

namespace ReactMvc\Routing;

use ReactMvc\HTTP\MethodEnum;
use ReactMvc\HTTP\Response;
use ReactMvc\HTTP\Request;

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
     * @param \ReactMvc\HTTP\Request $request
     * @param array $vars
     * @return Response
     */
    public function handle(Request $request, array $vars): Response;
}