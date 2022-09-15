<?php

declare(strict_types=1);

namespace ReactWeb;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReactWeb\HTTP\Enum\Method;
use ReactWeb\HTTP\Header;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response\ExceptionResponse;
use ReactWeb\Logger\Logger;
use Throwable;

/**
 * Server
 *
 * @package ReactWeb
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Server
{

    private readonly string $uri;

    public function __construct(
        private readonly string $ip,
        private readonly int    $port
    )
    {

        $this->uri = implode(':', [
            $ip, $port
        ]);
    }

    public function run(callable $callable): array
    {
        Logger::info($this, 'Starting server');

        $httpServer = new HttpServer(
            function (ServerRequestInterface $request) use ($callable): Response {

                $r = new Request(
                    uri: $this->buildUri($request),
                    route: $request->getUri()->getPath(),
                    method: Method::from($request->getMethod()),
                    header: new Header(array_change_key_case($request->getHeaders(), CASE_LOWER)),
                    queryParams: $request->getQueryParams(),
                    cookies: $request->getCookieParams(),
                    body: $request->getParsedBody() ?? []
                );

                Logger::debug($this, sprintf('Incoming request %s %s (%s)', $r->method->value, $r->route, $r->uri));

                return $callable($r);
            }
        );

        $socketServer = new SocketServer($this->uri);

        Logger::info($this, sprintf('App running on %s', $this->uri));

        $httpServer->listen($socketServer);

        return [$httpServer, $socketServer];
    }

    private function buildUri(ServerRequestInterface $request): string
    {
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
    }
}