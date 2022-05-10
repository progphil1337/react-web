<?php

declare(strict_types=1);

namespace ReactWeb\HTTP;

use ReactWeb\HTTP\Enum\Method;
use ReactWeb\Session\Session;

/**
 * Request
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class Request
{

    private ?Session $session = null;

    /**
     * @param string $uri
     * @param string $route
     * @param Method $method
     * @param Header $header
     * @param array $queryParams
     * @param array $cookies
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $route,
        public readonly Method $method,
        public readonly Header $header,
        public readonly array $queryParams,
        public readonly array $cookies
    )
    {
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }
}