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
 * Main
 *
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class Server
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
     * @param Config $config
     * @param \ReactWeb\DependencyInjection\Injector $injector
     */
    private function __construct(
        private readonly Config $config,
        private readonly Injector $injector,
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

        $this->loadRoutes(APP_PATH . $this->config->get('Routes'));
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

        $getUrl = function (ServerRequestInterface $request): string {
            $uri = $request->getUri();

            $pattern = '%s://%s';
            $args = [$uri->getScheme(), $uri->getHost()];

            if ($uri->getPort() !== NULL) {
                $pattern .= ':%s';
                $args[] = $uri->getPort();
            }

            if ($uri->getPath() !== '') {
                $pattern .= '%s';
                $args[] = $uri->getPath();
            }

            if ($uri->getQuery() !== '') {
                $pattern .= '?%s';
                $args[] = $uri->getQuery();
            }

            return call_user_func_array('sprintf', [$pattern, ...$args]);
        };

        $filesystem = $this->filesystem;

        $httpServer = new HttpServer(
            function (ServerRequestInterface $request) use ($getUrl, $filesystem): Response {

                $r = new Request(
                    uri: $getUrl($request),
                    route: $request->getUri()->getPath(),
                    method: Method::from($request->getMethod()),
                    header: new Header(array_change_key_case($request->getHeaders(), CASE_LOWER)),
                    queryParams: $request->getQueryParams(),
                    cookies: $request->getCookieParams(),
                );

                Logger::debug($this, sprintf('Incoming request %s %s (%s)', $r->method->value, $r->route, $r->uri));

                $uri = $r->route;
                if (false !== $pos = strpos($uri, '?')) {
                    $uri = substr($uri, 0, $pos);
                }
                $uri = rawurldecode($uri);

                $routeInfo = $this->routeDispatcher->dispatch($r->method->value, $uri);

                /** @var \ReactWeb\Routing\Route $route */
                $route = $routeInfo[1] ?? null;

                try {
                    return match ($routeInfo[0]) {
                        Dispatcher::METHOD_NOT_ALLOWED => new Response(405, ['Content-Type' => 'text/plain'], sprintf('Method %s not found', $r->method->value)),
                        Dispatcher::FOUND => $route->callHandler($r, $routeInfo[2])->toHttpResponse(),
                        default => $filesystem->find($r->route)?->createResponse($this->config->get('Filesystem'))->toHttpResponse() ?? new Response(404, ['Content-Type' => 'text/plain'], sprintf('Route %s not found', $uri)),
                    };
                } catch (\Exception $e) {
                    return (new ExceptionResponse($e))->toHttpResponse();
                }
            }
        );

        $socketServer = new SocketServer($uri);

        Logger::info($this, sprintf('Server running on %s', $uri));

        $httpServer->listen($socketServer);

        $httpServer->on('error', function (Throwable $t) {
            echo $t;
        });
    }
}
