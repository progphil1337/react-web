<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Response;

use ReactWeb\HTTP\Response;

/**
 * RedirectResponse
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class RedirectResponse extends Response
{

    public function __construct(string $url)
    {
        $header = [
            'Location' => $url
        ];
        parent::__construct('', 307, $header);
    }

    protected function getContentType(): ?string
    {
        return null;
    }
}