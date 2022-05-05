<?php

namespace ReactMvc\HTTP;


/**
 * Header
 *
 * @package ReactMvc\HTTP
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Header
{
    /**
     * @param array $data
     */
    public function __construct(
        private readonly array $data
    )
    {
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed
    {
        $data = $this->data[$name] ?? null;

        if ($data === null) {
            return null;
        }

        if (is_array($data) && count($data) === 1) {
            return $data[0];
        }

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

}