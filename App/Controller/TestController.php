<?php

namespace App\Controller;

use ReactMvc\Mvc\Controller\AbstractController;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Http\Request;
use ReactMvc\Mvc\Routing\RouteAwareHandler;

/**
 * TestController
 *
 * @package App\Controller
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TestController extends AbstractController implements RouteAwareHandler
{
    /**
     * @param string $route
     * @param \ReactMvc\Mvc\Http\Request $request
     * @param array $vars
     * @return \ReactMvc\Mvc\Http\HtmlResponse
     */
    public function call(string $route, Request $request, array $vars): AbstractResponse
    {
        return $this->render('test', [
            'session_id' => $request->getSession()->hash
        ]);
    }
}