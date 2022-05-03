<?php

namespace ReactMvc\Mvc\Controller;

use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Http\ExceptionResponse;
use ReactMvc\Mvc\Http\HtmlResponse;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * AbstractController
 *
 * @package ReactMvc\Mvc\Controller
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class AbstractController
{
    private readonly TwigEnvironment $twig;

    public function __construct() {
        // needed for DI
    }

    public function createInstance(TwigEnvironment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @param string $template
     * @param array $args
     * @return AbstractResponse
     */
    protected function render(string $template, array $args = []): AbstractResponse
    {
        try {
            return new HtmlResponse($this->twig->render("{$template}.twig", $args));
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return new ExceptionResponse($e);
        }
    }
}