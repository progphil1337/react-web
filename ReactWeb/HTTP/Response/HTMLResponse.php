<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Response;

use ReactWeb\HTTP\Response;

/**
 * HtmlResponse
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class HTMLResponse extends Response
{

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'text/html';
    }
}