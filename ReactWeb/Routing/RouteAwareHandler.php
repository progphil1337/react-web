<?php

namespace ReactWeb\Routing;

use ReactWeb\HTTP\MethodEnum;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;

/**
 * RouteAwareHandler
 *
 * @package ReactWeb\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
interface RouteAwareHandler
{
    /**
     * @param \ReactWeb\HTTP\Request $request
     * @param array $vars
     * @return Response
     */
    public function handle(Request $request, array $vars): Response;
}