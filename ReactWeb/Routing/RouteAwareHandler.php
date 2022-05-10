<?php

declare(strict_types=1);

namespace ReactWeb\Routing;

use ReactWeb\HTTP\Enum\Method;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;

/**
 * RouteAwareHandler
 *
 * @package ReactWeb\Routing
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
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