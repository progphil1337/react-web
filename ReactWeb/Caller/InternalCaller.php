<?php

declare(strict_types=1);

namespace ReactWeb\Caller;

use FastRoute\Dispatcher;
use ReactWeb\DependencyInjection\Singleton;
use ReactWeb\HTTP\Enum\Method;
use ReactWeb\HTTP\Header;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response;
use \RuntimeException;

/**
 * InternalCaller
 *
 * @package ReactWeb\Caller
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class InternalCaller implements Singleton
{
    public function __construct(private readonly Dispatcher $dispatcher)
    {
    }

    public function get(string $uri, array $body = [], array $headers = [], array $queryParams = [], array $cookies = []): Response
    {
        return $this->call(Method::GET, $uri, $body, $headers, $queryParams, $cookies);
    }

    public function post(string $uri, array $body = [], array $headers = [], array $queryParams = [], array $cookies = []): Response
    {
        return $this->call(Method::POST, $uri, $body, $headers, $queryParams, $cookies);
    }

    public function put(string $uri, array $body = [], array $headers = [], array $queryParams = [], array $cookies = []): Response
    {
        return $this->call(Method::PUT, $uri, $body, $headers, $queryParams, $cookies);
    }

    public function delete(string $uri, array $body = [], array $headers = [], array $queryParams = [], array $cookies = []): Response
    {
        return $this->call(Method::DELETE, $uri, $body, $headers, $queryParams, $cookies);
    }

    public function options(string $uri, array $body = [], array $headers = [], array $queryParams = [], array $cookies = []): Response
    {
        return $this->call(Method::OPTIONS, $uri, $body, $headers, $queryParams, $cookies);
    }

    public function call(Method $method, string $uri, array $body = [], array $headers = [], array $queryParams = [], array $cookies = []): Response
    {
        $info = $this->dispatcher->dispatch($method->value, $uri);
        /** @var \ReactWeb\Routing\Route $route */
        $route = $info[1] ?? null;

        $request = new Request(
            uri: $uri,
            route: $uri,
            method: $method,
            header: new Header(array_change_key_case($headers, CASE_LOWER)),
            queryParams: $queryParams,
            cookies: $cookies,
            body: $body
        );

        return match ($info[0]) {
            Dispatcher::METHOD_NOT_ALLOWED => throw new RuntimeException(sprintf('Internal api call failed. Method %s not allowed for route %s.', $method->value, $uri)),
            Dispatcher::FOUND => $route->callHandler($request, $info[2]),
            Dispatcher::NOT_FOUND => throw new RuntimeException(sprintf('Internal api call failed. Route %s not found.', $uri)),
        };
    }
}