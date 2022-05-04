<?php

namespace ReactMvc\Http;

/**
 * HtmlResponse
 *
 * @package ReactMvc\Http
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class HtmlResponse extends Response
{

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'text/html';
    }
}