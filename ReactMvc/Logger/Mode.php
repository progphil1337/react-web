<?php

declare(strict_types=1);

namespace ReactMvc\Logger;

/**
 * Mode
 *
 * @package ReactMvc\Logger
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
enum Mode: int
{
    case DEBUG = 0;
    case INFO = 1;
    case NOTICE = 2;
    case ERROR = 3;

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
        };
    }
}