<?php

declare(strict_types=1);

namespace App\Handler;

use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\HtmlResponse;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;
use ReactWeb\Session\Manager;

/**
 * LoginHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class LoginHandler extends Handler implements RouteAwareHandler
{
    public function __construct(private readonly Manager $sessionManager)
    {

    }

    public function handle(Request $request, array $vars): Response
    {
        $response = new HtmlResponse('LoginHandler');

        $response->writeHeader('Set-Cookie', urlencode('session_id') . '=' . urlencode($this->sessionManager->createSession()->hash));

        return $response;
    }
}