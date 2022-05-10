<?php

declare(strict_types=1);

namespace ReactWeb\Routing;

use ReactWeb\DependencyInjection\Injector;
use ReactWeb\Logger\Logger;
use ReactWeb\Enum\BasicAction;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Enum\Method;
use ReactWeb\HTTP\Response;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response\TextResponse;
use ReactWeb\Routing\Exception\RoutesFileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * RouteHandleResolver
 *
 * @package ReactWeb\Routing
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class RouteHandleResolver
{
    /** @var array<\ReactWeb\Routing\Route> */
    private array $routes = [];

    private static Injector $injector;

    /** @var array<string, \ReactWeb\Routing\RouteAwareHandler> */
    private static array $loadedHandlers = [];

    /**
     * @param \ReactWeb\DependencyInjection\Injector $injector
     */
    public function __construct(Injector $injector)
    {
        self::$injector = $injector;
    }

    /**
     * @throws RoutesFileNotFoundException
     */
    public function loadFromFile(string $file): BasicAction
    {
        Logger::debug(RouteHandleResolver::class, sprintf('Loading routes from %s', $file));

        if (!file_exists($file)) {
            throw new RoutesFileNotFoundException(sprintf('Routes file %s not found', $file));
        }

        $routes = Yaml::parseFile($file);
        foreach ($routes as $handler => $info) {
            $route = new Route(
                route: strtolower($info['route']),
                handler: $handler,
                httpMethods: array_map(fn(string $method): Method => Method::from(strtoupper($method)), $info['methods']),
                middlewares: $info['middleware'] ?? []
            );

            Logger::debug(RouteHandleResolver::class, sprintf('Registering route %s (%s) with handler %s', $route->route, implode(', ', $info['methods']), $route->handler));

            $this->routes[] = $route;
        }

        return BasicAction::SUCCESS;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param Route $route
     * @return RouteAwareHandler|null
     */
    public static function getHandler(Route $route): ?RouteAwareHandler
    {
        return self::$loadedHandlers[$route->route] ?? null;
    }


    /**
     * @param string $handlerName
     * @param Route $route
     * @param Request $request
     * @param array $vars
     * @return Response
     */
    public static function callHandler(string $handlerName, Route $route, Request $request, array $vars): Response
    {
        Logger::debug(RouteHandleResolver::class, sprintf('Calling handler %s', $handlerName));

        $handler = self::getHandler($route);

        // create RouteAwareHandler
        if ($handler === null) {

            $classPath = sprintf('App\%s', $handlerName);

            /** @var \ReactWeb\Routing\RouteAwareHandler $handler */
            $handler = self::$injector->create($classPath);

            if ($handler instanceof Handler) {

                /** @var Environment $twigEnv */
                $twigEnv = self::$injector->create(Environment::class);
                $handler->createInstance($twigEnv);
            }

            self::cacheHandler($route, $handler);
        }

        foreach ($route->middlewares as $className) {
            /** @var \ReactWeb\Middleware\Middleware $middleware */
            $middleware = self::$injector->create($className);

            $result = $middleware->evaluate($request, $handler);

            if ($result instanceof BasicAction && $result !== BasicAction::SUCCESS) {
                Logger::error(RouteHandleResolver::class, 'Middleware not successful');

                return new TextResponse('Not authorized');
            }

            if ($result instanceof Response) {
                return $result;
            }
        }
        return $handler->handle($request, $vars);
    }

    /**
     * @param Route $route
     * @param RouteAwareHandler $handler
     * @return void
     */
    private static function cacheHandler(Route $route, RouteAwareHandler $handler): void
    {
        Logger::debug(RouteHandleResolver::class, sprintf('Caching handler %s for route %s', get_class($handler), $route->route));
        self::$loadedHandlers[$route->route] = $handler;
    }
}