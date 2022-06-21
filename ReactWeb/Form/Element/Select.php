<?php

declare(strict_types=1);

namespace ReactWeb\Form\Element;

use ReactWeb\Form\AbstractInput;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;

/**
 * Select
 *
 * @package ReactWeb\Form\Element
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Select extends AbstractInput
{
    /** @var array<Element> */
    private array $optionElements = [];

    public function __construct(string $name, array $options, string $label = null)
    {
        $select = (new Element('select'))
            ->addAttribute(new Attribute('name', $name));

        foreach ($options as $value => $text) {
            $element = (new Element('option'))
                ->addAttribute(new Attribute('value', $value))
                ->innerText($text);

            $select->addElement($element);

            $this->optionElements[$value] = $element;
        }

        $this->elements[] = $select;

        parent::__construct($name, InputType::SELECT, $label);
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        foreach ($this->optionElements as $val => $element) {
            if ($val === $value) {
                $element->addAttribute(new Attribute('selected'));
            } else {
                $attr = $element->getAttribute('selected');
                if ($attr !== null) {
                    $element->removeAttribute($attr);
                }
            }
        }

        return $this;
    }
}