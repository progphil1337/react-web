<?php

namespace ReactMvc\Routing;

use ReactMvc\DependencyInjection\Injector;
use ReactMvc\Logger\Logger;
use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Handler\Handler;
use ReactMvc\HTTP\MethodEnum;
use ReactMvc\HTTP\Response;
use ReactMvc\HTTP\Request;
use ReactMvc\HTTP\TextResponse;
use ReactMvc\Routing\Exception\RoutesFileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * RouteHandleResolver
 *
 * @package ReactMvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class RouteHandleResolver
{
    /** @var array<\ReactMvc\Routing\Route> */
    private array $routes = [];

    private static Injector $injector;

    /** @var array<string, \ReactMvc\Routing\RouteAwareHandler> */
    private static array $loadedHandlers = [];

    /**
     * @param \ReactMvc\DependencyInjection\Injector $injector
     */
    public function __construct(Injector $injector)
    {
        self::$injector = $injector;
    }

    /**
     * @throws RoutesFileNotFoundException
     */
    public function loadFromFile(string $file): BasicActionEnum
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
                httpMethods: array_map(fn(string $method): MethodEnum => MethodEnum::from(strtoupper($method)), $info['methods']),
                middlewares: $info['middleware'] ?? []
            );

            Logger::debug(RouteHandleResolver::class, sprintf('Registering route %s (%s) with handler %s', $route->route, implode(', ', $info['methods']), $route->handler));

            $this->routes[] = $route;
        }

        return BasicActionEnum::SUCCESS;
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

            /** @var \ReactMvc\Routing\RouteAwareHandler $handler */
            $handler = self::$injector->create($classPath);

            if ($handler instanceof Handler) {

                $handler->createInstance(self::$injector->create(Environment::class));
            }

            self::cacheHandler($route, $handler);
        }

        foreach ($route->middlewares as $className) {
            /** @var \ReactMvc\Middleware\Middleware $middleware */
            $middleware = self::$injector->create($className, [
                'createInstance' => [$request]
            ]);

            $result = $middleware->evaluate();

            if ($result instanceof BasicActionEnum && $result !== BasicActionEnum::SUCCESS) {
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