<?php

declare(strict_types=1);

namespace ReactWeb\Handler;

use ReactWeb\DependencyInjection\Singleton;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Response\ExceptionResponse;
use ReactWeb\HTTP\Response\HTMLResponse;
use ReactWeb\Logger\Logger;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * AbstractHandler
 *
 * @package ReactWeb\Handler
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
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
            return new HTMLResponse($this->twig->render("{$template}.twig", $args));
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            return new ExceptionResponse($e);
        }
    }

    public function __destruct()
    {
        Logger::info($this, 'Destroying');
    }
}