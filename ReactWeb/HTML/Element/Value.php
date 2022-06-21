<?php

declare(strict_types=1);

namespace ReactWeb\HTML\Element;

use ReactWeb\HTML\Element;

/**
 * TextElement
 *
 * @package ReactWeb\HTML\Element
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Value extends Element
{
    /**
     * @param string $value
     * @param bool $htmlspecialchars
     */
    public function __construct(private readonly string $value, private readonly bool $htmlspecialchars)
    {
        parent::__construct('');
    }

    /**
     * @param bool $withAttributes
     * @param bool $withChildren
     * @return string
     */
    public function toHTML(bool $withAttributes = true, bool $withChildren = true): string
    {
        return $this->htmlspecialchars ? htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') : $this->value;
    }
}