<?php

namespace ReactMvc\Handler;

use ReactMvc\Http\AbstractResponse;
use ReactMvc\Http\ExceptionResponse;
use ReactMvc\Http\HtmlResponse;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * AbstractHandler
 *
 * @package ReactMvc\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class AbstractHandler
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