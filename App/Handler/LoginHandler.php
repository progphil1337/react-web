<?php

declare(strict_types=1);

namespace App\Handler;

use ReactMvc\Handler\AbstractHandler;
use ReactMvc\Http\AbstractResponse;
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
class LoginHandler extends AbstractHandler implements RouteAwareHandler
{
    public function __construct(private readonly Manager $sessionManager)
    {

        parent::__construct();
    }

    public function handle(Request $request, array $vars): AbstractResponse
    {
        $response = new HtmlResponse('LoginHandler');

        $response->writeHeader('Set-Cookie', urlencode('session_id') . '=' . urlencode($this->sessionManager->createSession()->hash));

        return $response;
    }
}