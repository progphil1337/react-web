<?php

declare(strict_types=1);

namespace ReactWeb\HTML\Attribute;

use ReactWeb\HTML\Attribute;

/**
 * ArrayValueAttribute
 *
 * @package ReactWeb\HTML\Attribute
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class ArrayValueAttribute extends Attribute
{

    /**
     * @param string $name
     * @param array $list
     * @param string $separator
     * @param bool $htmlspecialchars
     * @param bool $keys
     * @param string $keyValueSeparator
     */
    public function __construct(
        string                  $name,
        private array           $list,
        private readonly string $separator = ';',
        bool                    $htmlspecialchars = false,
        private readonly bool   $keys = true,
        private readonly string $keyValueSeparator = ':')
    {
        parent::__construct($name, '', $htmlspecialchars);
    }

    /**
     * @param string $key
     * @param string|null $value
     * @return $this
     */
    public function set(string $key, string $value = null): self
    {
        if ($value === null && !$this->keys) {
            $this->list[] = $value;
        } else {
            $this->list[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove(string $key): self
    {
        if ($this->keys) {
            unset($this->list[$key]);
        } else {
            for ($i = 0; $i < count($this->list); $i++) {
                if ($this->list[$i] === $key) {
                    unset($this->list[$i]);
                }
            }

            $this->list = array_values($this->list); // reindex
        }

        return $this;
    }

    /**
     * @return string
     */
    public function toHTML(): string
    {
        if ($this->keys) {
            $value = array_map(fn(string $k, string $v): string => sprintf('%s%s%s', $k, $this->keyValueSeparator, $v), array_keys($this->list), array_values($this->list));
        } else {
            $value = $this->list;
        }

        $this->setValue(implode($this->separator, $value));

        return parent::toHTML();
    }
}