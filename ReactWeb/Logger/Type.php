<?php

declare(strict_types=1);

namespace ReactWeb\Logger;

/**
 * Type
 *
 * @package ReactWeb
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
enum Type: int
{
    case CONSOLE = 1;
    case FILE = 2;
    case BOTH = 3;
}