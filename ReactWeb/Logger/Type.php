<?php

namespace ReactWeb\Logger;

/**
 * Type
 *
 * @package ReactWeb
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
enum Type: int
{
    case CONSOLE = 1;
    case FILE = 2;
    case BOTH = 3;
}