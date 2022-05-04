<?php

namespace ReactMvc;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReactMvc\Config\AbstractConfig;
use ReactMvc\DependencyInjection\Injector;
use ReactMvc\Logger\Logger;
use ReactMvc\Http\ExceptionResponse;
use ReactMvc\Http\Header;
use ReactMvc\Http\MethodEnum;
use ReactMvc\Http\Request;
use ReactMvc\Routing\RouteHandler;
use FastRoute;

/**
 * Main
 *
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Main
{
    private static ?self $instance = null;
    private readonly Dispatcher $dispatcher;
    private readonly Injector $injector;

    /**
     * @param AbstractConfig $config
     * @return static
     */
    public static function create(AbstractConfig $config): self
    {

        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * @param AbstractConfig $config
     */
    private function __construct(private readonly AbstractConfig $config)
    {
        Logger::setConfig($this->config);
    }

    /**
     * @param \ReactMvc\DependencyInjection\Injector $injector
     * @return void
     * @throws \ReactMvc\Routing\Exception\RoutesFileNotFoundException
     */
    public function run(Injector $injector): void
    {
        $this->injector = $injector;

        $this->loadRoutes(APP_PATH . $this->config->get('Routes'));
        $this->start($this->config->get('HttpServer::ip'), (int)$this->config->get('HttpServer::port'));
    }

    /**
     * @param string $routesFile
     * @return void
     * @throws \ReactMvc\Routing\Exception\RoutesFileNotFoundException
     */
    private function loadRoutes(string $routesFile): void
    {
        Logger::debug($this, 'Loading Routes');
        $routeHandler = new RouteHandler($this->injector);
        $routeHandler->loadFromFile($routesFile);

        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routeHandler) {
            foreach ($routeHandler->getRoutes() as $route) {
                /** @var MethodEnum $httpMethod */
                foreach ($route->httpMethods as $httpMethod) {
                    $r->addRoute($httpMethod->value, $route->route, $route);
                }
            }
        });
    }

    /**
     * @param string $ip
     * @param int $port
     * @return void
     */
    private function start(string $ip, int $port): void
    {
        Logger::info($this, 'Starting server');
        $uri = implode(':', [
            $ip,
            $port
        ]);

        $httpServer = new HttpServer(
            function (ServerRequestInterface $request): Response {

                $r = new Request(
                    uri: $request->getUri(),
                    route: $request->getUri()->getPath(),
                    method: MethodEnum::from($request->getMethod()),
                    header: new Header(array_change_key_case($request->getHeaders(), CASE_LOWER)),
                    queryParams: $request->getQueryParams(),
                    cookies: $request->getCookieParams()
                );

                Logger::debug($this, sprintf('Incoming request %s %s (%s)', $r->method->value, $r->route, $r->uri));

                $uri = $r->route;
                if (false !== $pos = strpos($uri, '?')) {
                    $uri = substr($uri, 0, $pos);
                }
                $uri = rawurldecode($uri);

                $routeInfo = $this->dispatcher->dispatch($r->method->value, $uri);

                /** @var \ReactMvc\Routing\Route $route */
                $route = $routeInfo[1] ?? null;

                try {
                    return match ($routeInfo[0]) {
                        Dispatcher::METHOD_NOT_ALLOWED => new Response(405, ['Content-Type' => 'text/plain'], sprintf('Method %s not found', $r->method->value)),
                        Dispatcher::FOUND => $route->callHandler($r, $routeInfo[2])->toHttpResponse(),
                        default => new Response(404, ['Content-Type' => 'text/plain'], sprintf('Route %s not found', $uri)),
                    };
                } catch(\Exception $e) {
                    return (new ExceptionResponse($e))->toHttpResponse();
                }
            }
        );

        $socketServer = new SocketServer($uri);

        Logger::info($this, sprintf('Server running on %s', $uri));
        $httpServer->listen($socketServer);
    }
}
