<?php

namespace ReactMvc\Handler;

use ReactMvc\Http\Response;
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
abstract class Handler
{
    private readonly TwigEnvironment $twig;

    public function createInstance(TwigEnvironment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @param string $template
     * @param array $args
     * @return Response
     */
    protected function render(string $template, array $args = []): Response
    {
        try {
            return new HtmlResponse($this->twig->render("{$template}.twig", $args));
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return new ExceptionResponse($e);
        }
    }
}