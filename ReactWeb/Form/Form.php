<?php

declare(strict_types=1);

namespace ReactWeb\Form;

use ReactWeb\Form\Enum\InputType;
use ReactWeb\Form\Validation\Result;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;
use ReactWeb\HTTP\Enum\Method;

/**
 * Form
 *
 * @package ReactWeb\Form
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class Form
{

    protected readonly Element $form;

    private bool $built = false;

    /** @var array<\ReactWeb\Form\Input> */
    private array $inputs = [];

    public function __construct(string $name, Method $method, string $action = null)
    {
        $this->form = (new Element('form'))
            ->addAttribute(new Attribute('name', $name))
            ->addAttribute(new Attribute('method', $method->value));

        if ($action !== null) {
            $this->form->addAttribute(new Attribute('action', $action));
        }
    }

    abstract protected function build(): void;

    public function add(Input $input): self
    {
        if (array_key_exists($input->name, $this->inputs)) {
            throw new \InvalidArgumentException(sprintf('Input with name %s already added', $input->name));
        }

        $this->inputs[$input->name] = $input;

        return $this;
    }

    // @TODO: Add Error messages
    public function validate(array $body, bool $fill = true): Result
    {
        if (!$this->built) {
            $this->build();
            $this->built = true;
        }

        $validationResult = new Result();

        foreach ($this->inputs as $input) {
            foreach ($input->validate($body[$input->name]) as $failedValidation) {
                $validationResult->addErrorMessage($input, $failedValidation);
            }
        }

        if ($fill) {
            foreach ($body as $key => $value) {
                $this->inputs[$key]->element->addAttribute(new Attribute('value', $value, true));
            }
        }

        return $validationResult;
    }

    public function toHTML(): string
    {
        if (!$this->built) {
            $this->build();
            $this->built = true;
        }

        foreach ($this->inputs as $input) {
            $this->form->add($input->element);
        }

        return $this->form->toHTML();
    }

    public function __toString(): string
    {
        return $this->toHTML();
    }

    protected function submitButton(string $text): Element
    {
        $input = new Input($text, InputType::SUBMIT);

        $this->add(new Input($text, InputType::SUBMIT));

        return $input->element;
    }
}