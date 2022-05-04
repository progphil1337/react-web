<?php

namespace App\Handler;

use ReactMvc\Handler\Handler;
use ReactMvc\Http\Response;
use ReactMvc\Http\Request;
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
    /**
     * @param \ReactMvc\Http\Request $request
     * @param array $vars
     * @return \ReactMvc\Http\HtmlResponse
     */
    public function handle(Request $request, array $vars): Response
    {
        return $this->render('test', [
            'session_id' => $request->getSession()->hash
        ]);
    }
}