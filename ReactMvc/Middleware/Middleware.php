<?php

declare(strict_types=1);

namespace ReactMvc\Middleware;

use ReactMvc\Enum\BasicActionEnum;
use ReactMvc\Http\AbstractResponse;
use ReactMvc\Http\Request;

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

    public function createInstance(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    abstract public function evaluate(): BasicActionEnum|AbstractResponse;
}