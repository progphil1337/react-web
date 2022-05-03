<?php

declare(strict_types=1);

namespace ReactMvc\Session;

use ReactMvc\Middleware\Middleware;

/**
 * SessionMiddleware
 *
 * @package ReactMvc\Session
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class SessionMiddleware extends Middleware
{

    public function __construct(private readonly SessionManager $sessionManager) {

    }

    public function run(): bool
    {
        return true;
    }
}