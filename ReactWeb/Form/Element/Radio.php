<?php

declare(strict_types=1);

namespace ReactWeb\Form\Element;

use ReactWeb\Form\AbstractInput;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\Form\Validation\Validator\InArray;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;

/**
 * Radio
 *
 * @package ReactWeb\Form\Element
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class Radio extends AbstractInput
{
    /**
     * @param string $name
     * @param array<string,mixed> $options
     */
    public function __construct(string $name, array $options, string $label = null)
    {
        foreach ($options as $value => $text) {

            $id = sprintf('%s_%s', $name, $value);

            $this->elements[] = (new Element('input', true))
                ->addAttribute(new Attribute('name', $name))
                ->addAttribute(new Attribute('value', $value))
                ->addAttribute(new Attribute('type', InputType::RADIO->value))
                ->addAttribute(new Attribute('id', $id));

            $this->elements[] = (new Element('label'))
                ->addAttribute(new Attribute('for', $id))
                ->innerText($text);
        }

        parent::__construct($name, InputType::RADIO, $label);

        $this->addValidator(new InArray(array_keys($options)));
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;

        foreach ($this->elements as $element) {
            $attribute = $element->getAttribute('value');
            if ($attribute !== null && $attribute->getValue() === $value) {
                $element->addAttribute(new Attribute('checked'));
            }
        }

        return $this;
    }
}