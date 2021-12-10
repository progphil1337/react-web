<?php

namespace ReactMvc\Logger;

/**
 * LogType
 *
 * @package ReactMvc
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
enum LogType: int
{
    case CONSOLE = 1;
    case FILE = 2;
    case BOTH = 3;
}