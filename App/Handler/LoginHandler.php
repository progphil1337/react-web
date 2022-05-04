<?php

declare(strict_types=1);

namespace App\Handler;

use ReactMvc\Handler\Handler;
use ReactMvc\Http\Response;
use ReactMvc\Http\HtmlResponse;
use ReactMvc\Http\Request;
use ReactMvc\Routing\RouteAwareHandler;
use ReactMvc\Session\Manager;

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
        $this->t->a = 'LoginHandler';
    }


    public function handle(Request $request, array $vars): Response
    {
        $response = new HtmlResponse('LoginHandler');

        $response->writeHeader('Set-Cookie', urlencode('session_id') . '=' . urlencode($this->sessionManager->createSession()->hash));

        return $response;
    }
}