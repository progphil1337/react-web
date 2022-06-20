<?php

declare(strict_types=1);

namespace ReactWeb\Form;

use ReactWeb\Form\Enum\InputType;
use ReactWeb\Form\Validation\Validator;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;

/**
 * Input
 *
 * @package ReactWeb\Form
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Input
{
    public readonly Element $element;
    /**
     * @var array<Validator>
     */
    private array $validators = [];

    public function __construct(public readonly string $name, private readonly InputType $type)
    {
        $this->element = (new Element('input', true))
            ->addAttribute(new Attribute('name', $name))
            ->addAttribute(new Attribute('type', $this->type->value));

        foreach ($this->type->getDefaultValidators() as $validator) {
            if ($validator instanceof Validator) {
                $this->addValidator($validator);
            } else if (is_string($validator)) {
                $this->addValidator(new $validator);
            }
        }
    }

    public function addValidator(Validator $validator): self
    {
        $this->validators[$validator->key] = $validator;

        if ($validator->getAttribute() !== null) {
            $this->element->addAttribute($validator->getAttribute());
        }

        return $this;
    }

    // @TODO: Add Error messages
    public function validate(mixed $data): array
    {
        $results = [];

        foreach ($this->validators as $validator) {
            if (!$validator->validate($data)) {
                $results[] = $validator;
            }
        }

        return $results;
    }
}