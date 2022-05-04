<?php

namespace App\Handler;

use ReactMvc\Handler\AbstractHandler;
use ReactMvc\Http\AbstractResponse;
use ReactMvc\Http\Request;
use ReactMvc\Routing\RouteAwareHandler;

/**
 * TestHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TestHandler extends AbstractHandler implements RouteAwareHandler
{
    /**
     * @param \ReactMvc\Http\Request $request
     * @param array $vars
     * @return \ReactMvc\Http\HtmlResponse
     */
    public function handle(Request $request, array $vars): AbstractResponse
    {
        return $this->render('test', [
            'session_id' => 'Hallo'
        ]);
    }
}