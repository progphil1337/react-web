<?php

declare(strict_types=1);

namespace ReactWeb\Form\Element;

use ReactWeb\Form\AbstractInput;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;

/**
 * Radio
 *
 * @package ReactWeb\Form\Element
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Radio extends AbstractInput
{
    public function __construct(string $name, array $options)
    {
        foreach ($options as $value => $label) {

            $id = sprintf('%s_%s', $name, $value);

            $this->elements[] = (new Element('input', true))
                ->addAttribute(new Attribute('name', $name))
                ->addAttribute(new Attribute('value', $value))
                ->addAttribute(new Attribute('type', InputType::RADIO->value))
                ->addAttribute(new Attribute('id', $id));

            $this->elements[] = (new Element('label'))
                ->addAttribute(new Attribute('for', $id))
                ->innerText($label);
        }

        parent::__construct($name, InputType::RADIO);
    }

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