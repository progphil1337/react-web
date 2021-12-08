<?php

namespace ReactMvc\Mvc\Routing;

use ReactMvc\Console;
use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Mvc\Http\MethodEnum;
use ReactMvc\Mvc\Http\AbstractResponse;
use ReactMvc\Mvc\Routing\Exception\RoutesFileNotFoundException;
use Symfony\Component\Yaml\Yaml;

/**
 * RouteHandler
 *
 * @package ReactMvc\Mvc\Routing
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class RouteHandler
{

    private static array $loadedHandlers = [];

    private array $routes = [];

    /**
     * @throws RoutesFileNotFoundException
     */
    public function loadFromFile(string $file): BasicActionEnum
    {

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

    public static function callHandler(string $handlerName, Route $route, MethodEnum $methodEnum, array $vars): AbstractResponse
    {
        $handler = self::getHandler($route);

        if ($handler === null) {
            $classPath = "App\\{$handlerName}";
            try {
                $reflectionClass = new \ReflectionClass($classPath);
            } catch (\ReflectionException $e) {
                Console::log(new self(), $e->getMessage());
            }

            /** @var RouteAwareHandler $handler */
            try {
                $handler = $reflectionClass->newInstance();
            } catch (\ReflectionException $e) {
                Console::log(new self(), $e->getMessage());
            }

            self::cacheHandler($route, $handler);
        }

        return $handler->call($route->route, $methodEnum, $vars);
    }

    private static function cacheHandler(Route $route, RouteAwareHandler $handler): void
    {
        self::$loadedHandlers[$route->route] = $handler;
    }
}