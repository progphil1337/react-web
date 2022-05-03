<?php

declare(strict_types=1);

namespace ReactMvc\Middleware;

use ReactMvc\Mvc\Http\Request;

/**
 * Middleware
 *
 * @package ReactMvc\Middleware
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class Middleware
{

    private readonly Request $request;

    private function setRequest(Request $request)
    {
        $this->request = $request;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    abstract public function run(): bool;
}