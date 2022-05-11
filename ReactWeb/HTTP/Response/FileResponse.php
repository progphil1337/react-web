<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Response;

use ReactWeb\HTTP\Response;

/**
 * FileResponse
 *
 * @package ReactWeb\HTTP\Response
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class FileResponse extends Response
{

    public function __construct(
        private readonly string $name,
        private readonly string $contentType,
        private readonly string $path
    )
    {

        parent::__construct(file_get_contents($this->path), 200, []);
    }

    protected function getContentType(): ?string
    {
        return $this->contentType;
    }
}