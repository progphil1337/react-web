<?php

namespace ReactMvc\Mvc\Http;

/**
 * Request
 *
 * @package ReactMvc\Mvc\Http
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Request
{

    /**
     * @param string $uri
     * @param string $route
     * @param MethodEnum $method
     * @param Header $header
     * @param array $queryParams
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $route,
        public readonly MethodEnum $method,
        public readonly Header $header,
        public readonly array $queryParams,
    )
    {

    }
}