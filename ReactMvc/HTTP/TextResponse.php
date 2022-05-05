<?php

namespace ReactMvc\HTTP;

/**
 * TextResponse
 *
 * @package ReactMvc\HTTP
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TextResponse extends Response
{

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'text/plain';
    }
}