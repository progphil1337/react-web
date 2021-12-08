<?php

namespace ReactMvc;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReactMvc\Config\AbstractConfig;
use ReactMvc\Mvc\Controller\ControllerFactory;
use ReactMvc\Mvc\Http\Header;
use ReactMvc\Mvc\Http\MethodEnum;
use ReactMvc\Mvc\Http\Request;
use ReactMvc\Mvc\Routing\RouteHandler;
use FastRoute;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;

/**
 * Main
 *
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Main
{
    private static ?self $instance = null;

    public static function create(AbstractConfig $config): self
    {

        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    private Dispatcher $dispatcher;

    private function __construct(private AbstractConfig $config)
    {
    }

    public function run(): void
    {
        $this->loadRoutes(APP_PATH . $this->config->get('Routes'));
        $this->start($this->config->get('HttpServer::ip'), (int)$this->config->get('HttpServer::port'));
    }

    private function loadRoutes(string $routesFile)
    {
        $routeHandler = new RouteHandler();
        $routeHandler->loadFromFile($routesFile);

        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($routeHandler) {
            foreach ($routeHandler->getRoutes() as $route) {
                /** @var MethodEnum $httpMethod */
                foreach ($route->httpMethods as $httpMethod) {
                    $r->addRoute($httpMethod->value, $route->route, $route);
                }
            }
        });

        $this->loadFactories();
    }

    private function loadFactories(): void
    {
        RouteHandler::registerFactory($this->getControllerFactory());
    }

    private function getTwigEnvironment(): TwigEnvironment
    {
        $loader = new TwigFilesystemLoader(APP_PATH . 'View');
        $twig = new TwigEnvironment(
            $loader
        );

        return $twig;
    }

    /**
     * @return ControllerFactory
     */
    private function getControllerFactory(): ControllerFactory
    {
        return new ControllerFactory(
            $this->getTwigEnvironment()
        );
    }

    private function start(string $ip, int $port): void
    {
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
                    header: new Header(array_change_key_case($request->getHeaders()), CASE_LOWER),
                    queryParams: $request->getQueryParams()
                );

                $uri = $r->route;
                if (false !== $pos = strpos($uri, '?')) {
                    $uri = substr($uri, 0, $pos);
                }
                $uri = rawurldecode($uri);

                $routeInfo = $this->dispatcher->dispatch($r->method->value, $uri);
                switch ($routeInfo[0]) {
                    case Dispatcher::METHOD_NOT_ALLOWED:
                        return new Response(405, ['Content-Type' => 'text/plain'], sprintf('Method %s not found', $r->method->value));

                    case Dispatcher::FOUND:
                        return $routeInfo[1]->callHandler($r, $routeInfo[2])->toHttpResponse();

                    case Dispatcher::NOT_FOUND:
                    default:
                        return new Response(404, ['Content-Type' => 'text/plain'], sprintf('Route %s not found', $uri));

                }
            }
        );

        $socketServer = new SocketServer($uri);

        Console::log($this, sprintf('Server running on %s', $uri));
        $httpServer->listen($socketServer);
    }
}
