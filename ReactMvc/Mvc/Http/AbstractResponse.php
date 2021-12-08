<?php

namespace ReactMvc\Mvc\Http;

use React\Http\Message\Response as HttpResponse;

/**
 * Response
 *
 * @package ReactMvc\Mvc\Http
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class AbstractResponse
{
    private int $code = 200;
    private string $contentType = 'text/html';
    private string $charset = 'utf-8';

    public function __construct(private string $content) {
    }

    public function toHttpResponse(): HttpResponse
    {
        return new HttpResponse($this->code, [
            'Content-Type' => $this->contentType,
            'charset' => $this->charset
        ], $this->content);
    }
}