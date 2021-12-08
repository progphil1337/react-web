<?php

namespace ReactMvc\Config\Interpreter;

use Symfony\Component\Yaml\Yaml;

/**
 * YamlConfigInterpreter
 *
 * @package ReactMvc\Config\Interpreter
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class YamlConfigInterpreter implements ConfigInterpreter
{

    public function fromFile(string $file): array
    {
        return $this->parse(file_get_contents($file));
    }

    public function parse(string $content): array
    {
        // lets use symfonys component.. cuz we're lazy
        return Yaml::parse($content);
    }
}