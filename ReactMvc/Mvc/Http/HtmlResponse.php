<?php

namespace ReactMvc\Mvc\Http;

/**
 * HtmlResponse
 *
 * @package ReactMvc\Mvc\Http
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class HtmlResponse extends AbstractResponse
{

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'text/html';
    }
}