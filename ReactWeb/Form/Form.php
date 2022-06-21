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

    public function __construct(string $name, public readonly Method $method, string $action = null)
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

    public function get(string $name): ?Input
    {
        return $this->inputs[$name] ?? null;
    }

    public function prepare(): void
    {
        if (!$this->built) {
            $this->build();

            $this->built = true;
        }
    }

    // @TODO: Add Error messages
    public function validate(array $body, bool $fill = true): Result
    {
        $this->prepare();

        $validationResult = new Result();

        foreach ($this->inputs as $input) {
            foreach ($input->validate($body[$input->name]) as $failedValidation) {
                $validationResult->addErrorMessage($input, $failedValidation, $failedValidation->getErrorMessage($body[$input->name]));
            }
        }

        if ($fill) {
            foreach ($body as $key => $value) {
                $this->inputs[$key]->element->addAttribute(new Attribute('value', $value, true));
            }
        }

        return $validationResult;
    }

    public function toHTML(bool $attributes = true, bool $children = true): string
    {
        $this->prepare();

        foreach ($this->inputs as $input) {
            if ($input->label !== null) {
                $this->form->addElement($input->label);
            }

            $this->form->addElement($input->element);
        }

        return $this->form->toHTML($attributes, $children);
    }

    public function __toString(): string
    {
        return $this->toHTML();
    }

    protected function submitButton(string $text): Element
    {
        $input = new Input(InputType::SUBMIT->value, InputType::SUBMIT);
        $input->element->addAttribute(new Attribute('value', $text, true));

        $this->add($input);

        return $input->element;
    }

    public function openTag(): string
    {
        return str_replace($this->closeTag(), '', $this->toHTML(true, false));
    }

    public function closeTag(): string
    {
        return '</form>';
    }
}