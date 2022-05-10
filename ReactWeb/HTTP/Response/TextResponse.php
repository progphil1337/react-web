<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Response;

use ReactWeb\HTTP\Response;

/**
 * TextResponse
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
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