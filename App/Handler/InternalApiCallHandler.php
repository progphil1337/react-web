<?php

declare(strict_types=1);

namespace App\Handler;

use ReactWeb\Caller\InternalCaller;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * InternalApiCallHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class InternalApiCallHandler extends Handler implements RouteAwareHandler
{
    public function __construct(
        private readonly InternalCaller $caller
    )
    {

    }

    public function handle(Request $request, array $vars): Response
    {
        $response = $this->caller->get('/api/test');

        $data = json_decode($response->getContent(), true);

        return new Response\HTMLResponse(<<<HTML
<b>{$data['text']}</b>
HTML);
    }
}