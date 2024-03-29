<?php

declare(strict_types=1);

namespace App\Handler\Api;

use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * TestApiHandler
 *
 * @package App\Handler\Api
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class TestApiHandler extends Handler implements RouteAwareHandler
{
    public function handle(Request $request, array $vars): Response
    {
        return new Response\JsonResponse([
            'text' => 'Hello world'
        ]);
    }
}