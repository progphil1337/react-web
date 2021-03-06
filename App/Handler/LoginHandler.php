<?php

declare(strict_types=1);

namespace App\Handler;

use App\Manager\UserManager;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Response\HTMLResponse;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;
use ReactWeb\Session\Manager;

/**
 * LoginHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class LoginHandler extends Handler implements RouteAwareHandler
{
    public function __construct(private readonly Manager $sessionManager, private readonly UserManager $userManager)
    {

    }

    public function handle(Request $request, array $vars): Response
    {
        $hash = $this->sessionManager->createSession()->hash;
        /** @var \App\Entity\User $user */
        $user = $this->userManager->getById(1);
        $user->sessionId = $hash;

        $success = $this->userManager->save($user);

        $response = new HTMLResponse($success ? 'Hash gesetzt' : 'Error');

        $response->writeHeader('Set-Cookie', urlencode('session_id') . '=' . urlencode($hash));

        return $response;
    }
}