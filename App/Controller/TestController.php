<?php

namespace App\Controller;

use ReactMvc\Mvc\Http\HtmlResponse;
use ReactMvc\Mvc\Http\MethodEnum;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Routing\RouteAwareHandler;

/**
 * TestController
 *
 * @package App\Controller
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TestController implements RouteAwareHandler
{
    public function call(string $route, MethodEnum $methodEnum, array $vars): AbstractResponse
    {
        return new HtmlResponse('<b>Hi</b>');
    }
}