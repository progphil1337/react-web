<?php

declare(strict_types=1);

namespace ReactWeb\HTML;

/**
 * Attribute
 *
 * @package ReactWeb\HTML
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Attribute
{
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $name
     * @param string $value
     * @param bool $htmlspecialchars
     */
    public function __construct(private readonly string $name, string $value, private bool $htmlspecialchars = true)
    {
        $this->setValue($value);
    }

    public function setValue(string $value): self
    {
        $this->value = $this->htmlspecialchars ? htmlspecialchars($value, ENT_COMPAT, 'UTF-8') : $value;

        return $this;
    }

    /**
     * @return string
     */
    public function toHTML(): string
    {
        return <<<HTML
{$this->name}="{$this->value}"
HTML;
    }

    public function __toString(): string
    {
        return $this->toHTML();
    }
}