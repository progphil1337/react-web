<?php

namespace App\Handler;

use App\Manager\UserManager;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * TestHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class TestHandler extends Handler implements RouteAwareHandler
{

    public function __construct(private readonly UserManager $userManager) {

    }

    /**
     * @param \ReactWeb\HTTP\Request $request
     * @param array $vars
     * @return \ReactWeb\HTTP\Response\HTMLResponse
     */
    public function handle(Request $request, array $vars): Response
    {
        $username = $this->userManager->getOneBy('session_id', $request->getSession()->hash);

        return $this->render('test', [
            'session_id' => $request->getSession()->hash,
            'username' => $username->name
        ]);
    }
}