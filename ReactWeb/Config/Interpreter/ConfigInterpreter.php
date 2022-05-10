<?php

namespace ReactWeb\Config\Interpreter;

/**
 * ConfigInterpreter
 *
 * @package ReactWeb\Config\Interpreter
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
interface ConfigInterpreter
{

    /**
     * @param string $file
     * @return array
     */
    public function fromFile(string $file): array;

    /**
     * @param string $content
     * @return array
     */
    public function parse(string $content): array;
}