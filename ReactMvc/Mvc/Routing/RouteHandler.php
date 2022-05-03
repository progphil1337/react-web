<?php

namespace ReactMvc\Mvc\Routing;

use ReactMvc\DependencyInjection\Injector;
use ReactMvc\Logger\Logger;
use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Mvc\Controller\AbstractController;
use ReactMvc\Mvc\Http\MethodEnum;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Routing\Exception\RoutesFileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * RouteHandler
 *
 * @package ReactMvc\Mvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class RouteHandler
{
    /** @var array<\ReactMvc\Mvc\Routing\Route> */
    private array $routes = [];

    private static Injector $injector;

    /** @var array<string, \ReactMvc\Mvc\Routing\RouteAwareHandler> */
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
        Logger::debug(RouteHandler::class, sprintf('Loading routes from %s', $file));

        if (!file_exists($file)) {
            throw new RoutesFileNotFoundException(sprintf('Routes file %s not found', $file));
        }

        $routes = Yaml::parseFile($file);
        foreach ($routes as $handler => $info) {
            $route = new Route(
                route: strtolower($info['route']),
                handler: $handler,
                httpMethods: array_map(fn(string $method): MethodEnum => MethodEnum::from(strtoupper($method)), $info['methods'])
            );

            Logger::debug(RouteHandler::class, sprintf('Registering route %s (%s) with handler %s', $route->route, implode(', ', $info['methods']), $route->handler));

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
     * @param MethodEnum $methodEnum
     * @param array $vars
     * @return AbstractResponse
     */
    public static function callHandler(string $handlerName, Route $route, MethodEnum $methodEnum, array $vars): AbstractResponse
    {
        Logger::debug(RouteHandler::class, sprintf('Calling handler %s', $handlerName));

        $handler = self::getHandler($route);

        // create RouteAwareHandler
        if ($handler === null) {

            $classPath = "App\\{$handlerName}";

            /** @var \ReactMvc\Mvc\Routing\RouteAwareHandler $handler */
            $handler = self::$injector->create($classPath);


            if ($handler instanceof AbstractController) {

                $handler->createInstance(self::$injector->create(Environment::class));
            }

            self::cacheHandler($route, $handler);
        }

        return $handler->call($route->route, $methodEnum, $vars);
    }

    /**
     * @param Route $route
     * @param RouteAwareHandler $handler
     * @return void
     */
    private static function cacheHandler(Route $route, RouteAwareHandler $handler): void
    {
        Logger::debug(RouteHandler::class, sprintf('Caching handler %s for route %s', get_class($handler), $route->route));
        self::$loadedHandlers[$route->route] = $handler;
    }
}