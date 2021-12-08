<?php

namespace ReactMvc\Mvc\Http;

/**
 * TextResponse
 *
 * @package ReactMvc\Mvc\Http
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TextResponse extends AbstractResponse
{

    protected function getContentType(): string
    {
        return 'text/plain';
    }
}