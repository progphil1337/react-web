<?php

declare(strict_types=1);

namespace App\Controller;

use ReactMvc\Mvc\Controller\AbstractController;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Http\HtmlResponse;
use ReactMvc\Mvc\Http\Request;
use ReactMvc\Mvc\Routing\RouteAwareHandler;
use ReactMvc\Session\Manager;

/**
 * LoginController
 *
 * @package App\Controller
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class LoginController extends AbstractController implements RouteAwareHandler
{
    public function __construct(private readonly Manager $sessionManager)
    {
    }

    public function call(string $route, Request $request, array $vars): AbstractResponse
    {
        $response = new HtmlResponse('LoginController');

        $response->writeHeader('Set-Cookie', urlencode('session_id') . '=' . urlencode($this->sessionManager->createSession()->hash));

        return $response;
    }
}