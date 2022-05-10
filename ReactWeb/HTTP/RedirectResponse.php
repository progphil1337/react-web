<?php

declare(strict_types=1);

namespace ReactWeb\HTTP;

/**
 * RedirectResponse
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
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