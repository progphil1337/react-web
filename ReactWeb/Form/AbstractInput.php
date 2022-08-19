<?php

declare(strict_types=1);

namespace ReactWeb\Form;

use ReactWeb\Form\Element\Input;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\Form\Validation\Validator;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;

/**
 * AbstractInput
 *
 * @package ReactWeb\Form
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class AbstractInput
{
    /** @var array<Element> */
    protected array $elements = [];
    public readonly ?Element $label;

    protected mixed $value;

    /**
     * @var array<Validator>
     */
    private array $validators = [];

    /**
     * @param string $name
     * @param \ReactWeb\Form\Enum\InputType $type
     * @param string|null $label
     */
    public function __construct(
        public readonly string       $name,
        protected readonly InputType $type,
        string                       $label = null
    )
    {
        if ($label !== null) {
            $this->label = (new Element('label'))
                ->addAttribute(new Attribute('for', $this->name))
                ->innerText($label);
        } else {
            $this->label = null;
        }

        foreach ($this->type->getDefaultValidators() as $validator) {
            if ($validator instanceof Validator) {
                $this->addValidator($validator);
            } else if (is_string($validator)) {
                $this->addValidator(new $validator);
            }
        }
    }

    /**
     * @param mixed $value
     * @return $this
     */
    abstract public function setValue(mixed $value): self;

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param \ReactWeb\Form\Validation\Validator $validator
     * @return $this
     */
    public function addValidator(Validator $validator): self
    {
        $this->validators[$validator->key] = $validator;

        if ($validator->getAttribute() !== null && $this instanceof Input) {
            $this->elements[0]->addAttribute($validator->getAttribute());
        }

        return $this;
    }

    public function validate(): array
    {
        $results = [];

        foreach ($this->validators as $validator) {
            if (!$validator->validate($this->value)) {
                $results[] = $validator;
            }
        }

        return $results;
    }

    public function getElements(): array
    {
        return $this->label !== null ? [
            $this->label,
            ...$this->elements
        ] : $this->elements;
    }
}