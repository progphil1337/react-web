<?php

declare(strict_types=1);

namespace ReactWeb\Config\Interpreter;

/**
 * ConfigInterpreter
 *
 * @package ReactWeb\Config\Interpreter
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
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