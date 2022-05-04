<?php

declare(strict_types=1);

namespace ReactMvc\Http;

/**
 * RedirectResponse
 *
 * @package ReactMvc\Http
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class RedirectResponse extends AbstractResponse
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