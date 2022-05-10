<?php

declare(strict_types=1);

namespace ReactWeb\HTTP\Enum;

/**
 * Method
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTION';
}