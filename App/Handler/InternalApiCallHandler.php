<?php

declare(strict_types=1);

namespace App\Handler;

use ReactWeb\Caller\InternalCaller;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * InteralApiCallHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
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
        return $this->caller->get('/api/test');
    }
}