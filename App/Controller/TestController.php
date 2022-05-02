<?php

namespace App\Controller;

use ReactMvc\Mvc\Controller\AbstractController;
use ReactMvc\Mvc\Http\HtmlResponse;
use ReactMvc\Mvc\Http\MethodEnum;
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
     * @param MethodEnum $methodEnum
     * @param array $vars
     * @return \ReactMvc\Mvc\Http\HtmlResponse
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function call(string $route, MethodEnum $methodEnum, array $vars): HtmlResponse
    {
        return $this->render('test');
    }
}