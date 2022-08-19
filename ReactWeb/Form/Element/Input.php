<?php

declare(strict_types=1);

namespace ReactWeb\Form\Element;

use ReactWeb\Form\AbstractInput;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;

/**
 * Input
 *
 * @package ReactWeb\Form\Element
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class Input extends AbstractInput
{
    public function __construct(string $name, InputType $type, string $label = null)
    {
        $this->elements[] = (new Element('input', true))
            ->addAttribute(new Attribute('name', $name))
            ->addAttribute(new Attribute('type', $type->value));

        parent::__construct($name, $type, $label);
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        $attributeName = 'value';
        $attribute = $this->elements[0]->getAttribute($attributeName);
        if ($attribute !== null) {
            $attribute->setValue($value);
        } else {
            $this->elements[0]->addAttribute(new Attribute($attributeName, $value, true));
        }

        return $this;
    }
}