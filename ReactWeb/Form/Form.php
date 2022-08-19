<?php

declare(strict_types=1);

namespace ReactWeb\Form;

use ReactWeb\Form\Element\Input;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\Form\Validation\Result;
use ReactWeb\HTML\Attribute;
use ReactWeb\HTML\Element;
use ReactWeb\HTTP\Enum\Method;

/**
 * Form
 *
 * @package ReactWeb\Form
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class Form
{

    protected readonly Element $form;

    private bool $built = false;

    /** @var array<\ReactWeb\Form\AbstractInput> */
    private array $inputs = [];

    /**
     * @param string $name
     * @param \ReactWeb\HTTP\Enum\Method $method
     * @param string|null $action
     */
    public function __construct(string $name, public readonly Method $method, string $action = null)
    {
        $this->form = (new Element('form'))
            ->addAttribute(new Attribute('name', $name))
            ->addAttribute(new Attribute('method', $method->value));

        if ($action !== null) {
            $this->form->addAttribute(new Attribute('action', $action));
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setDefaultValue(string $key, mixed $value): self
    {
        $this->prepare();

        $this->inputs[$key]->setValue($value);

        return $this;
    }

    /**
     * @param array<string,mixed> $values
     * @return $this
     */
    public function setDefaultValues(array $values): self
    {
        $this->prepare();

        foreach ($values as $key => $value) {
            $this->setDefaultValue($key, $value);
        }

        return $this;
    }

    abstract protected function build(): void;

    /**
     * @param \ReactWeb\Form\AbstractInput $input
     * @return $this
     */
    public function add(AbstractInput $input): self
    {
        if (array_key_exists($input->name, $this->inputs)) {
            throw new \InvalidArgumentException(sprintf('AbstractInput with name %s already added', $input->name));
        }

        $this->inputs[$input->name] = $input;

        return $this;
    }

    /**
     * @param string $name
     * @return \ReactWeb\Form\AbstractInput|null
     */
    public function get(string $name): ?AbstractInput
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

    /**
     * @param array<string,mixed> $body
     * @param bool $fill
     * @return \ReactWeb\Form\Validation\Result
     */
    public function validate(array $body, bool $fill = true): Result
    {
        $this->prepare();

        $validationResult = new Result();

        foreach ($this->inputs as $input) {
            $input->setValue($body[$input->name]);

            foreach ($input->validate() as $failedValidation) {
                $validationResult->addErrorMessage($input, $failedValidation, $failedValidation->getErrorMessage($body[$input->name]));
            }
        }

        if ($fill) {
            foreach ($body as $key => $value) {
                $this->inputs[$key]->setValue($value);
            }
        }

        return $validationResult;
    }

    public function toHTML(bool $attributes = true, bool $children = true): string
    {
        $this->prepare();

        foreach ($this->inputs as $input) {
            foreach ($input->getElements() as $element) {
                $this->form->addElement($element);
            }
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
        $input->getElements()[0]->addAttribute(new Attribute('value', $text, true));

        $this->add($input);

        return $input->getElements()[0];
    }

    public function openTag(): string
    {
        return str_replace($this->closeTag(), '', $this->toHTML(true, false));
    }

    public function closeTag(): string
    {
        return '</form>';
    }

    public function getValues(): array
    {
        $result = [];

        foreach ($this->inputs as $input) {
            $result[$input->name] = $input->getValue();
        }

        return $result;
    }
}