<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Response;

use Exception;
use JetBrains\PhpStorm\Pure;
use ReactWeb\HTTP\Response;

/**
 * ExceptionResponse
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class ExceptionResponse extends Response
{

    /**
     * @param Exception $e
     */
    #[Pure] public function __construct(Exception $e)
    {
        $content = <<<STR
        Exception {$e->getCode()}
        Message: 
        \t{$e->getMessage()}
        Trace:
        \t{$e->getTraceAsString()}
STR;

        parent::__construct($content, 500);
    }

    /**
     * @return string
     */
    protected function getContentType(): string
    {
        return 'text/plain';
    }
}