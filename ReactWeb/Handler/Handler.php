<?php

namespace ReactWeb\Handler;

use ReactWeb\DependencyInjection\Singleton;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\ExceptionResponse;
use ReactWeb\HTTP\HtmlResponse;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * AbstractHandler
 *
 * @package ReactWeb\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class Handler implements Singleton
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