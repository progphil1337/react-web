<?php

namespace App\Handler;

use App\Manager\UserManager;
use ReactMvc\Handler\Handler;
use ReactMvc\HTTP\Response;
use ReactMvc\HTTP\Request;
use ReactMvc\Routing\RouteAwareHandler;

/**
 * TestHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TestHandler extends Handler implements RouteAwareHandler
{

    public function __construct(private readonly UserManager $userManager) {

    }

    /**
     * @param \ReactMvc\HTTP\Request $request
     * @param array $vars
     * @return \ReactMvc\HTTP\HtmlResponse
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