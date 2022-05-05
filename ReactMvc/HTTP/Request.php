<?php

namespace ReactMvc\HTTP;

use ReactMvc\DependencyInjection\Singleton;
use ReactMvc\Session\Session;

/**
 * Request
 *
 * @package ReactMvc\HTTP
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Request
{

    private ?Session $session = null;

    /**
     * @param string $uri
     * @param string $route
     * @param MethodEnum $method
     * @param Header $header
     * @param array $queryParams
     * @param array $cookies
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $route,
        public readonly MethodEnum $method,
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