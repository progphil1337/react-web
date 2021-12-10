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

    /**
     * @param string $content
     * @param int $code
     * @param string $charset
     */
    public function __construct(private string $content, private int $code = 200, private string $charset = 'utf-8') {}

    /**
     * @return string
     */
    abstract protected function getContentType(): string;

    /**
     * @return HttpResponse
     */
    public function toHttpResponse(): HttpResponse
    {
        return new HttpResponse($this->code, [
            'Content-Type' => $this->getContentType(),
            'charset' => $this->charset
        ], $this->content);
    }
}