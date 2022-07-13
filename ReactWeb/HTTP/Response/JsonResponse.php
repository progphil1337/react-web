<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Response;

use ReactWeb\HTTP\Response;

/**
 * JsonResponse
 *
 * @package ReactWeb\HTTP\Response
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class JsonResponse extends Response
{
    /**
     * @param string $content
     * @param int $code
     * @param array $header
     */
    public function __construct(
        mixed $content,
        int   $code = 200,
        array $header = ['charset' => 'utf-8']
    )
    {
        parent::__construct(json_encode($content), $code, $header);
    }

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'application/json';
    }
}