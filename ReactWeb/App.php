<?php

declare(strict_types=1);

namespace ReactWeb;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReactWeb\Config\Config;
use ReactWeb\DependencyInjection\Injector;
use ReactWeb\Filesystem\Filesystem;
use ReactWeb\Logger\Logger;
use ReactWeb\HTTP\Response\ExceptionResponse;
use ReactWeb\HTTP\Header;
use ReactWeb\HTTP\Enum\Method;
use ReactWeb\HTTP\Request;
use ReactWeb\Routing\RouteHandleResolver;
use FastRoute;
use Throwable;

/**
 * App
 *
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class App
{
    private static ?self $instance = null;
    private readonly Dispatcher $routeDispatcher;

    /**
     * @param Config $config
     * @param \ReactWeb\DependencyInjection\Injector $injector
     * @return static
     */
    public static function create(Config $config, Injector $injector): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($config, $injector, new Filesystem($config));
        }

        return self::$instance;
    }

    /**
     * @param \ReactWeb\Config\Config $config
     * @param \ReactWeb\DependencyInjection\Injector $injector
     * @param \ReactWeb\Filesystem\Filesystem $filesystem
     */
    private function __construct(
        private readonly Config     $config,
        private readonly Injector   $injector,
        private readonly Filesystem $filesystem
    )
    {
    }

    /**
     * @return void
     * @throws \ReactWeb\Routing\Exception\RoutesFileNotFoundException
     */
    public function run(): void
    {
        $this->loadRoutes(PROJECT_PATH . $this->config->get('Routes'));
        $this->start($this->config->get('HttpServer::ip'), (int)$this->config->get('HttpServer::port'));
    }

    /**
     * @param string $routesFile
     * @return void
     * @throws \ReactWeb\Routing\Exception\RoutesFileNotFoundException
     */
    private function loadRoutes(string $routesFile): void
    {
        Logger::debug($this, 'Loading Routes');
        $routeHandler = new RouteHandleResolver($this->injector);
        $routeHandler->loadFromFile($routesFile);

        $this->routeDispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routeHandler) {
            foreach ($routeHandler->getRoutes() as $route) {
                /** @var Method $httpMethod */
                foreach ($route->httpMethods as $httpMethod) {
                    $r->addRoute($httpMethod->value, $route->route, $route);
                }
            }
        });

        $this->injector->getLookup()->register($this->routeDispatcher);
    }

    /**
     * @param string $ip
     * @param int $port
     * @return void
     */
    private function start(string $ip, int $port): void
    {
        $server = new Server($ip, $port);

        [$httpServer] = $server->run(function (Request $request): Response {
            $uri = $request->route;
            $pos = strpos($uri, '?');
            if ($pos !== false) {
                $uri = substr($uri, 0, $pos);
            }

            $uri = rawurldecode($uri);

            $routeInfo = $this->routeDispatcher->dispatch($request->method->value, $uri);

            /** @var \ReactWeb\Routing\Route $route */
            $route = $routeInfo[1] ?? null;

            try {
                return match ($routeInfo[0]) {
                    Dispatcher::METHOD_NOT_ALLOWED => new Response(405, ['Content-Type' => 'text/plain'], sprintf('Method %s not found', $request->method->value)),
                    Dispatcher::FOUND => $route->callHandler($request, $routeInfo[2])->toHttpResponse(),
                    Dispatcher::NOT_FOUND => $this->filesystem->find($request->route)?->createResponse($this->config->get('Filesystem'))->toHttpResponse() ?? new Response(404, ['Content-Type' => 'text/plain'], sprintf('There is nothing found at %s', $uri)),
                };
            } catch (\Exception $e) {
                return (new ExceptionResponse($e))->toHttpResponse();
            }
        });

        $httpServer->on('error', fn(Throwable $t) => Logger::error($this, (string)$t));
    }
}
