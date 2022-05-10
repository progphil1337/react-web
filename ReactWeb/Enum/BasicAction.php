<?php

declare(strict_types=1);

namespace ReactWeb\Enum;

/**
 * BasicAction
 *
 * @package ReactWeb\Enum
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
enum BasicAction: int
{
    case SUCCESS = 0;
    case ERROR = 1;
}