<?php

namespace ReactMvc\HTTP;

/**
 * HtmlResponse
 *
 * @package ReactMvc\HTTP
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