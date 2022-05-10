<?php

declare(strict_types=1);

namespace ReactWeb\Middleware;

use ReactWeb\Enum\BasicAction;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * Middleware
 *
 * @package ReactWeb\Middleware
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class Middleware
{
    abstract public function evaluate(Request $request, RouteAwareHandler $handler): BasicAction|Response;
}