<?php

declare(strict_types=1);

namespace ReactWeb\Logger;

/**
 * Mode
 *
 * @package ReactWeb\Logger
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
enum Mode: int
{
    case DEBUG = 0;
    case INFO = 1;
    case NOTICE = 2;
    case ERROR = 3;
    case DUMP = 4;

    /**
     * @return string
     */
    public function getText(): string
    {
        return match($this->value) {
            self::DEBUG->value => 'DEBUG',
            self::INFO->value => 'INFO',
            self::NOTICE->value => 'NOTICE',
            self::ERROR->value => 'ERROR',
            self::DUMP->value => 'DUMP'
        };
    }
}