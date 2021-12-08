<?php

namespace ReactMvc;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use ReactMvc\Config\AbstractConfig;

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

    private function __construct(private AbstractConfig $config)
    {
    }

    public function run(): void
    {
        $this->loadMvc();

        $this->start();
    }

    private function start(): void
    {
        $uri = $this->config->implode(':', [
            'Server::ip',
            'Server::port'
        ]);

        $httpServer = new HttpServer(
            fn(ServerRequestInterface $request): Response => new Response(200, ['Content-Type' => 'text/plain'], 'Hello World')
        );

        $socketServer = new SocketServer($uri);

        Console::log($this, sprintf('Server running on %s', $uri));
        $httpServer->listen($socketServer);
    }
}
