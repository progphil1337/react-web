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

    /**
     * @param string $file
     * @return array
     */
    public function fromFile(string $file): array
    {
        return $this->parse(file_get_contents($file));
    }

    /**
     * @param string $content
     * @return array
     */
    public function parse(string $content): array
    {
        // lets use symfonys component.. cuz we're lazy
        return Yaml::parse($content);
    }
}